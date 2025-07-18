@php
    $authContent = @getContent('auth.content', true)->data_values;
    $loginContent = @getContent('login.content', true)->data_values;
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="account-section">
        <div class="account-thumb">
            <img class="fit-image" src="{{ frontendImage('auth', $authContent->auth_image,'1060x1080') }}" alt="tree">
        </div>
        <div class="account-content">
            <form action="{{ route('user.login') }}" method="POST" class="account-form verify-gcaptcha disableSubmission">
                @csrf
                <a href="{{ route('home') }}" class="account-form__logo mb-60">
                    <img class="fit-image" src="{{ siteLogo() }}" alt="logo">
                </a>

                <div class="account-heading">
                    <p class="account-heading__text">{{ __(@$loginContent->title) }}</p>
                    <h6 class="account-heading__title">{{ __(@$loginContent->heading) }}</h6>
                </div>

                <div class="form-group">
                    <label class="form--label">@lang('Username')</label>
                    <input class="form--control" name="username" placeholder="@lang('Username or email')" type="text" required>
                </div>

                <div class="form-group">
                    <label class="form--label" for="password">@lang('Password')</label>
                    <div class="position-relative">
                        <input type="password" id="password" name="password" class="form--control"
                            placeholder="@lang('Enter your password')" required>
                        <span class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password"></span>
                    </div>
                </div>

                <div class="flex-between mb-4">
                    <div class="form--check">
                        <input class="form-check-input" type="checkbox" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <div class="form-check-label">
                            <label class="" for="remember">@lang('Remember Me')</label>
                        </div>
                    </div>
                    <a href="{{ route('user.password.request') }}" class="forgot-password">@lang('Forget Password?')</a>
                </div>

                <x-captcha />

                <button type="submit" class="btn btn--base w-100">@lang('Sign In Account')</button>

                <p class="account-external mt-3">@lang('Don\'t Have On Account Yet?') <a href="{{ route('user.register') }}"
                        class="text--base">@lang('Create Account')</a></p>

            </form>
        </div>
    </div>
@endsection

@push('script-lib')
@endpush
