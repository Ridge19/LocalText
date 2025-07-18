@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn--base showFilterBtn btn--sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-3">
                <div class="card-body">
                    <form>
                        <div class="row gy-3 gx-2 gx-md-4">
                            <div class="col-12 col-md-4">
                                <label class="form-label">@lang('Search Device')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control form--control" placeholder="@lang('Search with device name or device id')">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label d-block">@lang('Status')</label>
                                <select class="form-select form--control select2--dropdown" data-minimum-results-for-search="-1" name="status">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="1" @selected(request()->status === (string) Status::ENABLE)>@lang('Connected')</option>
                                    <option value="0" @selected(request()->status === (string) Status::DISABLE)>@lang('Disconnected')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <button class="btn btn--base btn--md w-100"><i class="las la-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive" id="device-table">
                        <table class="table custom--table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Model')</th>
                                    <th>@lang('Device ID')</th>
                                    <th>@lang('Android Version')</th>
                                    <th>@lang('App Version')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allDevice as $device)
                                    <tr class="device-{{ $device->device_id }}">
                                        <td>
                                            <strong>{{ __($device->device_name) }}</strong>
                                        </td>
                                        <td>{{ __(@$device->device_model) }}</td>
                                        <td>{{ __(@$device->device_id) }}</td>
                                        <td>{{ __(@$device->android_version) }}</td>
                                        <td>{{ __(@$device->app_version) }}</td>
                                        <td class="device-status">
                                            @if ($device->status)
                                                <span class="badge badge--success">@lang('Connected')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Disconnected')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($device->status)
                                                <button type="button" class="btn btn--danger btn--sm disconnect-btn" 
                                                        data-device-id="{{ $device->id }}" 
                                                        data-device-name="{{ $device->device_name }}"
                                                        title="@lang('Disconnect Device')">
                                                    <i class="las la-unlink"></i> @lang('Disconnect')
                                                </button>
                                            @else
                                                <span class="text-muted">@lang('Already Disconnected')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-message-row">
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($allDevice->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($allDevice) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Connect Device Modal -->
    <div class="modal custom--modal fade modal-lg" id="modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <div>
                        <h4 class="modal-title">@lang('Connect your device')</h4>
                        <span class=" text--small">@lang('Follow the below steps to connect your device smoothly')</span>
                    </div>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group mb-3 list-group-flush list-group-numbered  mt-0">
                        <li class="list-group-item">
                            @lang('Download the ') {{ gs('site_name') }} @lang('APK file from')
                            <a class="text-primary" href="{{ apkUrl() }}" download>
                                @lang('here') <i class="las la-download"></i>
                            </a>
                        </li>
                        <li class="list-group-item"> @lang('Install our android app on your mobile device.')</li>
                        <li class="list-group-item"> @lang('Scan the below QR code to connect your device.')</li>
                    </ul>
                    <div class="text-center pb-3">
                        <img src="{{ $qrCodeImgSrc }}" class="b--5 p-3 border--base rounded" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disconnect Device Modal -->
    <div class="modal custom--modal fade" id="disconnectModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <h4 class="modal-title">@lang('Disconnect Device')</h4>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">
                        @lang('Are you sure you want to disconnect') <strong id="deviceNameToDisconnect"></strong>?
                    </p>
                    <p class="text-muted text-center">
                        @lang('This will stop the device from processing SMS messages until it connects again.')
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <form id="disconnectForm" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn--danger">
                            <i class="las la-unlink"></i> @lang('Disconnect Device')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="custom--loader d-none">
        <div class="custom--loader-content text-center">
            <div class="progress"></div>
            <span>@lang('A device is connecting to the system')</span>
        </div>
    </div>


    @php
        $allDeviceIds = $allDevice->pluck('device_id')->toArray();
    @endphp
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn--base btn--sm addBtn">
        <i class="las la-plus"></i> @lang('Add Device')
    </button>
@endpush

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/pusher.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/broadcasting.js') }}"></script>

    <script>
        "use strict";

        (function($) {
            $('.addBtn').on('click', function(e) {
                $("#modal").modal('show');
            });

            // Disconnect device functionality
            $('.disconnect-btn').on('click', function(e) {
                e.preventDefault();
                const deviceId = $(this).data('device-id');
                const deviceName = $(this).data('device-name');
                
                $('#deviceNameToDisconnect').text(deviceName);
                $('#disconnectForm').attr('action', `{{ route('user.device.disconnect', '') }}/${deviceId}`);
                $('#disconnectModal').modal('show');
            });

            // Handle disconnect form submission
            $('#disconnectForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                
                // Disable submit button and show loading
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="las la-spinner la-spin"></i> @lang("Disconnecting...")');
                
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#disconnectModal').modal('hide');
                        location.reload(); // Reload to show updated status
                    },
                    error: function(xhr) {
                        console.error('Disconnect failed:', xhr);
                        // Re-enable button
                        submitBtn.prop('disabled', false);
                        submitBtn.html('<i class="las la-unlink"></i> @lang("Disconnect Device")');
                    }
                });
            });

            const pusherConnection = (eventName) => {
                pusher.connection.bind('connected', () => {
                    const SOCKET_ID = pusher.connection.socket_id;
                    const CHANNEL_NAME = `private-${eventName}`;
                    pusher.config.authEndpoint = makeAuthEndPointForPusher(SOCKET_ID, CHANNEL_NAME);
                    let channel = pusher.subscribe(CHANNEL_NAME);
                    channel.bind('pusher:subscription_succeeded', function() {
                        channel.bind(eventName, function(data) {
                            if (eventName == 'device-logout') {
                                let allDevices = @json($allDeviceIds);
                                if(allDevices.includes(data.deviceId)){
                                    deviceLogOut(data.deviceId)
                                }
                            } else {
                                if (data.device?.user_id != @json(auth()->id())) return false;
                                newDeviceHtml(data.device)
                            }
                        })
                    });
                });
            };

            //device logout event lisiten
            pusherConnection('device-logout');

            //device add eveent lisiten
            pusherConnection('device-add');

            const newDeviceHtml = device => {

                let exitsDevice = $("#device-table").find(`tbody .device-${device.device_id}`);
                $(".custom--loader").find("span").text("@lang('A device is connecting to the system')");
                $(".custom--loader").removeClass('d-none');

                setTimeout(() => {

                    $(".custom--loader").addClass('d-none');
                    $(".modal").find(".modal-header button").click();

                    if (exitsDevice.length > 0) {
                        $("#device-table").find(`tbody .device-${device.device_id} .device-status`)
                            .html(`<span class="badge badge--success">@lang('Connected')</span>`);
                        // Update action column for reconnected device
                        $("#device-table").find(`tbody .device-${device.device_id} td:last-child`)
                            .html(`<button type="button" class="btn btn--danger btn--sm disconnect-btn" 
                                           data-device-id="${device.id}" 
                                           data-device-name="${device.device_name}"
                                           title="@lang('Disconnect Device')">
                                       <i class="las la-unlink"></i> @lang('Disconnect')
                                   </button>`);
                    } else {
                        let html = `
                        <tr class="device-${device.device_id}">
                            <td>
                                <strong>${device.device_name}</strong>
                            </td>
                            <td> ${device.device_model}</td>
                            <td> ${device.device_id}</td>
                            <td>${device.android_version}</td>
                            <td>${device.app_version}</td>
                            <td  class="device-status">
                                <span class="badge badge--success">@lang('Connected')</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn--danger btn--sm disconnect-btn" 
                                        data-device-id="${device.id}" 
                                        data-device-name="${device.device_name}"
                                        title="@lang('Disconnect Device')">
                                    <i class="las la-unlink"></i> @lang('Disconnect')
                                </button>
                            </td>
                        </tr>`;
                        $("#device-table").find("tbody").prepend(html);
                        $("#device-table").find(".empty-message-row").remove();
                    }
                    
                    // Re-bind click events for new disconnect buttons
                    bindDisconnectEvents();
                }, 3000);
            }

            const deviceLogOut = deviceId => {
                $(".custom--loader").find("span").text("@lang('A device is disconnecting to the system')");
                $(".custom--loader").removeClass('d-none');
                setTimeout(() => {
                    $(".custom--loader").addClass('d-none');
                    $(".modal").find(".modal-header button").click();
                    $("#device-table").find(`tbody .device-${deviceId} .device-status`)
                        .html(`<span class="badge badge--danger">@lang('Disconnected')</span>`);
                    // Update action column for disconnected device
                    $("#device-table").find(`tbody .device-${deviceId} td:last-child`)
                        .html(`<span class="text-muted">@lang('Already Disconnected')</span>`);
                }, 3000);
            }

            // Function to bind disconnect events (for dynamically added buttons)
            const bindDisconnectEvents = () => {
                $('.disconnect-btn').off('click').on('click', function(e) {
                    e.preventDefault();
                    const deviceId = $(this).data('device-id');
                    const deviceName = $(this).data('device-name');
                    
                    $('#deviceNameToDisconnect').text(deviceName);
                    $('#disconnectForm').attr('action', `{{ route('user.device.disconnect', '') }}/${deviceId}`);
                    $('#disconnectModal').modal('show');
                });
            };

            // Initial binding
            bindDisconnectEvents();
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .custom--loader {
            position: fixed;
            width: 100%;
            height: 100%;
            background: #00000070;
            z-index: 9999999;
            left: 0;
            top: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress {
            width: 100.8px;
            height: 16.8px;
            -webkit-mask: linear-gradient(90deg, hsl(var(--base)) 70%, #0000 0) left/20% 100%;
            background: linear-gradient(hsl(var(--base)) 0 0) left/0% 100% no-repeat #dbdcef;
            animation: progress-422c3u 2s infinite steps(6);
            width: 100%;
        }

        @keyframes progress-422c3u {
            100% {
                background-size: 120% 100%;
            }
        }

        .custom--loader-content {
            background: #fff;
            padding: 30px 40px;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 3px;
        }
    </style>
@endpush
