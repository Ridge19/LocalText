@php
    $planContent = @getContent('plan.content', true)->data_values;
@endphp

<section class="plan-section py-120" id="pricing">
    <div class="plan-section__vector">
        <img class="fit-image" src="{{ frontendImage('plan', @$planContent->background_image_left, '875x975') }}"
            alt="">
    </div>
    <div class="plan-section__vector style-right">
        <img class="fit-image" src="{{ frontendImage('plan', @$planContent->background_image_right, '740x975') }}"
            alt="">
    </div>
    <div class="container">
        <div class="section-heading wow fadeInDown" data-wow-duration="1">
            <p class="section-heading__name">{{ __(@$planContent->title) }}</p>
            <h3 class="section-heading__title">{{ __(@$planContent->heading) }}</h3>
            <p class="section-heading__desc">{{ __(@$planContent->subheading) }}</p>
        </div>
        @include('Template::partials.plan')
    </div>
</section>
