@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-xxl-2 col-xl-4 col-lg-3">
                            <ul class="api-sidebar-menu">
                                <li class="api-sidebar-menu__item"><a href=""
                                        class="api-sidebar-menu__link">@lang('Get Started')</a>
                                    <ul class="api-sidebar-submenu">
                                        <li class="api-sidebar-submenu__item">
                                            <a href="#introduction" class="api-sidebar-submenu__link">@lang('Introduction')</a>
                                        </li>
                                        <li class="api-sidebar-submenu__item">
                                            <a href="#api-key" class="api-sidebar-submenu__link">@lang('API key')</a>
                                        </li>
                                        <li class="api-sidebar-submenu__item">
                                            <a href="#send-message-post"
                                                class="api-sidebar-submenu__link">@lang('Send Message(POST)')</a>
                                        </li>
                                        <li class="api-sidebar-submenu__item">
                                            <a href="#send-message-get"
                                                class="api-sidebar-submenu__link">@lang('Send Message(GET)')</a>
                                        </li>
                                        <li class="api-sidebar-submenu__item">
                                            <a href="#error-code" class="api-sidebar-submenu__link">@lang('Error Code')</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xxl-10 col-xl-8 col-lg-9">
                            <div class="api-content-wrapper">
                                <div class="api-docs-section" id="introduction">
                                    <div class="api-content">
                                        <h2 class="api-content__heading">@lang('INTRODUCTION')</h2>
                                        <p class="api-content__desc">
                                            <strong class="text-dark">{{ __(gs('site_name')) }}</strong> @lang('API integration is
                                                                                                                                                                                                                                                                                                                                                            pretty simple and easy. using our API you can send SMS to any mobile number. You
                                                                                                                                                                                                                                                                                                                                                            can send scheduled messages using our API. Our API is easy to implement in any other web application. Our API is
                                                                                                                                                                                                                                                                                                                                                            well-formatted, accepts GET & POST request, and returns JSON responses. All URLs are case-sensitive')
                                        </p>
                                    </div>
                                </div>
                                <div class="api-docs-section" id="api-key">
                                    <div class="api-content">
                                        <h2 class="api-content__heading">@lang('API KEY')</h2>
                                        <p class="api-content__desc">
                                            @lang('Get your API key from the below. The API key is used to authenticate the request and determines whether the request
                                                                                                                                                                                                                                                                                                                                                            is valid or not. If you want to regenerate the API key from the below sync
                                                                                                                                                                                                                                                                                                                                                            icon.')
                                        </p>
                                        <div class="form-group">
                                            <label for="" class="fw-bold">@lang('API Key')</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg--base text-white border--base c--p confirmationBtn"
                                                    data-question="@lang('Are you sure to regenerate your API key. Your old API key stop work here,if you do it!.')" data-bs-toggle="tooltip"
                                                    title="@lang('Regenerate API Key')"
                                                    data-action="{{ route('user.developer.regenerate.api.key') }}">
                                                <i class="las la-sync-alt"></i>
                                                </span>
                                                <input type="text" class="form-control copyText" readonly
                                                    value="{{ @$user->activeApiKey->key }}">
                                                <span class="input-group-text bg--base border--base c--p text-white coptBtn">
                                                    <i class="las la-copy"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="api-docs-section" id="send-message-post">
                                    <div class="api-content">
                                        <h2 class="api-content__heading">@lang('SEND SMS(POST)')</h2>
                                        <p class="api-content__desc">@lang('You will need to make request with these following API end points.')</p>
                                        <p class="api-content__desc mb-0">
                                            @lang('URL:') <span class="text-primary">{{ route('api.sms.send') }}</span>
                                        </p>
                                        <p class="api-content__desc mb-0">
                                            @lang('METHOD:') <span class="text-primary">@lang('POST')</span>
                                        </p>
                                        <p class="api-content__desc">
                                            @lang('HEADER:') <span class="text-primary">@lang('apikey')</span>
                                        </p>
                                        <p class="api-content__desc">
                                            @lang('Using this endpoint you can send SMS to multiple numbers, schedule SMS and change the default device  SIM slot. Request to the end point with the following
                                                                                                                                                                                                                                                                                                                                                            parameters below. The parameters must be send with request body & request header must be needed to valid API key.')
                                        </p>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <table class="table api-table table--responsive--lg">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('Param Name')</th>
                                                            <th>@lang('Param Type')</th>
                                                            <th>@lang('Validate')</th>
                                                            <th>@lang('Description')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>@lang('message')</td>
                                                            <td>@lang('string')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>@lang('Maximum 160 Charter')</td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('mobile_number')</td>
                                                            <td>@lang('string/number')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>
                                                                @lang('You can send sms to multiple numbers using this format') <br>
                                                                <span>@lang('+1 (695) 785-7295,+1 (695) 785-724544')</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('device')</td>
                                                            <td>@lang('string')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>
                                                                @lang('Device ID, which device to send from SMS.')
                                                                <a target="_blank"
                                                                    href="{{ route('user.device.index') }}">@lang('Device List')</a>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>@lang('device_sim')</td>
                                                            <td>@lang('number')</td>
                                                            <td>
                                                                <span class="badge badge--base">@lang('Optional')</span>
                                                            </td>
                                                            <td>
                                                                @lang('If your device has multiple SIM, then you can choose which SIM from sending SMS to. By Default send from SIM 1.')
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('date')</td>
                                                            <td>@lang('string')</td>
                                                            <td>
                                                                <span class="badge badge--base">@lang('Optional')</span>
                                                            </td>
                                                            <td>
                                                                @lang('If you also send schedule SMS. you can control when sending SMS. The date must be a future date, by default is the current DateTime.') <br>
                                                                <span>@lang('The Date format must be')
                                                                    {{ now()->format('Y-m-d h:i') }}</span>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="right-highlited mt-5">
                                                    <div class="right-highlited__heading">
                                                        <h6 class="right-highlited__title">@lang('PHP Code')</h6>
                                                        <button
                                                            class="right-highlited__button btn btn--base w-auto clipboard-btn"
                                                            data-clipboard-target="#php">
                                                            <i class="las la-copy"></i>
                                                        </button>
                                                    </div>
                                                    <pre class="mt-0 rounded-0">
                                                    <code class="language-php" id="php">
&lt;?php
    $parameters = [
        'message'       => 'your message',
        'mobile_number' => '+1 (695) 785-7295,+1 (695) 785-724544',
        'device'        => 'Your device id',
    ];

    $header = [
        'apikey:{{ @$user->activeApiKey->key }}'
    ];


    $url ={{ route('api.sms.send') }};

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
?&gt;
                                                        </code>
                                                    </pre>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="card h-100">
                                                    <div class="card-header custom--bg">
                                                        <h5 class="text-center text-white">@lang('Error Response')</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <pre class="mt-0 rounded-0 h-100">
                                                        <code class="language-php h-100">
{
    "success": false,
    "code": 422,
    "message": "Unprocessable Entity",
    "errors": [
        "The message field is required.",
        "The mobile number field is required.",
    ]
}
                                                            </code>
                                                        </pre>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="card h-100">
                                                    <div class="card-header custom--bg">
                                                        <h5 class="text-center text-white">@lang('Success Response')</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <pre class="mt-0 rounded-0 h-100">
                                                        <code class="language-php h-100">
{
    "success": true,
    "code": 200,
    "message": "1 message should be send.",
}
                                                            </code>
                                                        </pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="api-docs-section" id="send-message-get">
                                    <div class="api-content">
                                        <h2 class="api-content__heading">@lang('SEND SMS(GET)')</h2>
                                        <p class="api-content__desc">@lang('You will need to make request with these
                                                                                                                                                                                                                                                                                                                                following API end points.')</p>
                                        <p class="api-content__desc mb-0">
                                            @lang('URL:') <span
                                                class="text--base">{{ route('api.sms.send.get') }}</span>
                                        </p>
                                        <p class="api-content__desc mb-0">
                                            @lang('METHOD:') <span class="text--base">@lang('GET')</span>
                                        </p>
                                        <p class="api-content__desc">
                                            @lang('Using this endpoint you can send SMS to single numbers. Some restricted here, using this endpoint you can\'t send multiple, or schedule SMS and also you can\'t change the default device SIM slot. Request to the end point with the following
                                                                                                                                                                                                                                                                                                                                                            parameters below. The parameters must be pass as query-string via endpoint or URLs.')
                                        </p>

                                        <div class="row gy-4">
                                            <div class="col-lg-12">
                                                <table class="table api-table table--responsive--lg">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('Param Name')</th>
                                                            <th>@lang('Param Type')</th>
                                                            <th>@lang('Validate')</th>
                                                            <th>@lang('Description')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>@lang('apikey')</td>
                                                            <td>@lang('string')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>@lang('Must valid API KEY')</td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('message')</td>
                                                            <td>@lang('string')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>@lang('Maximum 160 Charter')
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('mobile_number')</td>
                                                            <td>@lang('string/number')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>
                                                                @lang('Mobile Numbers') <br>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>@lang('device')</td>
                                                            <td>@lang('string')</td>
                                                            <td>
                                                                <span class="badge badge--danger">@lang('Required')</span>
                                                            </td>
                                                            <td>
                                                                @lang('Device ID, which device to send from SMS.')
                                                                <a target="_blank"
                                                                    href="{{ route('user.device.index') }}">@lang('Device List')</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="card h-100">
                                                    <div class="card-header custom--bg">
                                                        <h5 class="text-center text-white">@lang('Error Response')</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <pre class="mt-0 rounded-0 h-100">
                                                        <code class="language-php h-100">
{
    "success": false,
    "code": 422,
    "message": "Unprocessable Entity",
    "errors": [
        "The message field is required.",
        "The mobile number field is required.",
    ]
}
                                                            </code>
                                                        </pre>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="card h-100">
                                                    <div class="card-header custom--bg">
                                                        <h5 class="text-center text-white">@lang('Success Response')</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <pre class="mt-0 rounded-0 h-100">
                                                        <code class="language-php h-100">
{
    "success": true,
    "code": 200,
    "message": "1 message should be send.",
}
                                                            </code>
                                                        </pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="api-docs-section" id="error-code">
                                    <div class="api-content">
                                        <h2 class="api-content__heading">@lang('ERROR CODE')</h2>
                                        <table class="table api-table table--responsive--lg">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Code')</th>
                                                    <th>@lang('Status')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($errorCodes as $k => $errorCode)
                                                    <tr>
                                                        <td>{{ $k }}</td>
                                                        <td>{{ __($errorCode) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="api-section"></div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/hightlight.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'dashboard/js/clipboard.min.js') }}"></script>

    <script>
        (function($) {
            $('.regerate-token').on('click', function(e) {
                alert('ok')
            });

            $('.coptBtn').on('click', function(e) {
                var copyText = $(this).siblings('.copyText')[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                copyText.blur();
                $(this).addClass('copied');
                setTimeout(() => {
                    $(this).removeClass('copied');
                }, 1500);
            });

            new ClipboardJS('.clipboard-btn');

            $(".clipboard-btn").on('click', function(e) {
                $(this).addClass('copied');
                setTimeout(() => {
                    $(this).removeClass('copied');
                }, 1500);
            });

        })(jQuery)
    </script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'dashboard/css/hightlight.css') }}">
@endpush

@push('style')
    <style>
        .custom--bg {
            background-color: #314459;
        }

        .body-wrapper {
            overflow: unset !important;
        }

        .api-sidebar-menu {
            position: sticky;
            top: 80px;
            line-height: 2rem;
            padding-inline: 16px;
        }

        .coptBtn,
        .confirmationBtn {
            cursor: pointer;
        }

        .api-content__heading {
            font-size: 24px !important;
            padding-block: 16px;
            margin-bottom: 16px;
        }

        .api-content__desc * {
            line-break: anywhere;
        }
    </style>
@endpush
