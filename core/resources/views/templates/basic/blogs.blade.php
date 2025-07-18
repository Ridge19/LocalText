@php
    $contactContent = @getContent('contact.content', true)->data_values;
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @include('Template::sections.blog')

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection
