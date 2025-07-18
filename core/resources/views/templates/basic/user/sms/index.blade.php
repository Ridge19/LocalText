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
                            <div class="col-12 col-md-2">
                                <label class="form-label">@lang('Mobile Number')</label>
                                <input type="search" name="mobile_number" value="{{ request()->mobile_number ?? null }}"
                                    autocomplete="off" class="form-control form--control" placeholder="@lang('Search with mobile number')">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label d-block">@lang('Device')</label>
                                <select class="form-select form--control select2--dropdown" name="device_id">
                                    <option value="" selected>@lang('All')</option>
                                    @foreach ($allDevice as $device)
                                        <option value="{{ $device->id }}" data-sim='@json($device->sim)'
                                            @selected(request()->device_id == $device->id)>
                                            {{ __($device->device_name) }}-{{ __($device->device_model) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label d-block">@lang('Status')</label>
                                <select class="form-select form--control select2--dropdown" name="status">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="{{ Status::SMS_INITIAL }}" @selected(request()->status === (string) Status::SMS_INITIAL)>
                                        @lang('Initial')</option>
                                    <option value="{{ Status::SMS_DELIVERED }}" @selected(request()->status == Status::SMS_DELIVERED)>
                                        @lang('Delivered')</option>
                                    <option value="{{ Status::SMS_PENDING }}" @selected(request()->status == Status::SMS_PENDING)>
                                        @lang('Pending')</option>
                                    <option value="{{ Status::SMS_SCHEDULED }}" @selected(request()->status == Status::SMS_SCHEDULED)>
                                        @lang('Scheduled')</option>
                                    <option value="{{ Status::SMS_PROCESSING }}" @selected(request()->status == Status::SMS_PROCESSING)>
                                        @lang('Processing')</option>
                                    <option value="{{ Status::SMS_FAILED }}" @selected(request()->status == Status::SMS_FAILED)>
                                        @lang('Failed')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label">@lang('Date')</label>
                                <input type="search" name="date" value="{{ request()->date }}"
                                    class="form-control form--control datepicker-here date-range" autocomplete="off"
                                    placeholder="@lang('Start Date - End Date')">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="form-label d-block">@lang('Sms Type')</label>
                                <select class="form-select form--control select2--dropdown"
                                    data-minimum-results-for-search="-1" name="sms_type">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="{{ Status::SMS_TYPE_SEND }}" @selected(request()->sms_type == Status::SMS_TYPE_SEND)>
                                        @lang('Send')</option>
                                    <option value="{{ Status::SMS_TYPE_RECEIVED }}" @selected(request()->sms_type == Status::SMS_TYPE_RECEIVED)>
                                        @lang('Received')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-2 d-flex align-items-end">
                                <button class="btn btn--base btn--md w-100"><i class="las la-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--responsive--lg message-table">
                            <thead>
                                <tr>
                                    <th>@lang('Device')</th>
                                    <th>@lang('Mobile Number')</th>
                                    <th>@lang('SMS')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('SMS Type')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($messages as $message)
                                    <tr>
                                        <td>
                                            <div>
                                            @if (@$message->device)
                                                    <span>
                                                        {{ __(@$message->device->device_name) }} -
                                                        {{ __(@$message->device->device_model) }}
                                                    </span>
                                                    <br>
                                                    <span class="badge badge--success">
                                                        {{ __(@$message->device_slot_number) }}-{{ __(@$message->device_slot_name) }}
                                                    </span>
                                                    @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if (!$message->api_key_id)
                                            +
                                            @endif
                                            {{ @$message->mobile_number }}
                                        </td>
                                        <td>
                                            <span> {{ __(strLimit($message->message, 20)) }}</span>
                                            @if (strlen($message->message) > 20)
                                                <span class="text--primary message" message="{{ @$message->message }}">
                                                    @lang('Read More')
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $question = trans('Are you sure to resend this sms?');
                                            @endphp
                                            <div>
                                                @if ($message->status == 1)
                                                <span class="badge badge--success">
                                                    @if ($message->sms_type == 1)
                                                        @lang('Delivered')
                                                    @else
                                                        @lang('Received')
                                                    @endif
                                                </span>
                                            @elseif($message->status == 2)
                                                <span class="badge badge--warning">
                                                    @lang('Pending')
                                                </span>
                                            @elseif($message->status == 3)
                                                <span class="badge badge--primary">
                                                    @lang('Scheduled')
                                                </span> <br>
                                                <small>{{ showDateTime($message->schedule, 'Y-m-d H:i') }}</small>
                                            @elseif($message->status == 4)
                                                <span class="badge badge--primary">
                                                    @lang('Processing')
                                                </span>
                                            @elseif($message->status == 9)
                                                <span class="badge badge--danger">
                                                    @lang('Failed')
                                                    <a href="javascript:void(0)" class="fail-message text--danger"
                                                        data-details='@json($message->failReason)'>
                                                        <i class="las la-info-circle"></i>
                                                    </a>
                                                </span>
                                                <span class="ms-1 confirmationBtn cursor-pointer"
                                                    data-question="@lang('Are you sure to resend this sms?')"
                                                    data-action="{{ route('user.sms.resend', $message->id) }}">
                                                    <i class="las la-redo"></i>
                                                </span>
                                            @else
                                                <span class="badge badge--dark">@lang('Initial')</span>
                                                @if ($message->et == 1)
                                                    <span class="ms-1 confirmationBtn cursor-pointer"
                                                        data-question="@lang('Are you sure to resend this sms?')"
                                                        data-action="{{ route('user.sms.resend', $message->id) }}">
                                                        <i class="fas fa-redo"></i>
                                                    </span>
                                                @endif
                                            @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($message->sms_type == Status::SMS_TYPE_SEND)
                                                <span class="badge badge--success">@lang('Send')</span>
                                            @else
                                                <span class="badge badge--primary">@lang('Received')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($messages->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($messages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />

    <div class="modal fade message-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Add New Device')</h4>
                    <button type="button" class="close btn btn--sm" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fail-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Faild Reason')</h4>
                    <button type="button" class="close btn btn--sm" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="error-short fw-bold"></p>
                    <div class="error-desc"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.sms.send') }}" class="btn btn--base btn--sm addBtn">
        <i class="las la-paper-plane"></i> @lang('Send SMS')
    </a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/pusher.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/broadcasting.js') }}?v8"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            //message received channel
            pusher.connection.bind('connected', () => {
                const SOCKET_ID = pusher.connection.socket_id;
                const CHANNEL_NAME = "private-message-received";
                pusher.config.authEndpoint = makeAuthEndPointForPusher(SOCKET_ID, CHANNEL_NAME);
                let channel = pusher.subscribe(CHANNEL_NAME);
                channel.bind('pusher:subscription_succeeded', function() {
                    channel.bind('message-received', function(data) {
                        let message = data.data.original.data.message;
                        messageReceived(message);
                    })
                });
            });

            let messageModal = $(".message-modal");

            $('.message-table').on('click', '.message', function(e) {
                let message = $(this).attr('message');
                messageModal.find('.modal-title').text("@lang('SMS')")
                $(messageModal).find('.modal-body').html(`
                <p>${message}</p>
            `)
                messageModal.modal('show')
            });

            function truncateString(str) {
                const maxLength = 50;
                if (str.length > maxLength) {
                    return str.substring(0, maxLength) + '...';
                }
                return str;
            }

            function messageReceived(message) {
                $(".custom--loader").removeClass('d-none');
                setTimeout(() => {
                    let html = `
            <tr>
                <td>
                    <span>${message.device_name}</span>
                    <br>
                    <span class="badge badge--success">
                        ${message.device_slot_number}-${message.device_slot_name}
                    </span>
                </td>
                <td>${message.mobile_number}</td>
                <td>
                    <span>${truncateString(message.message)}</span>
                    <span class="text--primary message ${message.message.length <= 50 ? 'd-none' : ''}" message="${message.message}">
                        @lang('Filter')
                    </span>
                </td>
                <td><span class="badge badge--success">@lang('Mobile Number')</span></td>
                <td><span class="badge badge--primary">@lang('Search with mobile number')</span></td>
            </tr>
            `;

                    $("#message-table").find("tbody").prepend(html);
                    $(".custom--loader").addClass('d-none');
                }, 2000);
            }
            @if (request()->sms_type)
                $('select[name=sms_type]').val("{{ request()->sms_type }}")
            @endif

            @if (request()->device_id)
                $('select[name=device_id]').val("{{ request()->device_id }}")
            @endif

            $('.fail-message').on('click', function(e) {
                let modal = $("#fail-modal");
                let details = $(this).data('details');

                modal.find(".error-short").text(details?.error_keyword);
                modal.find(".error-desc").text(details?.error_explanation);
                modal.modal('show')
            });

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

            let modal = $("#modal");

            $('#message-table').on('click', '.message', function(e) {
                let message = $(this).attr('message');
                modal.find('.modal-title').text("@lang('Device')")
                $(modal).find('.modal-body').html(`
                <p>${message}</p>
            `)
                modal.modal('show')
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .instruction {
            list-style: auto;
            padding: 0px 40px;
        }

        .message {
            cursor: pointer;
            font-size: 12px !important;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        .fail-message {
            cursor: pointer;
        }

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
            -webkit-mask: linear-gradient(90deg, #474bff 70%, #0000 0) left/20% 100%;
            background: linear-gradient(#474bff 0 0) left/0% 100% no-repeat #dbdcef;
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

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush
