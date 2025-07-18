@php
    $socialIcons = @getContent('social_icon.element', orderById: true);
    $footerContent = @getContent('footer.content', true)->data_values;
    $contactContent = @getContent('contact.content', true)->data_values;
    $policyPages = @getContent('policy_pages.element', false, null, true);
@endphp

<footer class="footer-area py-120">
    <div class="footer-area__shape">
        <img src="{{ getImage($activeTemplateTrue . 'images/footer-shape.png', '110x95') }}" alt="shape">
    </div>
    <div class="footer-area__shape style-two">
        <img src="{{ getImage($activeTemplateTrue . 'images/footer-shape2.png', '180x100') }}" alt="shape">
    </div>
    <div class="container">
        <div class="row justify-content-between gy-5">
            <div class="col-xl-5 col-md-12">
                <div class="footer-item wow fadeInLeft" data-wow-duration="1">
                    <a href="{{ route('home') }}" class="footer-item__logo">
                        <img src="{{ siteLogo('dark') }}" alt="logo">
                    </a>
                    <p class="footer-item__desc">{{ __(@$footerContent->short_description) }}</p>

                    <ul class="social-list">
                        @foreach ($socialIcons as $socialIcon)
                            <li class="social-list__item">
                                <a href="{{ $socialIcon->data_values->url }}" target="_blank" class="social-list__link">
                                    @php
                                        echo $socialIcon->data_values->social_icon;
                                    @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-xl-7 col-md-12">
                <div class="row justify-content-between">
                    <div class="col-md-4 col-sm-6 col-xsm-6">
                        <div class="footer-item wow fadeInDown" data-wow-duration="1">
                            <h5 class="footer-item__title">
                                @lang('Quick Links')
                            </h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item"><a href="{{ route('blog') }}" class="footer-menu__link"><i
                                            class="las la-angle-double-right"></i> @lang('Blog')</a></li>
                                <li class="footer-menu__item"><a href="{{ route('pricing') }}"
                                        class="footer-menu__link"><i class="las la-angle-double-right"></i>
                                        @lang('Pricing')</a></li>
                                <li class="footer-menu__item"><a href="{{ route('contact') }}"
                                        class="footer-menu__link"><i class="las la-angle-double-right"></i>
                                        @lang('Contact')</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xsm-6">
                        <div class="footer-item wow fadeInDown" data-wow-duration="1">
                            <h5 class="footer-item__title">
                                @lang('Privacy & Policy')
                            </h5>
                            <ul class="footer-menu">
                                @foreach ($policyPages as $policy)
                                    <li class="footer-menu__item">
                                        <a class="footer-menu__link"
                                            href="{{ route('policy.pages', $policy->slug) }}"><i
                                                class="las la-angle-double-right"></i>{{ __($policy->data_values->title) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xsm-6">
                        <div class="footer-item wow fadeInDown" data-wow-duration="1">
                            <h5 class="footer-item__title">
                                @lang('Contact Info')
                            </h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item">
                                    <a href="https://www.google.com/maps?q={{ urlencode(@$contactContent->address) }}"
                                        target="_blank" class="footer-menu__link"><i class="las la-map-marker-alt"></i>
                                        {{ @$contactContent->address }}</a>
                                </li>
                                <li class="footer-menu__item"><a
                                        href="tel:{{ @$contactContent->primary_phone_number }}"
                                        class="footer-menu__link"><i class="las la-phone"></i>
                                        {{ @$contactContent->primary_phone_number }}</a></li>
                                <li class="footer-menu__item"><a
                                        href="mailto:{{ @$contactContent->primary_email_address }}"
                                        class="footer-menu__link"><i class="las la-envelope"></i>
                                        {{ @$contactContent->primary_email_address }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="bottom-footer py-3">
    <div class="container">
        <div class="row gy-3">
            <div class="col-md-12 text-center wow fadeInDown" data-wow-duration="1">
                <div class="bottom-footer-text">@lang('Copyright') &copy; {{ date('Y') }} <a
                        href="{{ route('home') }}"
                        class="text-white text-decoration-underline">{{ __(gs('site_name')) }}</a> @lang('All Rights Reserved.')
                </div>
            </div>
        </div>
    </div>
</div>
