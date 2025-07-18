@php
    $languages = App\Models\Language::all();
@endphp
<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-xl" id="navbar-example2">
            <a class="navbar-brand logo m-0" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt=""></a>
            <button class="navbar-toggler header-button" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>
            <div class="offcanvas border-0  offcanvas-start" tabindex="-1" id="offcanvasDarkNavbar">
                <div class="offcanvas-header">
                    <a class="logo navbar-brand" href="{{ route('home') }}"><img src="{{ siteLogo('dark') }}"
                            alt=""></a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav w-100 justify-content-xl-center nav-menu align-items-xl-center">

                        @if (gs('multi_language'))
                            <li class="dropdown lang-box d-xl-none nav-item">
                                <button class="lang-box-btn" data-bs-toggle="dropdown">
                                    <span class="thumb">
                                        <img src="{{ asset('assets/images/language/' . getActiveLang()->image) }}"
                                            alt="language">
                                    </span>
                                    <span class="content">
                                        <span class="text">{{ __(getActiveLang()->name) }}</span>
                                        <span class="arrow">
                                            <i class="las la-angle-down"></i>
                                        </span>
                                    </span>
                                </button>

                                <ul class="dropdown-menu lang-box-menu">
                                    @foreach ($languages->where('code', '!=', getActiveLang()->code) as $language)
<li class="lang-box-item">
                                            <a href="{{ route('lang', $language->code) }}" class="lang-box-link">
                                                <div class="thumb">
                                                    <img src="{{ asset('assets/images/language/' . $language->image) }}"
                                                        alt="language">
                                                </div>
                                                <span class="text">{{ __($language->name) }}</span>
                                            </a>
                                        </li>
@endforeach
                                </ul>
                            </li>
@endif

                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('home') }}" aria-current="page"
                                href="{{ route('home') }}">@lang('Home')</a>
                        </li>

                        @foreach ($pages as $k => $page)
<li class="nav-item">
                                <a class="nav-link {{ menuActive('pages', [$page->slug]) }}"
                                    href="{{ route('pages', [$page->slug]) }}">{{ __(@$page->name) }}</a>
                            </li>
@endforeach

                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('pricing') }}" href="{{ route('pricing') }}">@lang('Pricing')</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('blog') }}" href="{{ route('blog') }}">@lang('Blog')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ menuActive('contact') }}" href="{{ route('contact') }}">@lang('Contact')</a>
                        </li>
                        @auth
                            <li class="nav-item d-xl-none">
                                <div class="header-right d-xl-none">
                                    <div class="header-button flex-align gap-3">
                                        <a href="{{ route('user.home') }}"
                                            class="w-100 btn btn--gr pill">@lang('Dashboard')</a>
                                    </div>
                                </div>
                            </li>
@else
<li class="nav-item d-xl-none">
                                <div class="header-right d-xl-none">
                                    <div class="header-button flex-align gap-3">
                                        <a href="{{ route('user.login') }}"
                                            class="w-100 btn btn-outline--base pill">@lang('Log In')</a>
                                        <a href="{{ route('user.register') }}"
                                            class="w-100 btn btn--gr pill">@lang('Sign Up')</a>
                                    </div>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
            <div class="header-right ms-4 d-none d-xl-block">
                <div class="top-button d-flex flex-wrap justify-content-between align-items-center">
                    <ul class="flex-align gap-2">
                        @if (gs('multi_language'))
                            <li class="dropdown lang-box me-3">
                                <button class="lang-box-btn" data-bs-toggle="dropdown">
                                    <div class="thumb">
                                        <img src="{{ asset('assets/images/language/' . getActiveLang()->image) }}"
                                            alt="image">
                                    </div>
                                    <span class="content">
                                        <span class="text">{{ __(getActiveLang()->name) }}</span>
                                    </span>
                                </button>
                                <ul class="dropdown-menu lang-box-menu">
                                    @foreach ($languages->where('code', '!=', getActiveLang()->code) as $language)
                                        <li class="lang-box-item">
                                            <a href="{{ route('lang', $language->code) }}" class="lang-box-link">
                                                <div class="thumb">
                                                    <img src="{{ asset('assets/images/language/' . $language->image) }}"
                                                        alt="language">
                                                </div>
                                                <span class="text">{{ __($language->name) }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                        @auth
                            <li class="login-registration-list__item">
                                <a href="{{ route('user.home') }}" class="btn btn--gr pill">@lang('Dashboard')</a>
                            </li>
                        @else
                            <li class="login-registration-list__item">
                                <a href="{{ route('user.login') }}"
                                    class="btn btn-outline--base pill">@lang('Log in')</a>
                            </li>
                            <li class="login-registration-list__item">
                                <a href="{{ route('user.register') }}" class="btn btn--gr pill">@lang('Sign Up')</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
