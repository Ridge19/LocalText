<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Events\MessageSend;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Device;
use App\Models\Sms;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    public function index()
    {
        $pageTitle = 'All SMS';
        $messages  = Sms::belongsToUser()
            ->with('device', 'failReason')
            ->filter(['mobile_number', 'sms_type', 'status', 'device_id'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->paginate(getPaginate(10));

        $allDevice = Device::belongsToUser()->orderBy('id', 'DESC')->get();

        return view('Template::user.sms.index', compact('pageTitle', 'messages', 'allDevice'));
    }

    public function send()
    {
        $pageTitle    = 'Send SMS';

        if (request()->dial_code && request()->mobile) {
            $contact = Contact::belongsToUser()->where('dial_code', request()->dial_code)->where('mobile', request()->mobile)->first();

            if ($contact) {
                $pageTitle = 'Send SMS to:  +' . $contact->dial_code . $contact->mobile;
            } else {
                abort(404);
            }
        }

        $hasDevice    = Device::belongsToUser()->exists();
        $allDevice    = Device::belongsToUser()->connected()->orderBy('id', 'DESC')->get();
        $templates    = Template::belongsToUser()->active()->orderBy('id', 'DESC')->get();
        $info         = json_decode(json_encode(getIpInfo()), true);
        $mobileCode   = @implode(',', $info['code']);
        $countries    = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.sms.send', compact('pageTitle', 'templates', 'allDevice', 'mobileCode', 'countries', 'hasDevice'));
    }

    public function sendSMS(Request  $request)
    {
        $validator = $this->validation($request);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()->all()
            ]);
        }

        $user = auth()->user();

        if ($request->date) {
            if ($user->plan_scheduled_sms != Status::YES) {
                return apiResponse(false, 429, null, ['Can\'t send scheduled sms. Please upgrade your plan']);
            }
            $schedule      = Carbon::parse($request->date)->format("Y-m-d H:i");
            $eventTrigger  = Status::NO;
            $status        = Status::SMS_SCHEDULED;
            $notifyMessage = "SMS will be sent on $schedule";
        } else {
            $eventTrigger = Status::YES;
            $schedule     = Carbon::now()->format("Y-m-d H:i");
            $status       = Status::SMS_INITIAL;
            $notifyMessage = "A Sms should be send";
        }

        $device = Device::where('user_id', $user->id)->where('id', $request->device)->first();

        if (!$device) {
            return apiResponse(false, 404, null, ['Device not found']);
        }

        if (!array_key_exists($request->sim, $device->sim)) {
            return apiResponse(false, 404, null, ['Device slot not found']);
        }

        $deviceSlot = $device->sim[$request->sim];

        $contact  = Contact::belongsToUser()
            ->where('dial_code', $request->mobile_code)
            ->where('mobile', $request->mobile)
            ->first();

        if (!$contact) {
            if (!$user->hasLimit('available_contact_limit')) {
                return apiResponse(false, 429, null, ['Can\'t add new contact. The maximum number of contact limit reached']);
            }

            $contact = new Contact();
            $contact->user_id     = $user->id;
            $contact->dial_code   = $request->mobile_code;
            $contact->mobile      = $request->mobile;
            $contact->save();

            $mobileNumber = $contact->dial_code . $contact->mobile;
        } else {
            $mobileNumber = $contact->dial_code . $contact->mobile;
        }

        if (!$user->hasLimit('available_sms')) {
            return apiResponse(false, 429, null, ['Can\'t send sms. The maximum number of sms limit reached']);
        }

        $todaysSendedSms = Sms::belongsToUser()->whereDate('created_at', Carbon::today())->count();

        if (!$user->hasLimit('daily_sms_limit', $todaysSendedSms)) {
            return apiResponse(false, 429, null, ['You have reached the daily sms limit']);
        }

        $batch         = createBatch();
        $message       = strip_tags($request->message);

        $sms                     = new Sms();
        $sms->device_id          = $device->id;
        $sms->user_id            = $user->id;
        $sms->device_slot_number = $deviceSlot['slot'];
        $sms->device_slot_name   = $deviceSlot['name'];
        $sms->mobile_number      = $mobileNumber;
        $sms->message            = $message;
        $sms->schedule           = $schedule;
        $sms->batch_id           = $batch->id;
        $sms->status             = $status;
        $sms->et                 = $eventTrigger;
        $sms->save();

        $messages[] = $sms;

        $user->subtractLimitCounter('available_sms');

        if ($eventTrigger) {
            event(new MessageSend([
                'success'       => true,
                'device_id'     => $device->device_id,
                "original_data" => [
                    'message' => $messages,
                ]
            ]));
        }

        return response()->json([
            'success'  => true,
            "messages" => $messages,
            'message'  => $notifyMessage
        ]);
    }

    protected function validation($request)
    {
        $validator = Validator::make($request->all(), [
            'message'      => 'required|string',
            'schedule'     => 'required|in:1,2', //schedule 1=now,2=future date
            'date'         => "required_if:schedule,==,2|nullable|date|date_format:Y-m-d h:i a|after_or_equal:today",
            'sim'          => 'required',
            'device'       => 'required|integer|exists:devices,id',
            'mobile_code'  => 'required',
            'mobile'       => 'required',
        ], [
            "date.required_if"    => "The date filed is required",
            "date.after_or_equal" => "The date must be today or future date",
            "date.date_format"    => "The date format invalid",
        ]);

        return $validator;
    }

    public function reSend($id)
    {
        $sms = Sms::with("device")
            ->belongsToUser()
            ->where('sms_type', Status::SMS_TYPE_SEND)
            ->where('et', Status::YES)
            ->whereIn('Status', [Status::SMS_INITIAL, Status::SMS_FAILED])
            ->findOrFail($id);

        $messages[] = $sms;

        event(new MessageSend([
            'success'       => true,
            'device_id'     => $sms->device->device_id,
            "original_data" => [
                'message' => $messages,
            ]
        ]));

        $notify[] = ['success', "Sms resend successfully"];
        return back()->withNotify($notify);
    }
}
