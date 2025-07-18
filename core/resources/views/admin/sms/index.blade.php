@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="row align-items-center">
                            <div class="col-lg-4 form-group">
                                <label>@lang('Search')</label>
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}"
                                    placeholder="@lang('Search with device or mobile number')" class="form-control">
                            </div>
                            <div class="col-lg-2 form-group">
                                <label>@lang('Status')</label>
                                <select name="status" class="form-control select2" id="status"
                                    data-minimum-results-for-search="-1">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="{{ Status::SMS_DELIVERED }}" @selected(request()->status == Status::SMS_DELIVERED)>
                                        @lang('Delivered')</option>
                                    <option value="{{ Status::SMS_PENDING }}" @selected(request()->status == Status::SMS_PENDING)>
                                        @lang('Pending')</option>
                                    <option value="{{ Status::SMS_SCHEDULED }}" @selected(request()->status == Status::SMS_SCHEDULED)>
                                        @lang('Scheduled')</option>
                                    <option value="{{ Status::SMS_PROCESSING }}" @selected(request()->status == Status::SMS_PROCESSING)>
                                        @lang('Processing')</option>
                                    <option value="{{ Status::SMS_FAILED }}" @selected(request()->status == Status::SMS_FAILED)>@lang('Failed')
                                    </option>
                                </select>
                            </div>
                            <div class="col-lg-2 form-group">
                                <label>@lang('Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="col-lg-2 form-group">
                                <label>@lang('Sms Type')</label>
                                <select name="sms_type" class="form-control select2" data-minimum-results-for-search="-1">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="{{ Status::SMS_TYPE_SEND }}" @selected(request()->sms_type == Status::SMS_TYPE_SEND)>
                                        @lang('Send')</option>
                                    <option value="{{ Status::SMS_TYPE_RECEIVED }}" @selected(request()->sms_type == Status::SMS_TYPE_RECEIVED)>
                                        @lang('Received')</option>
                                </select>
                            </div>
                            <div class="col-lg-2 form-group">
                                <button class="btn btn--primary w-100 h-45 mt-4" type="submit">
                                    <i class="fas fa-filter"></i>
                                    @lang('Filter')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two" id="message-table">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Device')</th>
                                    <th>@lang('Mobile Number')</th>
                                    <th>@lang('Sms')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Sms Type')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr>
                                        <td>
                                            @if ($message->user)
                                                <span class="fw-bold">{{ @$message->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a
                                                        href="{{ appendQuery('search', @$message->user->username) }}"><span>@</span>{{ $message->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($message->device)
                                                <span>
                                                    {{ __($message->device->device_name) }} -
                                                    {{ __($message->device->device_model) }}
                                                </span>
                                                <br>
                                                <span class="badge badge--success">
                                                    {{ __($message->device_slot_number) }}-{{ __($message->device_slot_name) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ __(@$message->mobile_number) }}</td>
                                        <td>
                                            <span> {{ __(strLimit($message->message, 50)) }}</span>
                                            @if (strlen($message->message) > 50)
                                                <span class="text--primary message" message="{{ $message->message }}">
                                                    @lang('Read More')
                                                </span>
                                            @endif
                                        </td>
                                        <td>
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
                                            @else
                                                <span class="badge badge--dark">@lang('Initial')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($message->sms_type == 1)
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
                    <div class="card-footer py-4">
                        {{ paginateLinks($messages) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="custom--loader d-none">
        <div class="custom--loader-content text-center">
            <div class="progress"></div>
            <span>@lang('The system is receiving a new message')</span>
        </div>
    </div>

@endsection

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
                <td>${message.search}</td>
                <td>
                    <span>${truncateString(message.message)}</span>
                    <span class="text--primary message ${message.message.length <= 50 ? 'd-none' : ''}" message="${message.message}">
                        @lang('Read More')
                    </span>
                </td>
                <td><span class="badge badge--success">@lang('Received')</span></td>
                <td><span class="badge badge--primary">@lang('Received')</span></td>
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
