<div class="dashboard-sidebar" id="dashboard-sidebar">
    <button class="btn-close dash-sidebar-close d-xl-none"></button>
    <a href="{{ route('home') }}" class="logo mb-4"><img src="{{ siteLogo() }}" alt="logo"></a>

    <ul class="sidebar-menu">
        <li><a class="{{ menuActive('user.home') }}" href="{{ route('user.home') }}">
                <i class="las la-home"></i>
                <span>@lang('Dashboard') </a></span>
        </li>
        <li><a class="{{ menuActive('user.device.index') }}" href="{{ route('user.device.index') }}">
                <i class="las la-mobile"></i>
                <span>@lang('Manage Device')</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="#sms" data-bs-toggle="collapse" @if (Route::is('user.sms.*')) aria-expanded="true" @else aria-expanded="false" @endif class="dropdown-toggle">
                <i class="las la-sms"></i>
                <span>@lang('Manage SMS')</span>
            </a>
            <ul class="collapse list-unstyled @if (Route::is('user.sms.*')) show @endif" id="sms">
                <li>
                    <a class="{{ menuActive('user.sms.send') }}" href="{{ route('user.sms.send') }}">
                        <span class="fas fa-circle"></span> @lang('Send Single SMS')
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.sms.index') }}" href="{{ route('user.sms.index') }}">
                        <span class="fas fa-circle"></span> @lang('SMS History')
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#campaign" data-bs-toggle="collapse" @if (Route::is('user.campaign.*')) aria-expanded="true" @else aria-expanded="false" @endif class="dropdown-toggle">
                <i class="las la-campground"></i>
                <span>@lang('Manage Campaign')</span>
            </a>
            <ul class="collapse list-unstyled @if (Route::is('user.campaign.*')) show @endif" id="campaign">
                <li>
                    <a class="{{ menuActive('user.campaign.create') }}" href="{{ route('user.campaign.create') }}">
                        <span class="fas fa-circle"></span> @lang('Create Campaign')
                    </a>
                </li>
                <li>
                    <a class="{{ menuActive('user.campaign.index') }}" href="{{ route('user.campaign.index') }}">
                        <span class="fas fa-circle"></span> @lang('Campaign History')
                    </a>
                </li>
            </ul>
        </li>

        <li><a class="{{ menuActive('user.contact.index') }}" href="{{ route('user.contact.index') }}">
                <i class="las la-id-card"></i>
                <span>@lang('Manage Contact')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.group.index') }}" href="{{ route('user.group.index') }}">
                <i class="las la-users"></i>
                <span>@lang('Manage Group')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.template.index') }}" href="{{ route('user.template.index') }}">
                <i class="las la-align-justify"></i>
                <span>@lang('Manage Template')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.batch.index') }}" href="{{ route('user.batch.index') }}">
                <i class="las la-network-wired"></i>
                <span>@lang('Manage Batch')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.deposit.history') }}" href="{{ route('user.deposit.history') }}">
                <i class="las la-wallet"></i>
                <span>@lang('Deposit')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.transactions') }}" href="{{ route('user.transactions') }}">
                <i class="las la-handshake"></i>
                <span>@lang('Transactions')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.plan.purchased') }}" href="{{ route('user.plan.purchased') }}">
                <i class="las la-file-alt"></i>
                <span>@lang('Purchase History')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.developer.api.docs') }}" href="{{ route('user.developer.api.docs') }}">
                <i class="las la-code"></i>
                <span>@lang('Developer Tools')</span>
            </a>
        </li>
        <li><a class="{{ menuActive('user.ticket.index') }}" href="{{ route('user.ticket.index') }}">
             <i class="las la-tag"></i>
             <span>@lang('My Ticket')</span>
         </a></li>
         
        <li><a class="{{ menuActive('user.profile.setting') }}" href="{{ route('user.profile.setting') }}">
                <i class="las la-user-edit"></i>
                <span>@lang('Profile Setting')</span> </a></li>

        <li><a class="{{ menuActive('user.change.password') }}" href="{{ route('user.change.password') }}">
                <i class="las la-key"></i>
                <span>@lang('Change Password')</span></a>
        </li>
        <li><a href="{{ route('user.logout') }}">
                <i class="las la-sign-out-alt"></i>
                <span>@lang('Logout')</span></a>
        </li>
    </ul>
</div>
