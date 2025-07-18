@extends('Template::layouts.master')
@section('content')
    <div class="card custom--card">
        <div class="card-body">
            <p>@lang('To run your system, you must connect your device to it. To do this, you need to download the APK.')</p>
            @lang('Download the ') {{ gs('site_name') }} @lang('APK file from')
            <a class="text-primary" href="{{ apkUrl() }}" download>
                @lang('here') <i class="las la-download"></i>
            </a>
            <div class="mt-3">
                <h6 class="mb-1">@lang('How the APK Works')</h6>
                <p>@lang('1. Log in to the application.')</p>
                <p>@lang('2. Navigate to "Manage Device" → "Add Device" and scan the QR code.')</p>
                <p>@lang('3. Your device will be automatically connected to the system.')</p>
                <p>@lang('4. Disconnect the device once your task is complete.')</p>
            </div>
        </div>
    </div>
@endsection
