<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Device';
        $allDevice = Device::with('user')
            ->searchable(['device_name', 'device_id'])
            ->filter(['status', 'user:username'])
            ->orderBy('id', 'DESC')
            ->paginate(getPaginate());
        $columns   = Device::getColumNames();

        return view('admin.device.index', compact('pageTitle', 'allDevice', 'columns'));
    }
}
