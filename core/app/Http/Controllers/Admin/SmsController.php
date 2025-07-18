<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Sms;

class SmsController extends Controller
{
    public function index()
    {
        $pageTitle = 'All SMS';
        $messages  = Sms::with(['device', 'failReason', 'user'])
            ->searchable(['mobile_number', 'device:device_name', 'user:username'])
            ->filter(['sms_type', 'status'])
            ->dateFilter()
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        $allDevice = Device::orderBy('id', 'DESC')->get();

        return view('admin.sms.index', compact('pageTitle', 'messages', 'allDevice'));
    }
}
