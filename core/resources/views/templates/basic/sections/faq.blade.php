@php
    $faqContent = @getContent('faq.content', true)->data_values;
    $faqElements = @getContent('faq.element', orderById: true);
@endphp
<section class="faq-section py-120" id="faq">
    <div class="container">
        <div class="row gy-4 justify-content-between align-items-center">
            <div class="col-lg-5">
                <div class="faq-thumb text-center wow fadeInLeft" data-wow-duration="1">
                    <img src="{{ frontendImage('faq', @$faqContent->faq_image, '1050x1060') }}" alt="faq">
                </div>
            </div>
            <div class="col-lg-6 wow fadeInRight" data-wow-duration="1">
                <div class="section-heading style-left">
                    <p class="section-heading__name">{{ __(@$faqContent->title) }}</p>
                    <h3 class="section-heading__title">{{ __(@$faqContent->heading) }}</h3>
                    <p class="section-heading__desc">{{ __(@$faqContent->subheading) }}</p>
                </div>
                <div class="faq--accordion accordion" id="faqAccordion">
                    @foreach ($faqElements as $faqElement)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="collapse{{ $loop->index }}">
                                    {{ __($faqElement->data_values->question) }}
                                </button>
                            </h2>
                            <div id="collapse{{ $loop->index }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>{{ __($faqElement->data_values->answer) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>
