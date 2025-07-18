@props(['columns' => [], 'collection' => [], 'route' => ''])
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Export Filter')</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form method="post" action="{{ $route }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="fw-bold">@lang('Export Column')</label>
                        <div class="d-flex gap-4 flex-wrap">
                            @foreach ($columns as $column)
                                <div>
                                    <input type="checkbox" name="columns[]" value="{{ $column }}"
                                        id="colum-{{ $column }}"
                                        {{ $column == 'created_at' || $column == 'updated_at' || $column == 'id' ? 'unchecked' : 'checked' }}>
                                    <label class="form-check-label" for="colum-{{ $column }}">
                                        {{ __(keyToTitle($column)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="fw-bold">@lang('Order By')</label>
                        <select name="order_by" class="form-control select2" data-minimum-results-for-search="-1">
                            <option value="ASC">@lang('ASC')</option>
                            <option value="DESC">@lang('DESC')</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="fw-bold">@lang('Export Item')</label>
                        <select class="form-control export-item select2" name="export_item"
                            max-item="{{ $collection->total() }}" data-minimum-results-for-search="-1">
                            <option value="10">@lang('10')</option>
                            <option value="50">@lang('50')</option>
                            <option value="100">@lang('100')</option>
                            @if ($collection->total() > 100)
                                <option value="{{ $collection->total() }}">{{ __($contacts->total()) }}</option>
                            @endif
                            <option value="custom">@lang('Custom')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="Submit" class="btn btn--primary w-100 h-45 contactExport">@lang('Export')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            var modal = $('#exportModal');
            var exportBtn = $('.exportBtn');
            exportBtn.on('click', function(e) {
                modal.modal('show');
            });
            var form = modal.find('form');
            form.on('submit', function() {
                modal.modal('hide');
            })
        })(jQuery);
    </script>
@endpush
