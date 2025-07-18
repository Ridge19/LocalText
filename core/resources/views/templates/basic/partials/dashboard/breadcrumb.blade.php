<div class="d-flex mb-10 flex-wrap justify-content-between align-items-center">
    @if (
        !request()->routeIs([
            'user.profile.setting',
            'user.change.password',
            'user.deposit.index',
            'user.campaign.create',
            'user.campaign.edit',
            'user.deposit.confirm',
            'user.deposit.manual.confirm',
        ]))
        <h5 class="page-title">{{ __($pageTitle) }}</h5>
    @endif
    <div class="d-flex justify-content-center justify-content-sm-end align-items-center flex-wrap gap-2">
        @stack('breadcrumb-plugins')
    </div>
</div>
