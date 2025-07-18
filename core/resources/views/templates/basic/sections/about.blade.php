@php
    $aboutContent = @getContent('about.content', true)->data_values;
    $aboutElements = @getContent('about.element', orderById: true);
@endphp
<section class="about-us py-120" id="about">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6">
                <div class="about-thumb wow fadeInLeft" data-wow-duration="1">
                    <img class="fit-image" src="{{ frontendImage('about', @$aboutContent->about_image, '1130x1000') }}"
                        alt="about-thumb">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content wow fadeInRight" data-wow-duration="1">
                    <div class="section-heading  style-left">
                        <h3 class="section-heading__name">{{ __(@$aboutContent->title) }}</h3>
                        <h3 class="section-heading__title">{{ __(@$aboutContent->heading) }}</h3>
                        <p class="section-heading__desc">{{ __(@$aboutContent->subheading) }}</p>
                        <p class="section-heading__desc mt-3">{{ __(@$aboutContent->description) }}</p>
                    </div>


                    <div class="about-content__button">
                        <a href="{{ @$aboutContent->button_url }}"
                            class="btn btn--gr pill">{{ __(@$aboutContent->button_text) }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
