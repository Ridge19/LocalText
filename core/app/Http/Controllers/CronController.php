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

            $cronLog->end_at = $cron->last_run;
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

    public function sendSMS()
    {
        return $this->sms('send');
    }

    public function resendSMS()
    {
        return $this->sms('reSend');
    }

    protected function sms($scope)
    {
        try {
            $gs = gs();

            try {
                $smsQuery = Sms::where('sms_type', Status::SMS_TYPE_SEND)
                    ->whereIn('status', [
                        Status::SMS_INITIAL,
                        Status::SMS_SCHEDULED
                    ])
                    ->where('schedule', '<=', now()->format('Y-m-d H:i'))
                    ->orderBy('schedule', 'ASC')
                    ->with('device')
                    ->take(15);

                $sms = $smsQuery->get();

                foreach ($sms->groupBy('device_id') as $deviceId => $newSMS) {
                    event(new MessageSend([
                        'success'       => true,
                        'original_data' => [
                            'message' => $newSMS->toArray(),
                        ],
                        'device_id'     => @$newSMS->first()->device->device_id,
                    ]));
                }

                if ($scope === 'send') {
                    // mark event-triggered so they aren't re-queued
                    $smsQuery->update(['et' => Status::YES]);
                }

                $gs->cron_error_message = null;
            } catch (Exception $ex) {
                $gs->cron_error_message = $ex->getMessage();
            }

            $gs->last_cron = now();
            $gs->save();
        } catch (Throwable $th) {
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
                'username' => $user->username
            ]);
        }
    }
}
