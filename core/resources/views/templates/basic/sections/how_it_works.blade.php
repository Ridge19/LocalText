@php
    $content = getContent('how_it_works.content', true);
    $elements = getContent('how_it_works.element', limit: 4, orderById: true);
@endphp
<section class="py-120 work-section">
    <div class="container">
        <div class="section-heading wow fadeInDown" data-wow-duration="1">
            <p class="section-heading__name">{{ __(@$content->data_values->title) }}</p>
            <h3 class="section-heading__title">{{ __(@$content->data_values->heading) }}</h3>
            <p class="section-heading__desc">{{ __(@$content->data_values->subheading) }}</p>
        </div>

        <div class="row gy-4 howwork-wrapper">
            @foreach ($elements as $element)
                <div class="col-xsm-6 col-sm-6 col-lg-3">
                    <div class="howwork-item wow fadeInDown" data-wow-duration="1">
                        <div class="howwork-item__count">
                            <span class="inner-count">{{ $loop->iteration }}</span>
                        </div>
                        <h6 class="howwork-item__title">{{ __($element->data_values->title) }}</h6>
                        <p class="howwork-item__desc">{{ __($element->data_values->description) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
