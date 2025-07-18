@php
    $serviceContent = @getContent('service.content', true)->data_values;
    $serviceElements = @getContent('service.element', orderById: true);
@endphp
<section class="service-section py-120" id="service">
    <div class="container">
        <div class="section-heading wow fadeInDown" data-wow-duration="1">
            <p class="section-heading__name">{{ __(@$serviceContent->title) }}</p>
            <h3 class="section-heading__title">{{ __(@$serviceContent->heading) }}</h3>
            <p class="section-heading__desc">{{ __(@$serviceContent->subheading) }}</p>
        </div>

        <div class="row service-card-wrapper justify-content-center gy-4">
            @foreach ($serviceElements as $serviceElement)
                <div class="col-lg-4 col-md-6">
                    <div class="service-card wow fadeInDown" data-wow-duration="1">
                        <span class="service-card__icon">
                            <img src="{{ frontendImage('service', $serviceElement->data_values->icon_image, '100x100') }}"
                                alt="message">
                        </span>
                        <h6 class="service-card__title"> {{ __(@$serviceElement->data_values->title) }} </h6>

                        <p class="service-card__desc">{{ __(@$serviceElement->data_values->description) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
