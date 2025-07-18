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
        .message {
            cursor: pointer;
            font-size: 12px !important;
        }

        .card-header-dropdown {
            cursor: pointer;
            border-bottom: unset;
        }

        .list.list-style-three li .label,
        .list.list-style-three li .value {
            width: calc(50% - 20px);
            font-size: 15px;
            font-family: var(--heading-font);
        }

        .list.list-style-three li {
            position: relative;
            padding: 12px 0;
            border-bottom: 1px solid #ebebeb;
        }

        .list.list-style-three li:last-child {
            border-bottom: none;
        }
    </style>
@endpush
