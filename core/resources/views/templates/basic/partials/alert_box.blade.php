@php
    $hasDevice = \App\Models\Device::belongsToUser()->exists();
    $hasConnectedDevice = \App\Models\Device::belongsToUser()->connected()->exists();
@endphp

@if (!$hasDevice)
    <div class="col-12">
        <div class="alert border border--danger {{ @$class ?? '' }}" role="alert">
            <div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-robot"></i></div>
            <p class="alert__message">
                <span class="fw-bold">@lang('No Device Added')</span><br>
                <small>@lang('You haven\'t added any device yet. To send messages from this system, please add a device first.') <a href="{{ route('user.device.index') }}" class="text--primary">@lang('Click here to add a device.')</a></small>
            </p>
        </div>
    </div>
@elseif(!$hasConnectedDevice)
    <div class="col-12">
        <div class="alert border border--danger {{ @$class ?? '' }}" role="alert">
            <div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-robot"></i></div>
            <p class="alert__message">
                <span class="fw-bold">@lang('No Devices are Connected')</span><br>
                <small>@lang('No devices are connected. To send messages from this system, you must first connect a device.')</small>
            </p>
        </div>
    </div>
@endif
