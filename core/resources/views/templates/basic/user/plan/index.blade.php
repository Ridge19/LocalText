@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom--table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Payment Type')</th>
                                    <th>@lang('Recurring Type')</th>
                                    <th>@lang('Purchase Date')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchasePlans as $key => $purchasePlan)
                                    <tr>
                                        <td>{{ __(@$purchasePlans->firstItem() + $loop->index) }}</td>
                                        <td>{{ __(@$purchasePlan->plan->name) }}</td>
                                        <td>{{ showAmount(@$purchasePlan->price) }}</td>
                                        <td>
                                            @if (@$purchasePlan->payment_type == Status::WALLET_PAYMENT)
                                                <span>@lang('Wallet Balance')</span>
                                            @else
                                                <div>
                                                    <div>@lang('Gateway Payment')</div>
                                                    <span class="text--success">
                                                        {{ @$purchasePlan->gateway->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ __(@$purchasePlan->plan->recurringtypeName) }}</td>
                                        <td>
                                            {{ showDateTime(@$purchasePlan->created_at) }}
                                            <br>
                                            {{ diffForHumans(@$purchasePlan->created_at) }}
                                        </td>
                                        <td>
                                            @if ($purchasePlan->status == Status::ENABLE)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Inactive')</span>
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
                @if ($purchasePlans->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($purchasePlans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('pricing') }}" class="btn btn--sm btn--base">@lang('Purchase Plan')</a>
@endpush
