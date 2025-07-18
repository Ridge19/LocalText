<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Device;
use App\Models\DeviceToken;
use App\Models\PurchasePlan;
use App\Models\Sms;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function home()
    {
        $user = auth()->user();

        // Sms
        $smsQuery = Sms::query()->belongsToUser();
        $widget['sms']['sent']          = (clone $smsQuery)->delivered()->count();
        $widget['sms']['scheduled']     = (clone $smsQuery)->scheduled()->count();
        $widget['sms']['initiated']     = (clone $smsQuery)->initiated()->count();
        $widget['sms']['failed']        = (clone $smsQuery)->failed()->count();

        $pageTitle              =   'Dashboard';
        $sms                    =   Sms::belongsToUser()->with('device')->latest('id')->take(5)->get();
        $activePlan             =   $user->activePlan();

        $widget['total_device'] = Device::belongsToUser()->count();
        $widget['connected_device'] = Device::belongsToUser()->connected()->count();
        $widget['disconnected_device'] = Device::belongsToUser()->disconnected()->count();

        return view('Template::user.dashboard', compact('pageTitle', 'widget', 'user', 'sms', 'activePlan'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';

        // Deposit
        $deposits = Deposit::belongsToUser();
        $widget['deposit']['successful'] = (clone $deposits)->successful()->sum('amount');
        $widget['deposit']['pending']    = (clone $deposits)->pending()->sum('amount');
        $widget['deposit']['rejected']   = (clone $deposits)->rejected()->sum('amount');
        $widget['deposit']['charge']   = (clone $deposits)->successful()->sum('charge');

        $deposits  = auth()->user()->deposits()->searchable(['trx'])->filter(['status'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());


        return view('Template::user.deposit_history', compact('pageTitle', 'deposits', 'widget'));
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function purchasedPlans(){
        $pageTitle     = 'Purchase History';
        $purchasePlans = PurchasePlan::belongsToUser()->orderBy('id', 'DESC')->with('gateway', 'plan')->paginate(getPaginate());
        return view('Template::user.plan.index', compact('pageTitle', 'purchasePlans'));
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => [
                'required',
                'regex:/^([0-9]*)$/',
                Rule::unique('users')->where('dial_code', $request->mobile_code),
            ],
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space, or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code     = $request->country_code;
        $user->mobile           = $request->mobile;
        $user->username         = $request->username;
        $user->address          = $request->address;
        $user->city             = $request->city;
        $user->state            = $request->state;
        $user->zip              = $request->zip;
        $user->country_name     = $request->country ?? null;
        $user->dial_code        = $request->mobile_code;
        $user->profile_complete = Status::YES;

        $user->save();

        return to_route('user.home');
    }

    public function addDeviceToken(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '-attachments.' . $extension;

        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exist'];
            return back()->withNotify($notify);
        }

        header('Content-Disposition: attachment; filename="' . $title . '"');
        header('Content-Type: ' . $mimetype);

        return readfile($filePath);
    }

    public function downloadApk(){
        $pageTitle = 'Download APK';
        return view('Template::user.apk', compact('pageTitle'));
    }
}
