@php
    $featureContent = @getContent('feature.content', true)->data_values;
    $featureElements = @getContent('feature.element', orderById: true);
@endphp
<section class="feature-section py-120" id="feature">
    <div class="container">
        <div class="section-heading wow fadeInDown" data-wow-duration="1">
            <p class="section-heading__name"> {{ __(@$featureContent->title) }} </p>
            <h3 class="section-heading__title">{{ __(@$featureContent->heading) }}</h3>
            <p class="section-heading__desc">{{ __(@$featureContent->subheading) }}</p>
        </div>

        <div class="row gy-4 justify-content-center">
            @foreach ($featureElements as $featureElement)
                <div class="col-xl-3 col-lg-4 col-sm-6 col-xsm-6">
                    <div class="feature-card h-100 wow fadeInDown" data-wow-duration="1">
                        <div class="feature-card__icon">
                            <img src="{{ frontendImage('feature', $featureElement->data_values->icon_image, '75x75') }}"
                                alt="icon">
                        </div>
                        <h6 class="feature-card__title">{{ __(@$featureElement->data_values->title) }}</h6>
                        <p class="feature-card__desc">{{ __(@$featureElement->data_values->description) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
