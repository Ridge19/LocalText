@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom--table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $key => $template)
                                    <tr>
                                        <td>{{ __(@$templates->firstItem() + $loop->index) }}</td>
                                        <td>{{ @$template->name }}</td>
                                        <td>
                                            @php echo $template->statusBadge @endphp
                                        </td>
                                        <td>
                                            {{ __(strLimit(@$template->message, 50)) }}
                                        </td>
                                        <td>
                                            <button type="button" data-template='@json($template)'
                                                class="btn btn--sm btn-outline-primary editBtn me-1">
                                                <i class="la la-pen"></i> @lang('Edit')
                                            </button>

                                            @if ($template->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn--sm btn-outline-success  confirmationBtn"
                                                    data-action="{{ route('user.template.status', $template->id) }}"
                                                    data-question="@lang('Are you sure to active this template?')">
                                                    <i class="la la-eye me-1"></i>@lang('Enable')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn--sm btn-outline-danger confirmationBtn"
                                                    data-action="{{ route('user.template.status', $template->id) }}"
                                                    data-question="@lang('Are you sure to inactive this template?')">
                                                    <i class="la la-eye-slash me-1"></i>@lang('Disable')
                                                </button>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($templates->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($templates) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade templateModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <h4 class="modal-title">@lang('Add New Contact')</h4>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" id="form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="fw-bold required">@lang('Name')</label>
                            <input required type="text" class="form-control form--control" name="name"
                                value="{{ old('Name') }}">
                        </div>
                        <div class="form-group">
                            <label class="fw-bold required">@lang('Message')</label>
                            <textarea required name="message" class="form-control form--control" cols="30"
                                rows="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base w-100 h-45" id="btn-save">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn--base btn--sm addBtn">
        <i class="las la-plus"></i> @lang('Add Template')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let templateModal = $(".templateModal");

            $('.addBtn').on('click', function(e) {
                let action = "{{ route('user.template.store') }}";
                templateModal.find(".modal-title").text("@lang('New Template')");
                templateModal.find('form').trigger('reset');
                templateModal.find('form').attr('action', action);
                templateModal.modal('show');
            });

            $('.editBtn').on('click', function(e) {
                let action = "{{ route('user.template.update', ':id') }}";
                let template = $(this).data('template');
                setFormValue(template, 'form')
                templateModal.find(".modal-title").text("@lang('Edit Template')");
                templateModal.find('form').attr('action', action.replace(':id', template.id));
                templateModal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
