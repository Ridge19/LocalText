<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Events\MessageSend;
use App\Lib\CurlRequest;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Sms;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Throwable;
use Illuminate\Http\JsonResponse;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())
                  ->where('is_running', Status::YES);
        }

        $crons = $crons->get();

        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();

            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }

            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at   = $cron->last_run;
            $cronLog->duration = Carbon::parse($cronLog->start_at)
                                       ->diffInSeconds(Carbon::parse($cronLog->end_at));
            $cronLog->save();
        }

        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }

        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function sendSMS(): JsonResponse
    {
        return $this->sms('send');
    }

    public function resendSMS(): JsonResponse
    {
        return $this->sms('reSend');
    }

    /**
     * Common SMS processing for send + re-send.
     *
     * @param  string  $scope  'send' or 'reSend'
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    protected function sms(string $scope): JsonResponse
    {
        $processed = 0;
        $smsList   = collect();

        try {
            $gs = gs();

            try {
                // pick up pending SMS (not yet sent, not permanently failed)
                $smsQuery = Sms::where('sms_type', Status::SMS_TYPE_SEND)
                    ->whereIn('status', [
                        Status::SMS_INITIAL,
                        Status::SMS_SCHEDULED,
                    ])
                    ->where('permanent_fail', false)
                    ->where('schedule', '<=', now()->format('Y-m-d H:i'))
                    ->orderBy('schedule', 'ASC')
                    ->with('device')
                    ->take(15);

                $smsList = $smsQuery->get();

                // group by device for batch dispatch
                foreach ($smsList->groupBy('device_id') as $deviceId => $group) {
                    $device = $group->first()->device;

                    // Build payload with opt-out appended
                    $payload = $group->map(function ($sms) {
                        $data = $sms->toArray();
                        $data['message'] = trim($data['message']) . ' Reply STOP to unsubscribe';
                        return $data;
                    });

                    event(new MessageSend([
                        'success'       => true,
                        'original_data' => [
                            'message' => $payload->toArray(),
                        ],
                        'device_id'     => $device->device_id ?? null,
                    ]));

                    // mark each SMS as sent
                    $group->each->update([
                        'status'          => Status::SMS_SENT,
                        'sent_at'         => now(),
                        'et'              => Status::YES,
                        'failed_attempts' => 0,
                        'error_msg'       => null,
                    ]);

                    $processed += $group->count();
                }

                // on initial send, mark them as event-triggered to avoid re-queuing
                if ($scope === 'send') {
                    $smsQuery->update(['et' => Status::YES]);
                }

                $gs->cron_error_message = null;
            } catch (Exception $ex) {
                // on any exception, increment failure counter and handle permanent failures
                foreach ($smsList as $sms) {
                    $sms->increment('failed_attempts');
                    if ($sms->failed_attempts >= 3) {
                        $sms->update(['permanent_fail' => true]);
                    }
                }
                $gs->cron_error_message = $ex->getMessage();
            }

            $gs->last_cron = now();
            $gs->save();

            return response()->json([
                'success'   => true,
                'scope'     => $scope,
                'processed' => $processed,
            ], 200);

        } catch (Throwable $th) {
            // escalate any unexpected errors
            throw new Exception($th->getMessage());
        }
    }

    public function checkExpiredPlan()
    {
        $users = User::whereNotNull('plan_id')
                     ->where('plan_expires_at', '<', Carbon::now())
                     ->get();

        foreach ($users as $user) {
            $user->available_sms           = 0;
            $user->available_device_limit  = 0;
            $user->available_group_limit   = 0;
            $user->available_contact_limit = 0;
            $user->daily_sms_limit         = 0;
            $user->plan_campaign_available = 0;
            $user->plan_api_available      = 0;
            $user->plan_scheduled_sms      = 0;
            $user->save();

            notify($user, 'PLAN_EXPIRED', [
                'username' => $user->username,
            ]);
        }
    }
}
