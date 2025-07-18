@php
    $plans = App\Models\Plan::active()->orderBy('price', 'asc')->get();
    $user = auth()->user();
    $userBalance = $user->balance ?? 00;
@endphp

<div class="row gy-4 justify-content-center">
    @foreach ($plans as $plan)
        <div class="col-md-6 col-xl-3">
            <div class="plan-card wow fadeInDown" data-wow-duration="1">
                <div class="plan-card__header">
                    <div class="plan-card__top">
                        <span class="plan-card__icon">
                            <img src="{{ getImage(getFilePath('plan') . '/' . @$plan->image, getFileSize('plan')) }}"
                                alt="image" class="plugin_bg">
                        </span>
                        <h4 class="plan-card__price">
                            {{ gs('cur_sym') . showAmount($plan->price, currencyFormat: false) }}
                        </h4>
                    </div>
                    <div class="plan-card__content">
                        <p class="plan-card__title">{{ __($plan->name) }}</p>
                        <p class="plan-card__subtitle">{{ __($plan->title) }}</p>
                    </div>
                </div>

                <div class="plan-card-info">
                    <ul class="plan-card-info__list">
                        <li class="plan-card-info__item"> <span class="icon"> <i class="fas fa-check"></i>
                            </span>
                            @lang('Recurring Type') : <span class="text--base">{{ @$plan->recurringtypeName }}</span>
                        </li>
                        <li class="plan-card-info__item"> <span class="icon"> <i class="fas fa-check"></i>
                            </span>
                            @lang('Device Limit') :
                            @if ($plan->device_limit == Status::UNLIMITED)
                                @lang('Unlimited')
                            @else
                                {{ $plan->device_limit }}
                            @endif
                        </li>
                        <li class="plan-card-info__item"> <span class="icon"> <i class="fas fa-check"></i>
                            </span>
                            @lang('SMS Limit') :
                            @if ($plan->sms_limit == Status::UNLIMITED)
                                @lang('Unlimited')
                            @else
                                {{ $plan->sms_limit }}
                            @endif
                        </li>
                        <li class="plan-card-info__item"> <span class="icon"> <i class="fas fa-check"></i>
                            </span>
                            @lang('Contact Limit') :
                            @if ($plan->contact_limit == Status::UNLIMITED)
                                @lang('Unlimited')
                            @else
                                {{ $plan->contact_limit }}
                            @endif
                        </li>
                        <li class="plan-card-info__item"> <span class="icon"> <i class="fas fa-check"></i>
                            </span>
                            @lang('Group Limit') :
                            @if ($plan->group_limit == Status::UNLIMITED)
                                @lang('Unlimited')
                            @else
                                {{ $plan->group_limit }}
                            @endif
                        </li>
                        <li class="plan-card-info__item"> <span class="icon"> <i class="fas fa-check"></i>
                            </span>
                            @lang('Daily SMS Limit') :
                            @if ($plan->daily_sms_limit == Status::UNLIMITED)
                                @lang('Unlimited')
                            @else
                                {{ $plan->daily_sms_limit }}
                            @endif
                        </li>
                        <li class="plan-card-info__item">
                            @if ($plan->scheduled_sms)
                                <span class="icon">
                                    <i class="fas fa-check"></i>
                                </span>
                                @lang('Scheduled SMS') : @lang('Yes')
                            @else
                                <span class="icon close">
                                    <i class="fas fa-close"></i>
                                </span>
                                @lang('Scheduled SMS') : @lang('No')
                            @endif
                        </li>
                        <li class="plan-card-info__item">
                            @if ($plan->campaign)
                                <span class="icon">
                                    <i class="fas fa-check"></i>
                                </span>
                                @lang('Campaign') : @lang('Yes')
                            @else
                                <span class="icon close">
                                    <i class="fas fa-close"></i>
                                </span>
                                @lang('Campaign') : @lang('No')
                            @endif
                        </li>
                        <li class="plan-card-info__item">
                            @if ($plan->api_available)
                                <span class="icon">
                                    <i class="fas fa-check "></i>
                                </span>
                                @lang('API Available') : @lang('Yes')
                            @else
                                <span class="icon close"> <i class="fas fa-close"></i></span>
                                @lang('API Available') : @lang('No')
                            @endif
                        </li>
                    </ul>
                </div>

                <div class="plan-card__button">
                    @guest
                        <a href="{{ route('user.login') }}" class="btn plan--btn pill w-100">
                            @lang('Purchase Now')
                        </a>
                    @else
                        <button class="btn plan--btn purchase-btn pill" data-plan='@json($plan)'
                            @disabled($user->plan_id == $plan->id)>
                            @lang('Purchase Now')
                        </button>
                    @endguest
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('modal')
    <div class="modal custom--modal fade modal-lg" id="purchaseModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Purchase Plan')</h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <form method="post" action="{{ route('user.purchase.plan.insert') }}">
                        @csrf
                        <input type="hidden" name="plan_id">
                        <ul class="list-group mb-3 mt-2 list-group-flush">
                            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                <span>@lang('Plan Name '):</span>
                                <span class="plan-name"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                <span>@lang('Plan Price '):</span>
                                <span>{{ gs('cur_sym') }}<span class="plan-price"></span></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                <span>@lang('Recurring Type '):</span>
                                <span class="plan-recurring-type"></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between flex-wrap gap-2 px-0">
                                <span>@lang('You have to pay '):</span>
                                <span>{{ gs('cur_sym') }}<span class="plan-price"></span></span>
                            </li>
                        </ul>
                        <div class="plan-pay-options mb-2">
                            <h6 class="title mb-3">@lang('Payment Via')</h6>
                            <div class="option-box gap-2">
                                <label for="wallet" class="option-item col-md-6">
                                    <i class="las la-wallet"></i>
                                    <span>@lang('Wallet Balance')</span>
                                    <p class="option-desc">@lang('Payment completed instantly with one click if sufficient balance is available')</p>
                                    <input hidden type="radio" id="wallet" name="payment_type"
                                        value="{{ Status::WALLET_PAYMENT }}">
                                    <span class="active--badge">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </label>
                                <label for="gateway" class="option-item col-md-6">
                                    <i class="las la-credit-card"></i>
                                    <span>@lang('Payment Gateway')</span>
                                    <p class="option-desc">@lang('Multiple gateways for ensuring hassle-free payment process')</p>
                                    <input hidden type="radio" id="gateway" name="payment_type"
                                        value="{{ Status::GATEWAY_PAYMENT }}">
                                    <span class="active--badge">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-3 float-end">
                            <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">
                                @lang('Cancel')
                            </button>
                            <button type="submit" class="btn btn--base btn--sm confirmBtn">
                                @lang('Confirm')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var $modal = $('#purchaseModal');
            var $modalForm = $modal.find('form');
            var $confirmBtn = $modal.find('.confirmBtn');

            $('.purchase-btn').on('click', function() {
                var plan = $(this).data('plan');
                var recurring_type = plan.recurring_type == '{{ Status::YEARLY_PLAN }}' ? 'yearly' : 'monthly';
                $modalForm.find('input[name=plan_id]').val(plan.id);
                $modal.find('.plan-name').text(plan.name);
                $modal.find('.plan-price').text(getAmount(plan.price));
                $modal.find('.plan-recurring-type').text(recurring_type);
                $modal.modal('show');

                $modalForm.trigger('reset');
            });

            $confirmBtn.on('click', function() {
                var paymentType = $('input[name=payment_type]:checked').val();
                if (!paymentType) {
                    notify('error', "@lang('Please select a payment method')");
                    return false;
                }
                if (paymentType == '{{ Status::WALLET_PAYMENT }}') {
                    if (checkUserBalance(plan.price) == false) {
                        $modal.modal('hide');
                        return false;
                    }
                }
                $modalForm.submit();
            });

            function checkUserBalance(amount) {
                let userBalance = parseFloat('{{ $userBalance }}');
                if (amount > userBalance) {
                    notify('error', "@lang('Insufficient balance')");
                    return false;
                }
            }
            // Get Amount With Precision
            const getAmount = (amount, precision = null) => {
                const allowPrecision = precision || 2;
                return parseFloat(amount).toFixed(allowPrecision);
            }
        })(jQuery);
    </script>
@endpush
