@php
    $elements = @getContent('brands.element', orderById: true);
@endphp

<section class="client my-120">
    <div class="container">
        <div class="client-slider">
            @foreach ($elements as $item)
                <div class="client-slider-image">
                    <img src="{{ frontendImage('brands', @$item->data_values->image, '140x35') }}" alt="img">
                </div>
            @endforeach
        </div>
    </div>
    </div>
</section>
