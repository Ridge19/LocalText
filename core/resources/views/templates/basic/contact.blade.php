@php
    $contactContent = @getContent('contact.content', true)->data_values;
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="py-120 section--bg">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4 col-sm-6 col-xsm-6">
                    <div class="contact-info-card  wow fadeInDown" data-wow-duration="1">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-card__content">
                            <h6 class="contact-info-card__title">@lang('Office Address')</h6>
                            <a href="javascript:void(0)" class="contact-info-card__link">
                                {{ @$contactContent->address }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xsm-6">
                    <div class="contact-info-card wow fadeInDown" data-wow-duration="1">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-card__content">
                            <h6 class="contact-info-card__title">@lang('Email Address')</h6>
                            <a href="mailto:{{ @$contactContent->primary_email_address }}"
                                class="contact-info-card__link">{{ @$contactContent->primary_email_address }}</a>
                            <a href="mailto:{{ @$contactContent->secondary_email_address }}"
                                class="contact-info-card__link">{{ @$contactContent->secondary_email_address }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-info-card wow fadeInDown" data-wow-duration="1">
                        <div class="contact-info-card__icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info-card__content">
                            <h6 class="contact-info-card__title">@lang('Phone Number')</h6>
                            <a href="tel:{{ @$contactContent->primary_phone_number }}"
                                class="contact-info-card__link">{{ @$contactContent->primary_phone_number }}</a>
                            <a href="tel:{{ @$contactContent->secondary_phone_number }}"
                                class="contact-info-card__link">{{ @$contactContent->secondary_phone_number }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="py-120 contact-section">
        <div class="container">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="contact-image wow fadeInLeft" data-wow-duration="1">
                        <img src="{{ frontendImage('contact', @$contactContent->image, '1100x1110') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-form  wow fadeInRight" data-wow-duration="1">
                        <h5 class="contact-form__title">{{ __(@$contactContent->form_title) }}</h5>
                        <form action="" method="POST" class="verify-gcaptcha">
                            @csrf
                            <div class="row g-3 g-xl-4">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="form--label">@lang('Name')</label>
                                        <input type="text" class="form--control" name="name"
                                            placeholder="@lang('Name')" value="{{ old('name', @$user->fullname) }}"
                                            @if ($user && $user->profile_complete) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="form--label">@lang('Email')</label>
                                        <input type="email" class="form--control" name="email"
                                            placeholder="@lang('Email Adress')" value="{{ old('email', @$user->email) }}"
                                            @if ($user) readonly @endif required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group mb-0">
                                        <label class="form--label">@lang('Subject')</label>
                                        <input type="text" class="form--control" name="subject"
                                            placeholder="@lang('Subject')" value="{{ old('subject') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group mb-0">
                                        <label class="form--label">@lang('Message')</label>
                                        <textarea name="message" class="form--control" name="message" placeholder="@lang('Write Your Text')"> {{ old('message') }} </textarea>
                                    </div>
                                </div>

                                <x-captcha />

                                <div class="col-lg-12">
                                    <button class="btn btn--gr pill">{{ __(@$contactContent->form_button_text) }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="contact-map">
        <iframe src="{{ @$contactContent->location_map_url }}" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
