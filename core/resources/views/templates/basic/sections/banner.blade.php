@php
    $bannerContent = @getContent('banner.content', true)->data_values;
@endphp
<section class="banner-section">
    <div class="banner-ring"></div>
    <div class="container">
        <div class="row align-items-center gx-5">
            <div class="col-lg-6">
                <div class="banner-content wow fadeInLeft" data-wow-duration="1">
                    <p class="banner-content__subtitle">{{ __(@$bannerContent->title) }}</p>
                    <h1 class="banner-content__title">{{ __(@$bannerContent->heading) }}</h1>
                    <p class="banner-content__text">{{ __(@$bannerContent->subheading) }}</p>
                    <div class="banner-content__button">
                        <a href="{{ @$bannerContent->left_button_link }}"
                            class="btn btn-outline--base pill">{{ __(@$bannerContent->left_button_text) }}</a>
                        <a href="{{ @$bannerContent->right_button_link }}"
                            class="btn btn--gr pill">{{ __(@$bannerContent->right_button_text) }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="banner-image wow fadeInRight" data-wow-duration="1">
                    <img src="{{ frontendImage('banner', @$bannerContent->thumbnail_image, '1270x1340') }}"
                        alt="banner">
                </div>
            </div>
        </div>
    </div>
</section>
