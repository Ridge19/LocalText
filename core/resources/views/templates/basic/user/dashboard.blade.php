@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="notice"></div>

    <!-- Devices -->
    <div class="row g-3 mb-3">
        @include('Template::partials.alert_box', ['class' => 'mb-0'])

        <div class="col-sm-6 col-lg-4 col-xxl-4">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_DELIVERED]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Total Device')</h6>
                    <i class="lab la-android text--info fs-3"></i>
                </div>
                <h5 class="widget-amount text--info fw-bold mt-2">
                    {{ $widget['total_device'] }} <span class="text-muted">@lang('Devices')</span>
                </h5>
            </a>
        </div>
        <div class="col-sm-6 col-lg-4 col-xxl-4">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_DELIVERED]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Connected Device')</h6>
                    <i class="las la-check-circle text--success fs-3"></i>
                </div>
                <h5 class="widget-amount text--success fw-bold mt-2">
                    {{ $widget['connected_device'] }} <span class="text-muted">@lang('Devices')</span>
                </h5>
            </a>
        </div>
        <div class="col-sm-12 col-lg-4 col-xxl-4">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_DELIVERED]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Disconnected Device')</h6>
                    <i class="las la-times-circle text--danger fs-3"></i>
                </div>
                <h5 class="widget-amount text--danger fw-bold mt-2">
                    {{ $widget['disconnected_device'] }} <span class="text-muted">@lang('Devices')</span>
                </h5>
            </a>
        </div>
    </div>
    <!-- SMS -->
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_DELIVERED]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Successfully Sent')</h6>
                    <i class="las la-check-circle text--success fs-3"></i>
                </div>
                <h5 class="widget-amount text--success fw-bold mt-2">
                    {{ $widget['sms']['sent'] }} <span class="text-muted">@lang('SMS')</span>
                </h5>
            </a>
        </div>
        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_INITIAL]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Initiated SMS')</h6>
                    <i class="las la-campground text--dark fs-3"></i>
                </div>
                <h5 class="widget-amount text--dark fw-bold mt-2">
                    {{ $widget['sms']['initiated'] }} <span class="text-muted">@lang('SMS')</span>
                </h5>
            </a>
        </div>
        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_SCHEDULED]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Scheduled SMS')</h6>
                    <i class="las la-clock text--primary fs-3"></i>
                </div>
                <h5 class="widget-amount text--primary fw-bold mt-2">
                    {{ $widget['sms']['scheduled'] }} <span class="text-muted">@lang('SMS')</span>
                </h5>
            </a>
        </div>
        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.sms.index', ['status' => Status::SMS_FAILED]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Failed SMS')</h6>
                    <i class="las la-times-circle text--danger fs-3"></i>
                </div>
                <h5 class="widget-amount text--danger fw-bold mt-2">
                    {{ $widget['sms']['failed'] }} <span class="text-muted">@lang('SMS')</span>
                </h5>
            </a>
        </div>
    </div>

    <!-- Active Plan & Recent Sms -->
    <div class="row gy-4">
        @if (@$activePlan)
            <div class="col-lg-4">
                <div class="card custom--card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-1">
                        <div>
                            <h6 class="card-title mb-0">{{ @$activePlan->name }}</h6>
                            <p class="text-muted">@lang('Expires at') : {{ showDateTime(@$user->plan_expires_at, 'd M Y') }}</p>
                        </div>
                        <div>
                            <a href="{{ route('user.plan.purchased') }}" class="btn btn--base btn--sm">@lang('Purchase History')</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list list-style-three text-start">
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Recurring Type')</span>
                                <span class="value">{{ @$activePlan->recurringtypeName }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Campaign')</span>
                                <span class="value">{{ @$user->plan_campaign_available == Status::YES ? 'Yes' : 'No' }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('SMS Limit')</span>
                                <span class="value">{{ @$user->available_sms == Status::UNLIMITED ? 'Unlimited' : @$user->available_sms }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Device Limit')</span>
                                <span class="value">{{ @$user->available_device_limit == Status::UNLIMITED ? 'Unlimited' : @$user->available_device_limit }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Daily SMS Limit')</span>
                                <span class="value">{{ @$user->daily_sms_limit == Status::UNLIMITED ? 'Unlimited' : @$user->daily_sms_limit }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Contact Limit')</span>
                                <span class="value">{{ @$user->available_contact_limit == Status::UNLIMITED ? 'Unlimited' : @$user->available_contact_limit }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Group Limit')</span>
                                <span class="value">{{ @$user->available_group_limit == Status::UNLIMITED ? 'Unlimited' : @$user->available_group_limit }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('Schedule SMS')</span>
                                <span class="value">{{ @$user->plan_scheduled_sms == Status::YES ? 'Yes' : 'No' }}</span>
                            </li>
                            <li class="d-flex flex-wrap justify-content-between align-items-center">
                                <span class="label">@lang('API Available')</span>
                                <span class="value">{{ @$user->plan_api_available == Status::YES ? 'Yes' : 'No' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="@if(@$activePlan) col-lg-8 @else col-lg-12 @endif">
            <div class="card custom--card recent-sms">
                <div class="card-body p-0">
                    <div class="card custom--card">
                        <div class="card-header d-flex justify-content-between flex-wrap gap-2 align-items-center">
                            <h5 class="card-title mb-0">@lang('Recent SMS')</h5>

                            @if ($sms->count())
                                <a href="{{ route('user.sms.index') }}" class="btn btn--base btn--sm">@lang('SMS History')</a>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table--responsive--lg message-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Device')</th>
                                            <th>@lang('Mobile Number')</th>
                                            <th>@lang('SMS')</th>
                                            <th>@lang('SMS Type')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($sms as $key => $message)
                                            <tr>
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
                                                <td>
                                                    @if (!$message->api_key_id)
                                                        +
                                                    @endif
                                                    {{ $message->mobile_number }}
                                                </td>
                                                <td>
                                                    <span> {{ __(strLimit($message->message, 20)) }}</span>
                                                    @if (strlen($message->message) > 20)
                                                        <span class="text--primary message" message="{{ $message->message }}">
                                                            @lang('Read More')
                                                        </span>
                                                    @endif
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
                                                <td class="text-muted text-center" colspan="100%">
                                                    {{ __($emptyMessage) }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade message-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('SMS')</h4>
                    <button type="button" class="close btn btn--sm" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            let messageModal = $(".message-modal");

            $('.message-table').on('click', '.message', function(e) {
                let message = $(this).attr('message');
                messageModal.find('.modal-title').text("@lang('Sms')")
                $(messageModal).find('.modal-body').html(`
                <p>${message}</p>
            `)
                messageModal.modal('show')
            });

            $('.card-header-dropdown').on('click', function() {
                $(this).find('i').toggleClass('la-chevron-down la-chevron-up');
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        /* Modern Template Dashboard Styling */
        body {
            background: #f8f9fc !important;
        }

        /* Dashboard Widget Cards */
        .dashboard-widget-main {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 1.5rem;
            display: block;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            position: relative;
            overflow: hidden;
        }

        .dashboard-widget-main:hover {
            transform: translateY(-0.125rem);
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
            text-decoration: none;
            border-color: #5a5c69;
        }

        .dashboard-widget-main h6 {
            font-size: 0.7rem;
            font-weight: 700;
            margin-bottom: 0;
            color: #5a5c69 !important;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .dashboard-widget-main .widget-amount {
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 0.5rem !important;
            line-height: 1.2;
            color: #5a5c69;
        }

        .dashboard-widget-main .widget-amount span {
            font-size: 0.7rem;
            font-weight: 400;
            color: #858796 !important;
        }

        .dashboard-widget-main i {
            font-size: 2rem !important;
            opacity: 0.3;
        }

        /* Color scheme */
        .text--info, .dashboard-widget-main .text--info {
            color: #36b9cc !important;
        }

        .text--success, .dashboard-widget-main .text--success {
            color: #1cc88a !important;
        }

        .text--danger, .dashboard-widget-main .text--danger {
            color: #e74a3b !important;
        }

        .text--primary, .dashboard-widget-main .text--primary {
            color: #4e73df !important;
        }

        .text--dark, .dashboard-widget-main .text--dark {
            color: #5a5c69 !important;
        }

        /* Card Enhancements */
        .custom--card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.15s ease-in-out;
            background: #fff;
        }

        .custom--card:hover {
            transform: translateY(-0.0625rem);
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
        }

        .custom--card .card-header {
            background: #f8f9fc;
            color: #5a5c69;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 0.35rem 0.35rem 0 0;
            padding: 0.75rem 1.25rem;
            font-weight: 700;
        }

        .custom--card .card-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: #5a5c69;
        }

        .custom--card .card-body {
            padding: 1.25rem;
        }

        /* Plan Details List */
        .list.list-style-three {
            margin: 0;
            padding: 0;
        }

        .list.list-style-three li {
            position: relative;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e3e6f0;
            transition: background 0.15s ease-in-out;
        }

        .list.list-style-three li:hover {
            background: #f8f9fc;
            border-radius: 0.25rem;
            margin: 0 -0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .list.list-style-three li:last-child {
            border-bottom: none;
        }

        .list.list-style-three li .label,
        .list.list-style-three li .value {
            width: calc(50% - 20px);
            font-size: 0.8rem;
            font-family: inherit;
        }

        .list.list-style-three li .label {
            color: #858796;
            font-weight: 400;
        }

        .list.list-style-three li .value {
            color: #5a5c69;
            font-weight: 600;
        }

        /* Button Enhancements */
        .btn--base {
            background: #4e73df;
            border: 1px solid #4e73df;
            border-radius: 0.35rem;
            font-weight: 400;
            transition: all 0.15s ease-in-out;
            text-transform: none;
            letter-spacing: 0;
            font-size: 0.8rem;
            color: #fff;
            padding: 0.375rem 0.75rem;
        }

        .btn--base:hover {
            background: #2e59d9;
            border-color: #2653d4;
            transform: translateY(-0.0625rem);
            box-shadow: 0 0.125rem 0.5rem 0 rgba(78, 115, 223, 0.5);
            color: #fff;
        }

        /* Recent SMS Table */
        .message-table {
            margin: 0;
            background: #fff;
            color: #858796;
        }

        .message-table thead th {
            background: #f8f9fc;
            border: none;
            color: #5a5c69;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 0.05rem;
            padding: 0.75rem;
            border-bottom: 1px solid #e3e6f0;
        }

        .message-table tbody tr {
            transition: all 0.15s ease-in-out;
            border: none;
        }

        .message-table tbody tr:hover {
            background: #f8f9fc;
        }

        .message-table tbody td {
            padding: 0.75rem;
            border: none;
            border-bottom: 1px solid #e3e6f0;
            vertical-align: middle;
            color: #858796;
            font-size: 0.8rem;
        }

        /* Badge Improvements */
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.35rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .badge--success {
            background: #1cc88a;
            color: #fff;
        }

        .badge--primary {
            background: #4e73df;
            color: #fff;
        }

        /* Message Modal */
        .message {
            cursor: pointer;
            font-size: 0.65rem !important;
            color: #4e73df;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }

        .message:hover {
            text-decoration: underline;
            color: #2e59d9;
        }

        .card-header-dropdown {
            cursor: pointer;
            border-bottom: unset;
        }

        /* Container and spacing adjustments */
        .row.g-3 {
            margin-bottom: 1.5rem;
        }

        .row.gy-4 {
            margin-top: 1.5rem;
        }

        /* Alert Box Enhancement */
        .alert {
            border-radius: 0.35rem;
            border: 1px solid;
            font-weight: 400;
            font-size: 0.8rem;
        }

        .alert-info {
            background: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        /* Notice Enhancement */
        .notice {
            margin-bottom: 1.5rem;
        }

        /* Modal Styling */
        .modal-content {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            background: #f8f9fc;
        }

        .modal-title {
            color: #5a5c69;
            font-weight: 700;
            font-size: 1rem;
        }

        .modal-body {
            padding: 1.25rem;
            color: #858796;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .dashboard-widget-main {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .dashboard-widget-main .widget-amount {
                font-size: 1.125rem;
            }

            .custom--card .card-header {
                padding: 0.75rem 1rem;
            }

            .custom--card .card-body {
                padding: 1rem;
            }

            .list.list-style-three li {
                padding: 0.5rem 0;
            }

            .list.list-style-three li .label,
            .list.list-style-three li .value {
                font-size: 0.75rem;
            }

            .message-table thead th,
            .message-table tbody td {
                padding: 0.5rem 0.25rem;
            }
        }

        /* Text muted override */
        .text-muted {
            color: #858796 !important;
        }

        .text-secondary {
            color: #858796 !important;
        }

        /* Table responsive wrapper */
        .table-responsive {
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
        }

        /* Bootstrap spacing overrides */
        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        /* Widget hover effects */
        .dashboard-widget-main:hover .widget-amount {
            color: #2e59d9;
        }

        .dashboard-widget-main:hover h6 {
            color: #2e59d9 !important;
        }
    </style>
@endpush
