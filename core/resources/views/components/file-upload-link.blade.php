<div class="mt-1">
    <small class="d-block">
        @lang('Supported files:') <b class="fw-bold">@lang('csv'), @lang('excel'),
            @lang('txt')</b>
    </small>
    <small>
        @lang('Download the all template file from here')
        <a href="{{ asset('assets/file_template/mobile/sample.csv') }}" data-bs-toggle="tooltip"
            title="@lang('Download csv file')" class="text--primary" download>
            <b>@lang('csv,')</b>
        </a>
        <a href="{{ asset('assets/file_template/mobile/sample.xlsx') }}" data-bs-toggle="tooltip"
            title="@lang('Download excel file')" class="text--primary" download>
            <b>@lang('excel,')</b>
        </a>
        <a href="{{ asset('assets/file_template/mobile/sample.txt') }}" ata-bs-toggle="tooltip"
            title="@lang('Download txt file')" class="text--primary" download>
            <b>@lang('txt')</b>
        </a>
    </small>
</div>
