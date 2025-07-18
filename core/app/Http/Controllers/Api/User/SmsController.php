<?php

namespace App\Http\Controllers\Api\User;

use App\Constants\Status;
use App\Events\MessageReceived;
use App\Events\MessageSend;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Device;
use App\Models\Sms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    public function send(Request $request)
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return apiResponse(false, 422, "Unprocessable Entity", $validator->errors()->all());
        }

        $device = Device::where('device_id', $request->device)->first();

        if (!$device->status) {
            return apiResponse(false, 424, null, ['Device not connected']);
        }

        $slotNumber = $request->device_sim && $request->device_sim == 2 ? 1 : 0;
        $deviceSlot = @$device->sim[$slotNumber];

        if (gettype($deviceSlot) != 'array') {
            return apiResponse(false, 424, null, ['Error found for device SIM slot']);
        }

        if ($request->date) {
            $schedule     = now()->parse($request->date)->format("Y-m-d H:i");
            $eventTrigger = Status::NO;
            $status       = Status::SMS_SCHEDULED;
        } else {
            $eventTrigger = Status::YES;
            $schedule     = now()->format("Y-m-d H:i");
            $status       = Status::SMS_INITIAL;
        }

        $apiKey = ApiKey::where('key', $request->header()['apikey'][0])
                        ->with('user')
                        ->first();

        if (!$apiKey) {
            return apiResponse(false, 401, null, ['Invalid API Key']);
        }

        $user = $apiKey->user;

        if (!$user) {
            return apiResponse(false, 404, null, ['Invalid API Key']);
        }

        if (!$user->activePlan()) {
            return apiResponse(false, 406, null, ['Not acceptable']);
        }

        if (!$user->plan_api_available) {
            return apiResponse(false, 406, null, ['Api is not available']);
        }

        $todaysSendedSms = Sms::where('user_id', $user->id)
                              ->whereDate('created_at', Carbon::today())
                              ->count();

        if (!$user->hasLimit('daily_sms_limit', $todaysSendedSms)) {
            return apiResponse(false, 406, null, ['You have reached the daily sms limit']);
        }

        $numbers = explode(',', $request->mobile_number);
        $numbers = array_unique(array_filter($numbers));

        // Normalize each number to ensure a leading "+" for E.164 formatting
        $numbers = array_map(function($num) {
            $num = trim($num);
            if (strpos($num, '+') !== 0) {
                $num = '+' . $num;
            }
            return $num;
        }, $numbers);

        // check availability
        if (!$user->hasLimit('available_sms', count($numbers))) {
            return apiResponse(false, 406, null, ['Can\'t send sms. The maximum number of sms limit reached']);
        }

        $batch    = createBatch($user->id);
        $messages = [];

        foreach ($numbers as $number) {
            Log::info("▶︎ Sending SMS to {$number} on device {$device->device_id}");

            $sms                     = new Sms();
            $sms->device_id          = $device->id;
            $sms->user_id            = $user->id;
            $sms->device_slot_number = $deviceSlot["slot"];
            $sms->device_slot_name   = $deviceSlot["name"];
            $sms->mobile_number      = $number;
            $sms->message            = strip_tags($request->message);
            $sms->schedule           = $schedule;
            $sms->batch_id           = $batch->id;
            $sms->status             = $status;
            $sms->et                 = $eventTrigger;
            $sms->api_key_id         = $apiKey->id;
            $sms->save();

            $messages[] = $sms;
        }

        $user->subtractLimitCounter('available_sms', count($messages));

        if ($eventTrigger) {
            event(new MessageSend([
                'success'       => true,
                'device_id'     => $device->device_id,
                'original_data'=> [
                    'message' => $messages,
                ],
            ]));
        }

        return apiResponse(true, 200, count($numbers) . ' sms should be send');
    }

    public function sendViaGet(Request $request)
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return apiResponse(false, 422, "Unprocessable Entity", $validator->errors()->all());
        }

        $device = Device::where('status', Status::ENABLE)
                        ->where('device_id', $request->device)
                        ->first();

        if (!$device || ! $device->status) {
            return apiResponse(false, 424, null, ['Device not connected']);
        }

        // Pick the right SIM slot based on request
        $slotNumber = ($request->device_sim && $request->device_sim == 2) ? 1 : 0;
        $deviceSlot = @$device->sim[$slotNumber];

        if (gettype($deviceSlot) != 'array') {
            return apiResponse(false, 424, null, ['Error found for device SIM slot']);
        }

        $batch  = createBatch();
        $now    = now();
        $apiKey = ApiKey::belongsToUser()
                        ->where('status', Status::ENABLE)
                        ->where('key', $request->apikey)
                        ->first();

        $sms                     = new Sms();
        $sms->device_id          = $device->id;
        $sms->user_id            = auth()->id();
        $sms->device_slot_number = $deviceSlot['slot'];
        $sms->device_slot_name   = $deviceSlot['name'];

        // Normalize and log the number
        $number = trim($request->mobile_number);
        if (strpos($number, '+') !== 0) {
            $number = '+' . $number;
        }
        Log::info("▶︎ Sending SMS (via GET) to {$number} on device {$device->device_id}");
        $sms->mobile_number = $number;

        $sms->message   = strip_tags($request->message);
        $sms->schedule  = $now;
        $sms->batch_id  = $batch->id;
        $sms->api_key_id= $apiKey->id;
        $sms->save();

        event(new MessageSend([
            'success'       => true,
            'device_id'     => $sms->device->device_id,
            'original_data'=> [
                'message' => $sms->toArray(),
            ],
        ]));

        return apiResponse(true, 200, '1 sms should be send');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:1,2,3,4,9'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 422, "Unprocessable entity", $validator->errors()->all());
        }

        $sms = Sms::where('id', $id)->first();

        if (!$sms) {
            return apiResponse(false, 406, null, ["Sms not found"]);
        }

        $sms->status     = $request->status;
        $sms->error_code = $request->error_code ?? 0;
        $sms->save();

        return apiResponse(true, 200, "SMS updated successfully");
    }

    public function received(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id'          => 'required',
            'device_slot_number' => 'required|integer|in:1,2',
            'device_slot_name'   => 'required',
            'mobile_number'      => 'required',
            'message'            => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 422, "Unprocessable entity", $validator->errors()->all());
        }

        $device = Device::where('device_id', $request->device_id)->first();

        if (!$device) {
            return apiResponse(false, 406, null, ["Device Not Found"]);
        }

        $sms                     = new Sms();
        $sms->device_id          = $device->id;
        $sms->user_id            = auth()->id();
        $sms->device_slot_number = $request->device_slot_number;
        $sms->device_slot_name   = $request->device_slot_name;
        $sms->mobile_number      = $request->mobile_number;
        $sms->schedule           = now()->format('Y-m-d H:i');
        $sms->message            = $request->message;
        $sms->status             = Status::SMS_DELIVERED;
        $sms->sms_type           = Status::SMS_TYPE_RECEIVED;
        $sms->save();

        $sms->device_name = $device->device_name . '-' . $device->device_model;

        $data['message'] = $sms;
        $response        = apiResponse(true, 200, "Sms Received Successfully", [], $data);

        event(new MessageReceived($response));

        return $response;
    }

    protected function validator($request)
    {
        return Validator::make($request->all(), [
            'message'       => 'required|string|max:160',
            'date'          => "nullable|date|date_format:Y-m-d h:i a|after_or_equal:today",
            'device'        => 'required|exists:devices,device_id',
            'mobile_number' => 'required',
            'device_sim'    => 'nullable|in:1,2',
        ], [
            "date.after_or_equal" => "The date must be today or future date",
            "date.date_format"    => "The date format invalid",
            "message.max"         => "The message 160 character allowed",
        ]);
    }
}
