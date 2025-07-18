<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Events\MessageSend;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Device;
use App\Models\Group;
use App\Models\GroupContact;
use App\Models\Sms;
use App\Models\Template;
use App\Rules\FileTypeValidate;
use Exception;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $pageTitle = 'Campaign History';
        $campaigns = Campaign::belongsToUser()->latest('id')->paginate(getPaginate());

        return view('Template::user.campaign.index', compact('campaigns', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle      = 'Create Campaign';
        $allDevice      = Device::belongsToUser()->orderBy('id', 'DESC')->get();
        $groups         = Group::belongsToUser()->active()->whereHas('contact')->orderBy('id', 'DESC')->get();
        $templates      = Template::belongsToUser()->active()->orderBy('id', 'DESC')->get();
        $contactExists  = Contact::belongsToUser()->active()->exists();
        $info           = json_decode(json_encode(getIpInfo()), true);
        $mobileCode     = @implode(',', $info['code']);
        $countries      = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.campaign.create', compact('pageTitle', 'allDevice', 'templates', 'groups', 'contactExists', 'mobileCode', 'countries'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'title'          => 'required',
            'device'         => 'required|integer',
            'sim'            => 'required',
            'schedule'       => 'required|in:1,2', // schedule 1=now,2=future date
            'date'           => 'nullable|required_if:schedule,2|date|date_format:Y-m-d H:i|after_or_equal:today',
            'selection_type' => 'required|in:1,2,3,4',
            'contact_list'   => 'nullable|array',
            'group'          => 'nullable|array',
            'mobile_numbers' => 'nullable|required_if:selection_type,3',
            'mobile_code'    => 'nullable|required_if:selection_type,3',
            'file'           => ['nullable', 'required_if:selection_type,4', new FileTypeValidate(['csv', 'xlsx', 'txt'])],
            'message'        => 'required|string',
        ]);


        if($request->selection_type == Status::CONTACT && (!$request->contact_list && !$request->select_all_contact)){
            $notify[] = ['error', 'The contact list field is required'];
            return back()->withNotify($notify)->withInput();
        }

        if($request->selection_type == Status::GROUP && (!$request->group && !$request->select_all_group)){
            $notify[] = ['error', 'The group field is required'];
            return back()->withNotify($notify)->withInput();
        }

        $user = auth()->user();
        $numbers = [];

        if ($request->selection_type == Status::CONTACT) {
            if ($request->select_all_contact) {
                $numbers = Contact::where('status', Status::ENABLE)
                    ->select('dial_code', 'mobile')
                    ->get()
                    ->map(fn($contact) => $contact->dial_code . $contact->mobile)
                    ->toArray();
            } elseif ($request->contact_list) {
                $numbers = Contact::where('status', Status::ENABLE)
                    ->whereIn('id', $request->contact_list ?? [])
                    ->select('dial_code', 'mobile')
                    ->get()
                    ->map(fn($contact) => $contact->dial_code . $contact->mobile)
                    ->toArray();
            }
        } elseif ($request->selection_type == Status::GROUP) {

            if ($request->select_all_group) {
                $numbers = GroupContact::belongsToUser()
                    ->whereHas('contact')
                    ->with(['contact' => function ($query) {
                        $query->where('status', Status::ENABLE);
                    }])
                    ->get()
                    ->map(fn($groupContact) => $groupContact->contact->dial_code . $groupContact->contact->mobile)
                    ->toArray();
            } elseif ($request->group) {
                $numbers = GroupContact::belongsToUser()
                    ->whereHas('contact')
                    ->whereIn('group_id', $request->group ?? [])
                    ->with(['contact' => function ($query) {
                        $query->where('status', Status::ENABLE);
                    }])
                    ->get()
                    ->map(fn($groupContact) => $groupContact->contact->dial_code . $groupContact->contact->mobile)
                    ->toArray();
            }
        } elseif($request->selection_type == Status::DIRECT_INPUT) {

            $mobileNumbers = explode("\r\n", trim($request->mobile_numbers));
            $numbers = array_map(fn($number) => $request->mobile_code . $number, $mobileNumbers);
        }elseif($request->selection_type == Status::DIRECT_INPUT_FROM_FILE){
            if ($request->hasFile('file')) {
                $columnNames = ['firstname', 'lastname', 'email', 'dial_code', 'mobile', 'country', 'city', 'state', 'zip'];
                $notify      = [];
                try {
                    $import = importFileReader($request->file, $columnNames, $columnNames);
                    $readData = $import->getReadData();

                    $numbers = array_map(fn($row) => $row[3] . $row[4], $readData);
                } catch (Exception $ex) {
                    $notify[] = ['error', $ex->getMessage()];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }

        $numbers = array_map('trim', $numbers);
        $numbers = array_unique(array_filter($numbers));

        if (count($numbers) <= 0) {
            $notify[] = ['error', 'At least one mobile number required'];
            return back()->withNotify($notify)->withInput();
        }

        if (!$user->hasLimit('available_sms', count($numbers))) {
            $notify[] = ['error', 'Can\'t send sms. The maximum number of sms limit reached'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->date) {
            $schedule     = now()->parse($request->date)->format("Y-m-d H:i");
            $eventTrigger = Status::NO;
            $status       = Status::CAMPAIGN_PENDING;
        } else {
            $eventTrigger = Status::YES;
            $schedule     = now()->format("Y-m-d H:i");
            $status       = Status::CAMPAIGN_INITIAL;
        }

        $device = Device::belongsToUser()->find($request->device);

        if (!$device) {
            $notify[] = ['error', 'Device not found'];
            return back()->withNotify($notify)->withInput();
        }

        if (!$device->status) {
            $notify[] = ['error', 'The device is not connected'];
            return back()->withNotify($notify)->withInput();
        }

        if (!array_key_exists($request->sim, $device->sim)) {
            $notify[] = ['error', 'Device slot not found'];
            return back()->withNotify($notify)->withInput();
        }

        $deviceSlot = $device->sim[$request->sim];

        $campaign             = new Campaign();
        $campaign->user_id    = $user->id;
        $campaign->title      = $request->title;
        $campaign->schedule   = $schedule;
        $campaign->et         = $eventTrigger;
        $campaign->status     = $status;
        $campaign->message    = $request->message;
        $campaign->save();

        $batch = createBatch();

        if ($request->date) {
            $schedule     = now()->parse($request->date)->format("Y-m-d H:i");
            $eventTrigger = Status::NO;
            $status       = Status::SMS_SCHEDULED;
        } else {
            $eventTrigger = Status::YES;
            $schedule     = now()->format("Y-m-d H:i");
            $status       = Status::SMS_INITIAL;
        }

        $messages = [];
        foreach ($numbers as $number) {
            $sms                     = new Sms();
            $sms->device_id          = $device->id;
            $sms->user_id            = $campaign->user->id;
            $sms->campaign_id        = $campaign->id;
            $sms->device_slot_number = $deviceSlot['slot'];
            $sms->device_slot_name   = $deviceSlot['name'];
            $sms->mobile_number      = $number;
            $sms->message            = $campaign->message;
            $sms->schedule           = $schedule;
            $sms->batch_id           = $batch->id;
            $sms->status             = $status;
            $sms->et                 = $eventTrigger;
            $sms->save();

            $messages[] = $sms;
        }

        $user->subtractLimitCounter('available_sms', count($messages));

        if ($eventTrigger) {
            event(new MessageSend([
                'success'       => true,
                'device_id'     => $device->device_id,
                "original_data" => [
                    'message' => $messages,
                ]
            ]));
        }

        $notify[] = ['success', 'Campaign created successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        Campaign::belongsToUser()->findOrFail($id);
        return Campaign::changeStatus($id);
    }
}
