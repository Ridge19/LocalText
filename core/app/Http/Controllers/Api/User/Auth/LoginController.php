<?php

namespace App\Http\Controllers\Api\User\Auth;

use Exception;
use App\Models\User;
use App\Models\Device;
use App\Constants\Status;
use App\Events\DeviceAdd;
use App\Events\DeviceLogOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function addDevice(Request $request)
    {
        $validator    = Validator::make($request->all(), [
            'scan_data'       => 'required|string',
            'device_id'       => 'required',
            'device_name'     => 'required',
            'device_model'    => 'required',
            'android_version' => 'required',
            'app_version'     => 'required',
            'sim'             => 'required',
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 422, "Unprocessable Entity", $validator->errors()->all());
        }


        $scanData = explode("HOST", $request->scan_data);
        try {
            $userId = decrypt(@$scanData[0]);
            $user   = User::find($userId);

            if (!$user) {
                return   apiResponse(false, 406, null, ['User not found']);
            }

            if ($user->id != auth()->id()) {
                return   apiResponse(false, 401, null, ['Invalid QR code']);
            }

            $device = Device::where('user_id', $user->id)
                ->where('device_id', $request->device_id)
                ->first();

            if (!$device) {
                if (!$user->hasLimit('available_device_limit')) {
                    return apiResponse(false, 406, null, ["You have reached your device limit"]);
                }

                $device                  = new Device();
                $device->user_id         = $user->id;
                $device->device_id       = $request->device_id;
                $device->device_name     = $request->device_name;
                $device->device_model    = $request->device_model;
                $device->android_version = $request->android_version;
                $device->app_version     = $request->app_version;

                $user->subtractLimitCounter('available_device_limit');
            }
            $allSim = explode(',', $request->sim);
            foreach ($allSim as $k => $sim) {
                $simData[]    = [
                    'slot' => $k + 1,
                    'name' => @$sim,
                ];
            }
            $device->sim    = $simData;
            $device->status = Status::ENABLE;
            $device->save();

            event(new DeviceAdd($device));

            $data  = $this->response();

            return apiResponse(true, 200, "Login Successfully", null, $data);
        } catch (Exception $ex) {
            return  apiResponse(false, 500, null, [$ex->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $validator    = Validator::make($request->all(), [
            'device_id' => 'required|exists:devices,device_id'
        ]);

        if ($validator->fails()) {
            return apiResponse(false, 422, "Unprocessable Entity", $validator->errors()->all());
        }

        //disconnected device
        $device = Device::where('device_id', $request->device_id)->first();
        $device->status = Status::DISABLE;
        $device->save();

        DB::table('personal_access_tokens')->where('device_id', $device->device_id)->delete();
        event(new DeviceLogOut($device->device_id));
        return  apiResponse(true, 200, "Logout Successful");
    }

    protected function response()
    {
        $user = auth()->user();
        $data  = [
            'user'         => $user,
            'access_token' => $user->createToken('auth_token', [request()->device_id])->plainTextToken,
            'token_type'   => 'Bearer',
            'base_url'     => route('home'),
            'pusher'       => [
                'pusher_key'     => config('app.PUSHER_APP_KEY'),
                'pusher_id'      => config('app.PUSHER_APP_ID'),
                'pusher_secret'  => config('app.PUSHER_APP_SECRET'),
                'pusher_cluster' => config('app.PUSHER_APP_CLUSTER'),
            ]
        ];
        return $data;
    }
}
