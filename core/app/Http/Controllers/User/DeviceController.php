<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Events\DeviceLogOut;

class DeviceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pageTitle = 'Manage Device';
        $allDevice = Device::belongsToUser()
            ->searchable(['device_name', 'device_id'])
            ->filter(['status'])
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());

        $userId       = encrypt($user->id);
        $rootUrl      = route('home');
        $qrCodeImgSrc = cryptoQR($userId . "HOST" . $rootUrl);

        return view('Template::user.device.index', compact('pageTitle', 'allDevice', 'qrCodeImgSrc'));
    }

    public function disconnect($id)
    {
        $device = Device::belongsToUser()->where('id', $id)->firstOrFail();
        
        // Update device status to disconnected
        $device->status = 0;
        $device->save();
        
        // Broadcast device logout event
        broadcast(new DeviceLogOut($device->device_id));
        
        $notify[] = ['success', 'Device disconnected successfully'];
        return back()->withNotify($notify);
    }
}
