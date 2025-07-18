<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;

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
}
