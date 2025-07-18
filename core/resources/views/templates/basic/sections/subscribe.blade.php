@php
    $subscribeContent = @getContent('subscribe.content', true)->data_values;
@endphp
<section class="newsletter-section">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-xl-5 col-lg-6">
                <div class="newsletter-content wow fadeInLeft" data-wow-duration="1">
                    <h4 class="newsletter-content__title">{{ __(@$subscribeContent->heading) }}</h4>
                    <p class="newsletter-content__text">{{ __(@$subscribeContent->subheading) }}</p>
                </div>
            </div>
            <div class="col-xl-7 col-lg-6">
                <form action="#" class="newsletter-form wow fadeInRight" data-wow-duration="1">
                    <div class="input-group">
                        <input type="email" class="form-control form--control" placeholder="@lang('Enter your email')"
                            required>
                        <button type="submit" class="input-group-text btn btn--gr"> <span class="icon"><i class="far fa-paper-plane"></i></span>{{ __(@$subscribeContent->button_text) }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('script-lib')
    <script>
        "use strict";

        (function($) {
            let form = $('.newsletter-form');
            let isSubmitting = false;
            form.on('submit', function(e) {
                e.preventDefault();

                if (isSubmitting) {
                    return;
                }

                let email = form.find('input').val();
                let token = '{{ csrf_token() }}';
                let url = '{{ route('subscribe') }}'

                let data = {
                    email: email,
                    _token: token
                }

                isSubmitting = true;
                $.post(url, data, function(response) {
                    if (response.success) {
                        notify('success', response.message);
                        $(form).trigger('reset');
                    } else {
                        notify('error', response.message);
                    }
                }).always(function() {
                    isSubmitting = false;
                });
            });
        })(jQuery);
    </script>
@endpush
