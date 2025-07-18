@php
    $user = auth()->user();
@endphp
<div class="dashboard-nav d-flex flex-wrap align-items-center justify-content-between">
    <div class="nav-left d-flex gap-4 align-items-center">
        <div class="dash-sidebar-toggler d-xl-none" id="dash-sidebar-toggler">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="nav-right d-flex flex-wrap align-items-center gap-3">
        <a href="{{ route('user.apk') }}" class="btn btn--dark btn--sm">@lang('Download APK')</a>

        <a href="{{ route('user.sms.send') }}" class="btn btn--base btn--sm">@lang('Send Single SMS')</a>
        <a href="{{ route('user.campaign.create') }}" class="btn btn--info btn--sm">@lang('Send Bulk SMS')</a>

        @if (gs('multi_language'))
            @php
                $language = App\Models\Language::all();
            @endphp
            <select class="langSel form--control h-auto px-2 py-1 border-0">
                @foreach ($language as $item)
                    <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>
                        {{ __($item->name) }}</option>
                @endforeach
            </select>
        @endif
        <ul class="nav-header-link d-flex flex-wrap gap-2">
            <li>
                <a class="link fullscreen-btn fullscreen-open d-grid" onclick="openFullscreen()" href="javascript:void(0)"><i class="fas fa-expand"></i></a>
                <a class="link fullscreen-btn fullscreen-close d-none" onclick="closeFullscreen()" href="javascript:void(0)"><i class="fas fa-compress"></i></a>
            </li>
            <li>
                <a class="link short_name" href="javascript:void(0)">
                    {{ generateInitials(@$user->fullname) }}
                </a>
                <div class="dropdown-wrapper">
                    <div class="dropdown-header">
                        <h6 class="name text--base">{{ @$user->username }}</h6>
                        <div class="d-flex flex-wrap justify-content-between mt-2">
                            <h6 class="fs--14px">@lang('Balance')</h6>
                            <h6 class="fs--14px text--success">{{ showAmount(@$user->balance, currencyFormat: false) }}
                                {{ gs('cur_text') }}</h6>
                        </div>
                    </div>
                    <ul class="links">
                        <li><a href="{{ route('user.profile.setting') }}"><i class="las la-user"></i> @lang('Profile')
                            </a></li>
                        <li><a href="{{ route('user.logout') }}"><i class="las la-sign-out-alt"></i> @lang('Sign Out')
                            </a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
