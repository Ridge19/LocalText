@php
    $content = getContent('testimonial.content', true);
    $elements = getContent('testimonial.element');
@endphp

<section class="testimonials py-120">
    <div class="container">
        <div class="section-heading wow fadeInDown" data-wow-duration="1">
            <p class="section-heading__name">{{ __(@$content->data_values->title) }}</p>
            <h3 class="section-heading__title">{{ __(@$content->data_values->heading) }}</h3>
            <p class="section-heading__desc">{{ __(@$content->data_values->subheading) }}</p>
        </div>
        <div class="testimonial-slider">
            @foreach ($elements as $item)
                <div class="testimonial-item wow fadeInDown" data-wow-duration="1">
                    <q class="testimonial-item__desc">{{ __($item->data_values->quote) }}</q>

                    <div class="testimonial-item__info">
                        <div class="testimonial-item__thumb">
                            <img src="{{ frontendImage('testimonial', $item->data_values->image, '100x100') }}"
                                class="fit-image" alt="">
                        </div>
                        <div class="testimonial-item__details">
                            <h6 class="testimonial-item__name">{{ __($item->data_values->name) }}</h6>
                            <span class="testimonial-item__designation">{{ __($item->data_values->designation) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
