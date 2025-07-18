@php
    $ctaContent = @getContent('cta.content', true)->data_values;
    $ctaElements = @getContent('cta.element', orderById: true);
@endphp

<section class="cta-section my-120" id="app">
    <div class="container">
        <div class="cta-wrapper">
            <div class="row align-items-center justify-content-between gy-4">
                <div class="col-lg-6">
                    <div class="cta-content wow fadeInLeft" data-wow-duration="1">
                        <div class="section-heading style-left">
                            <p class="section-heading__name">{{ __(@$ctaContent->title) }}</p>
                            <h3 class="section-heading__title">{{ __(@$ctaContent->heading) }}</h3>
                            <p class="section-heading__desc">{{ __(@$ctaContent->subheading) }}</p>
                        </div>

                        <div class="cta-download-wrapper wow fadeInRight" data-wow-duration="1">
                            <a target="_blank" href="{{ @$ctaContent->playstore_url }}" class="cta-download playstore">
                                <div class="cta-download__icon">
                                    <img src="{{ getImage($activeTemplateTrue . 'images/playstore.png', '32x33') }}"
                                        alt="playstore">
                                </div>
                                <div class="cta-download__content">
                                    <p class="cta-download__title">@lang('GET IN NOW')</p>
                                    <p class="cta-download__name">@lang('Google Play')</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="cta-thumb wow fadeInRight" data-wow-duration="1">
                        <img src="{{ frontendImage('cta', @$ctaContent->cta_image, '800x800') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
