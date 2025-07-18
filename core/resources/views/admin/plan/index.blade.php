@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Recurring') | @lang('Contact Limit')</th>
                                    <th>@lang('Device Limit') | @lang('Group Limit')</th>
                                    <th>@lang('SMS Limit') | @lang('Daily SMS')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('plan') . '/' . $plan->image, getFileSize('plan')) }}" class="w-100">
                                                </div>
                                                <div class="plan_description">
                                                    <span class="name">{{ __($plan->name) }}</span>
                                                    <br>
                                                    <small class="name">{{ __($plan->title) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ showAmount(@$plan->price, currencyFormat: false) }} {{ gs('cur_text') }}</td>
                                        <td>
                                            {{ @$plan->recurringtypeName }}
                                            <br>
                                            @if (@$plan->contact_limit == Status::UNLIMITED)
                                                @lang('Unlimited')
                                            @else
                                                {{ @$plan->contact_limit }}
                                                {{ str()->plural('Contact', @$plan->contact_limit) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$plan->device_limit == Status::UNLIMITED)
                                                @lang('Unlimited')
                                            @else
                                                {{ @$plan->device_limit }}
                                                {{ str()->plural('Device', @$plan->device_limit) }}
                                            @endif
                                            <br>
                                            @if (@$plan->group_limit == Status::UNLIMITED)
                                                @lang('Unlimited')
                                            @else
                                                {{ @$plan->group_limit }}
                                                {{ str()->plural('Group', @$plan->group_limit) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$plan->sms_limit == Status::UNLIMITED)
                                                @lang('Unlimited')
                                            @else
                                                {{ @$plan->sms_limit }}
                                                {{ str()->plural('Sms', @$plan->sms_limit) }}
                                            @endif
                                            <br>
                                            @if (@$plan->daily_sms_limit == Status::UNLIMITED)
                                                @lang('Unlimited')
                                            @else
                                                {{ @$plan->daily_sms_limit }}
                                                {{ str()->plural('Sms', @$plan->daily_sms_limit) }}
                                            @endif
                                        </td>
                                        <td>
                                            @php echo $plan->statusBadge @endphp
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary ms-1 editBtn" data-plan='@json($plan)' data-image="{{ getImage(getFilePath('plan') . '/' . $plan->image, getFileSize('plan')) }}">
                                                <i class="la la-pen"></i> @lang('Edit')
                                            </button>
                                            @if ($plan->status == Status::ENABLE)
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn ms-1" data-question="@lang('Are you sure to disable this plan?')" data-action="{{ route('admin.plan.status', $plan->id) }}">
                                                    <i class="la la-eye-slash"></i>@lang('Disable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--success confirmationBtn ms-1" data-question="@lang('Are you sure to enable this plan?')" data-action="{{ route('admin.plan.status', $plan->id) }}">
                                                    <i class="la la-eye"></i>@lang('Enable')
                                                </button>
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
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($plans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
    <!-- Add Modal -->
    <div class="modal fade" id="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="createModalLabel"> @lang('Add New Plan')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label> @lang('Image') </label>
                                <x-image-uploader image="/" type="plan" class="w-100" :required="false" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>@lang('Plan Name')</label>
                                <input type="text" class="form-control" value="{{ old('name') }}" name="name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Plan Title')</label>
                                <input type="text" class="form-control" value="{{ old('title') }}" name="title" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Price')</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" value="{{ old('price') }}" name="price" required>
                                    <span class="input-group-text"> {{ __(gs('cur_text')) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Recurring Type')</label>
                                <select class="form-control select2" name="recurring_type" required data-minimum-results-for-search="-1">
                                    <option value="{{ Status::MONTHLY_PLAN }}" selected>@lang('Monthly')</option>
                                    <option value="{{ Status::YEARLY_PLAN }}">@lang('Yearly')</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <label>@lang('Total SMS Limit')</label>
                                        <input type="checkbox" data-bs-toggle="tooltip" title="@lang('Unlimited')" class="form--check--input checkd-unlimited">
                                    </div>
                                    <input type="number" class="form-control limit_input" value="{{ old('sms_limit') }}" name="sms_limit" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <label>@lang('Device Limit')</label>
                                        <input type="checkbox" data-bs-toggle="tooltip" title="@lang('Unlimited')" class="form--check--input checkd-unlimited">
                                    </div>
                                    <input type="number" class="form-control limit_input" value="{{ old('device_limit') }}" name="device_limit" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <label>@lang('Daily SMS Limit')</label>
                                        <input type="checkbox" data-bs-toggle="tooltip" title="@lang('Unlimited')" class="form--check--input checkd-unlimited">
                                    </div>
                                    <input type="number" class="form-control limit_input" value="{{ old('daily_sms_limit') }}" name="daily_sms_limit" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <label>@lang('Contact Limit')</label>
                                        <input type="checkbox" data-bs-toggle="tooltip" title="@lang('Unlimited')" class="form--check--input checkd-unlimited">
                                    </div>
                                    <input type="number" class="form-control limit_input" value="{{ old('contact_limit') }}" name="contact_limit" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <label>@lang('Group Limit')</label>
                                        <input type="checkbox" data-bs-toggle="tooltip" title="@lang('Unlimited')" class="form--check--input checkd-unlimited">
                                    </div>
                                    <input type="number" class="form-control limit_input" value="{{ old('group_limit') }}" name="group_limit" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('API Available')</label>
                                <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('YES')" data-off="@lang('NO')" name="api_available">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Schedule SMS')</label>
                                <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('YES')" data-off="@lang('NO')" name="scheduled_sms">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('Campaign')</label>
                                <input type="checkbox" data-width="100%" data-height="40px" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('YES')" data-off="@lang('NO')" name="campaign">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45" id="btn-save" value="add">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            const $modal = $("#modal");

            var checkedUnlimited = $('.checkd-unlimited');

            checkedUnlimited.on('change', function() {
                if (this.checked) {
                    $(this).closest('.form-group').find('input[type="number"]').attr('readonly', true).val(
                        '{{ Status::UNLIMITED }}');
                } else {
                    $(this).closest('.form-group').find('input[type="number"]').attr('readonly', false).val('');
                }
            })

            $('.editBtn').on('click', function() {
                var url = $(this).data('url');
                var plan = $(this).data('plan');
                var action = "{{ route('admin.plan.update', ':id') }}";

                $modal.find('form').attr('action', action.replace(":id", plan.id));
                $modal.find('.modal-title').text("@lang('Edit Plan')")
                $modal.find('form').attr('action', url);
                $modal.find('input[name=name]').val(plan.name);
                $modal.find('input[name=title]').val(plan.title);
                $modal.find('input[name=price]').val(parseFloat(plan.price).toFixed(2));
                $modal.find('select[name=recurring_type]').val(plan.recurring_type).trigger("change");
                $modal.find('input[name=device_limit]').val(plan.device_limit);
                $modal.find('input[name=sms_limit]').val(plan.sms_limit);
                $modal.find('input[name=daily_sms_limit]').val(plan.daily_sms_limit);
                $modal.find('input[name=contact_limit]').val(plan.contact_limit);
                $modal.find('input[name=group_limit]').val(plan.group_limit);

                $modal.find('.limit_input').each(function() {
                    if ($(this).val() == '{{ Status::UNLIMITED }}') {
                        $(this).closest('.form-group').find('input[type="checkbox"]').prop('checked',
                            true);
                        $(this).attr('readonly', true);
                    } else {
                        $(this).closest('.form-group').find('input[type="checkbox"]').prop('checked',
                            false);
                        $(this).attr('readonly', false);
                    }
                })

                $modal.find('.image-upload-preview').css('background-image', `url(${$(this).data('image')})`);

                if (plan.api_available) {
                    $modal.find('input[name=api_available]').bootstrapToggle('on');
                } else {
                    $modal.find('input[name=api_available]').bootstrapToggle('off');
                }

                if (plan.scheduled_sms) {
                    $modal.find('input[name=scheduled_sms]').bootstrapToggle('on');
                } else {
                    $modal.find('input[name=scheduled_sms]').bootstrapToggle('off');
                }
                if (plan.campaign) {
                    $modal.find('input[name=campaign]').bootstrapToggle('on');
                } else {
                    $modal.find('input[name=campaign]').bootstrapToggle('off');
                }
                $modal.modal('show');
            });

            $('.add-btn').on('click', function() {
                $modal.find('form').trigger('reset');
                $modal.find('.modal-title').text("@lang('Add Plan')");
                $modal.find('.image-upload-preview').css('background-image',
                    `url("{{ getImage(getFilePath('plan'), getFileSize('plan')) }}")`);
                $modal.find('.limit_input').attr('readonly', false);
                var action = "{{ route('admin.plan.store') }}";
                $modal.find('form').attr('action', action);
                $modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-sm btn-outline--primary add-btn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush

@push('style')
    <style>
        .table--light thead th {
            white-space: normal !important;
        }

        table .user {
            flex-wrap: nowrap;
        }

        @media (max-width: 991px) {
            .table-responsive--md table .user {
                flex-wrap: wrap;
                justify-content: flex-end;
            }
        }
    </style>
@endpush
