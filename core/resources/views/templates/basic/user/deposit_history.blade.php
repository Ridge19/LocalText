@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row g-3 mb-3">

        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.deposit.history', ['status' => Status::PAYMENT_SUCCESS]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Successful Deposit')</h6>
                    <i class="las la-wallet text--success fs-3"></i>
                </div>
                <h5 class="widget-amount text--success fw-bold mt-2">
                    {{ showAmount($widget['deposit']['successful'], currencyFormat: false) }} <span class="text-muted">{{ gs('cur_text') }}</span>
                </h5>
            </a>
        </div>

        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.deposit.history', ['status' => Status::PAYMENT_PENDING]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Pending Deposit')</h6>
                    <i class="las la-clock text--warning fs-3"></i>
                </div>
                <h5 class="widget-amount text--warning fw-bold mt-2">
                    {{ showAmount($widget['deposit']['pending'], currencyFormat: false) }} <span class="text-muted">{{ gs('cur_text') }}</span>
                </h5>
            </a>
        </div>

        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.deposit.history', ['status' => Status::PAYMENT_REJECT]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Rejected Deposit')</h6>
                    <i class="las la-times-circle text--danger fs-3"></i>
                </div>
                <h5 class="widget-amount text--danger fw-bold mt-2">
                    {{ showAmount($widget['deposit']['rejected'], currencyFormat: false) }} <span class="text-muted">{{ gs('cur_text') }}</span>
                </h5>
            </a>
        </div>
        <div class="col-sm-6 col-xxl-3">
            <a href="{{ route('user.deposit.history', ['status' => Status::PAYMENT_SUCCESS]) }}" class="dashboard-widget-main">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="text-secondary">@lang('Deposit Charge')</h6>
                    <i class="las la-percent text--dark fs-3"></i>
                </div>
                <h5 class="widget-amount text--dark fw-bold mt-2">
                    {{ showAmount($widget['deposit']['charge'], currencyFormat: false) }} <span class="text-muted">{{ gs('cur_text') }}</span>
                </h5>
            </a>
        </div>
    </div>

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
                                <label class="form-label">@lang('Trx')</label>
                                <input type="search" name="search" value="{{ request()->search }}" class="form-control form--control" placeholder="@lang('Search with trx')">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label d-block">@lang('Status')</label>
                                <select class="form-select form--control select2--dropdown" data-minimum-results-for-search="-1" name="status">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="{{ Status::PAYMENT_INITIATE }}" @selected(request()->status === (string) Status::PAYMENT_INITIATE)>@lang('Initiated')</option>
                                    <option value="{{ Status::PAYMENT_PENDING }}" @selected(request()->status === (string) Status::PAYMENT_PENDING)>@lang('Pending')</option>
                                    <option value="{{ Status::PAYMENT_SUCCESS }}" @selected(request()->status === (string) Status::PAYMENT_SUCCESS)>@lang('Completed')</option>
                                    <option value="{{ Status::PAYMENT_REJECT }}" @selected(request()->status === (string) Status::PAYMENT_REJECT)>@lang('Rejected')</option>
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
                    <div class="table-responsive">
                        <table class="table custom--table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th class="text-center">@lang('Initiated')</th>
                                    <th class="text-center">@lang('Amount')</th>
                                    <th class="text-center">@lang('Conversion')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th>@lang('Details')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <div>
                                                <span class="fw-bold">
                                                    <span class="text-primary">
                                                        @if ($deposit->method_code < 5000)
                                                            {{ __(@$deposit->gateway->name) }}
                                                        @else
                                                            @lang('Google Pay')
                                                        @endif
                                                    </span>
                                                </span>
                                                <br>
                                                <small> {{ $deposit->trx }} </small>
                                            </div>
                                        </td>

                                        <td class="text-md-center">
                                            {{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}
                                        </td>
                                        <td class="text-md-center">
                                            <div>
                                                {{ showAmount($deposit->amount) }} + <span class="text--danger" data-bs-toggle="tooltip" title="@lang('Processing Charge')">{{ showAmount($deposit->charge) }}
                                                </span>
                                                <br>
                                                <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                                </strong>
                                            </div>
                                        </td>
                                        <td class="text-md-center">
                                            <div>
                                                {{ showAmount(1) }} =
                                                {{ showAmount($deposit->rate, currencyFormat: false) }}
                                                {{ __($deposit->method_currency) }}
                                                <br>
                                                <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }}
                                                    {{ __($deposit->method_currency) }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php echo $deposit->statusBadge @endphp
                                        </td>
                                        @php
                                            $details = [];
                                            if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                                foreach (@$deposit->detail ?? [] as $key => $info) {
                                                    $details[] = $info;
                                                    if ($info->type == 'file') {
                                                        $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                                    }
                                                }
                                            }
                                        @endphp

                                        <td>
                                            @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                                <a href="javascript:void(0)" class="btn btn--base btn--sm detailBtn" data-info="{{ json_encode($details) }}" @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                                    <i class="las la-desktop"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn--success btn--sm" data-bs-toggle="tooltip" title="@lang('Automatically processed')">
                                                    <i class="las la-check"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($deposits->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($deposits) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.deposit.index') }}" class="btn btn--base btn--sm"><i class="fas fa-plus"></i>
        @lang('New Deposit')</a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush
