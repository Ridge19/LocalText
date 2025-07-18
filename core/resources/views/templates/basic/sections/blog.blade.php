@php
    $blogContent = @getContent('blog.content', true)->data_values;

    if(request()->routeIs('blog')){
        $blogElements = @getContent('blog.element');
    }else{
        $blogElements = @getContent('blog.element', limit:3);
    }
@endphp
<section class="blog py-120">
    <div class="container">
        <div class="section-heading wow fadeInDown" data-wow-duration="1">
            <p class="section-heading__name">{{ __(@$blogContent->title) }}</p>
            <h2 class="section-heading__title">{{ __(@$blogContent->heading) }}</h2>
            <p class="section-heading__desc mb-4">{{ __(@$blogContent->subheading) }}</p>
        </div>

        <div class="row gy-4">
            @foreach ($blogElements as $blogElement)
                <div class="col-lg-4 col-sm-6">
                    <div class="blog-item wow fadeInDown" data-wow-duration="1">
                        <a href="{{ route('blog.details', @$blogElement->slug) }}" class="blog-item__thumb">
                            <img src="{{ frontendImage('blog', 'thumb_' . @$blogElement->data_values->image, '420x290') }}"
                                class="fit-image" alt="image">
                        </a>
                        <div class="blog-item__content">
                            <h6 class="blog-item__title">
                                <a href="{{ route('blog.details', @$blogElement->slug) }}"
                                    class="blog-item__title-link">
                                    {{ __(@$blogElement->data_values->title) }}
                                </a>
                            </h6>
                            <p class="blog-item__desc">@php echo strLimit(strip_tags(@$blogElement->data_values->description), 100) @endphp</p>
                            <div class="blog-item-footer">
                                <a href="{{ route('blog.details', @$blogElement->slug) }}" class="blog-item-link">
                                    @lang('Read More') <span class="icon"> <i class="las la-arrow-right"></i> </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
