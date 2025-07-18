@php
    $authContent = @getContent('auth.content', true)->data_values;
    $registerContent = @getContent('register.content', true)->data_values;
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @if (gs('registration'))
        <div class="account-section">
            <div class="account-thumb">
                <img class="fit-image" src="{{ frontendImage('auth', $authContent->auth_image,'1060x1080') }}" alt="tree">
            </div>
            <div class="account-content">
                <form action="{{ route('user.register') }}" method="POST" class="account-form verify-gcaptcha disableSubmission">
                    @csrf
                    <a href="{{ route('home') }}" class="account-form__logo mb-60">
                        <img class="fit-image" src="{{ siteLogo() }}" alt="logo">
                    </a>

                    <div class="account-heading">
                        <p class="account-heading__text">{{ __(@$registerContent->title) }}</p>
                        <h6 class="account-heading__title">{{ __(@$registerContent->heading) }}</h6>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-12 col-xl-6">
                            <div class="form-group">
                                <label class="form--label">@lang('First Name')</label>
                                <input class="form--control" name="firstname" type="text" placeholder="@lang('First Name')" required>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-12 col-xl-6">
                            <div class="form-group">
                                <label class="form--label">@lang('Last Name')</label>
                                <input class="form--control" name="lastname" type="text" placeholder="@lang('Last Name')" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form--label">@lang('Email')</label>
                                <input class="form--control checkUser" name="email" type="email" placeholder="@lang('Enter email address')" required>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-12 col-xl-6">
                            <div class="form-group">
                                <label class="form--label" for="password">@lang('Password')</label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" class="form--control @if (gs('secure_password')) secure-password @endif" placeholder="@lang('Enter password')" required>
                                    <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-12 col-xl-6">
                            <div class="form-group">
                                <label class="form--label" for="password">@lang('Confirm Password')</label>
                                <div class="position-relative">
                                    <input type="password" id="confirm-password" name="password_confirmation" class="form--control" placeholder="@lang('Confirm password')" required>
                                    <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#confirm-password"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-captcha />

                    @if (gs('agree'))
                        @php
                            $policyPages = getContent('policy_pages.element', false, orderById: true);
                        @endphp
                        <div class="form-group">
                            <input type="checkbox" id="agree" @checked(old('agree')) name="agree" required>
                            <label for="agree">@lang('I agree with')</label> <span>
                                @foreach ($policyPages as $policy)
                                    <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank">{{ __($policy->data_values->title) }}</a>
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </span>
                        </div>
                    @endif

                    <button type="submit" class="btn btn--base w-100">@lang('Sign Up Account')</button>

                    <p class="account-external mt-3">@lang('Already have an account ?')
                        <a href="{{ route('user.login') }}" class="text--base">@lang('Sign In')</a>
                    </p>
                </form>
            </div>
        </div>

        <div class="modal custom--modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h6>
                        <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">@lang('You already have an account. Please Login ')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                        <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif
@endsection

@if (gs('registration'))

    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('user.checkUser') }}';
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data != false) {
                            $('#existModalCenter').modal('show');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush

@endif
