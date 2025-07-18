@php
    $content = getContent('api_integration.content', true);
    $elements = getContent('api_integration.element', limit: 3, orderById: true);
@endphp

<section class="api-section my-120">
    <div class="container">
        <div class="row align-items-center gy-4 mb-60">
            <div class="col-lg-6">
                <div class="api-section-thumb pe-xl-5 wow fadeInLeft" data-wow-duration="1">
                    <img class="fit-image"
                        src="{{ frontendImage('api_integration', @$content->data_values->image, '1200x1200') }}"
                        alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-heading style-left wow fadeInRight" data-wow-duration="1">
                    <p class="section-heading__name">{{ __(@$content->data_values->title) }}</p>
                    <h3 class="section-heading__title">{{ __(@$content->data_values->heading) }}</h3>
                    <p class="section-heading__desc">{{ __(@$content->data_values->subheading) }}</p>
                </div>

                <a href="{{ @$content->data_values->button_url }}" class="btn btn--gr pill">
                    {{ __(@$content->data_values->button_text) }}
                </a>
            </div>
        </div>
        <div class="row gy-4">
            @foreach ($elements as $item)
                <div class="col-lg-4">
                    <div class="api-card wow fadeInDown" data-wow-duration="1">
                        <span class="img">
                            <img src="{{ frontendImage('api_integration', $item->data_values->icon_image, '100x100') }}"
                                alt="icon">
                        </span>

                        <span class="api-card__contet">
                            <h6 class="title">{{ __($item->data_values->title) }}</h6>
                            <p class="desc">{{ __($item->data_values->description) }}</p>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
