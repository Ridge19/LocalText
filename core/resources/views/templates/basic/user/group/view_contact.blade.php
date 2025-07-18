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
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contacts as $key => $contact)
                                    <tr>
                                        <td>{{ __(@$contacts->firstItem() + $loop->index) }}</td>
                                        <td>+{{ @$contact->contact?->dial_code . @$contact->contact?->mobile }}</td>
                                        <td>
                                            <button type="button" class="btn btn--sm btn-outline-danger confirmationBtn"
                                                data-question="@lang('Are you sure to remove this contact from the contact?')"
                                                data-action="{{ route('user.group.delete.contact', $contact->id) }}">
                                                <i class="la la-trash"></i> @lang('Remove')
                                            </button>
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
                @if ($contacts->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($contacts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="groupModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> @lang('Add Phone To Group')</h4>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('user.group.to.contact.save', $group->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group" id="selection-contact">
                            <label class="fw-bold">@lang('Mobile')</label>
                            <select class="form-control w-100" id="contact-list" name="contacts[]" multiple></select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base w-100 h-45"> @lang('Submit') </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import Contact')</h4>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('user.group.import.contact', $group->id) }}" id="importForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <x-file-upload-warning />
                        </div>
                        <div class="form-group">
                            <label class="fw-bold required">@lang('Select File')</label>
                            <input type="file" class="form-control form--control" name="file"
                                accept=".txt,.csv,.xlsx">
                            <x-file-upload-link />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="Submit" class="btn btn--base w-100 h-45">@lang('Upload')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex justify-content-center justify-content-sm-end align-items-center flex-wrap gap-2">
        <button type="button" class="btn btn-outline-primary btn--sm addBtn">
            <i class="las la-plus"></i> @lang('Add Contact')
        </button>
        <button type="button" class="btn btn-outline-info btn--sm importData">
            <i class="las la-cloud-upload-alt"></i> @lang('Import Contact')
        </button>
        <a href="{{ route('user.group.index') }}" class="btn btn-outline-primary btn--sm">
            <i class="la la-undo"></i> @lang('Back')
        </a>
    </div>
@endpush

@push('script')
    <script>
        "use strict";

        (function($) {

            let groupModal = $("#groupModal");

            $('.addBtn').on('click', function(e) {
                groupModal.modal('show');
            });

            $(".importData").on('click', function(e) {
                $('#importModal').modal('show')
            });

            $("#importForm").on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData($(this)[0])
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function(e) {
                        $("#importModal").find(".modal-header").addClass('animate-border');
                    },
                    complete: function(e) {
                        $("#importModal").find(".modal-header").removeClass('animate-border');
                        $("#importModal").modal('hide');
                        setInterval(() => {
                            location.reload();
                        }, 2000);
                    },
                    success: function(response) {
                        if (response.success) {
                            notify('success', response.message);
                        } else {
                            notify('error', response.errors && response.errors.length > 0 ? response
                                .errors : response.message || "@lang('Something went to wrong')")
                        }
                    },
                    error: function(e) {
                        notify('error', "@lang('Something went to wrong')")
                    }
                });
            });

            $('#contact-list').select2({
                ajax: {
                    url: "{{ route('user.contact.search') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 1000,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page,
                            group_id: "{{ $group->id }}"
                        };
                    },
                    processResults: function(response, params) {
                        params.page = params.page || 1;
                        let data = response.contacts.data;
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: '+' + item.dial_code + ' ' + item.mobile,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: response.more
                            }
                        };
                    },
                    cache: false,
                    dropdownParent: $('#selection-contact'),
                    width: "100%"
                },
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select2-container--default .select2-selection--multiple {
            border-color: #ddd;
            min-height: calc(1.8rem + 1rem + 2px) !important;
            height: auto;
            width: 100% !important;
        }

        .select2-container {
            z-index: 9999;
        }

        span.select2.select2-container.select2-container--default.select2-container--focus.select2-container--below.select2-container--open {
            width: 100% !important;
        }

        input.select2-search__field {
            width: 100% !important;
        }

        span.select2.select2-container.select2-container--default.select2-container--below {
            width: 100% !important;
        }

        span.select2.select2-container.select2-container--default.select2-container--focus,
        span.select2.select2-container.select2-container--default.select2-container--default {
            width: 100% !important;
        }
    </style>
@endpush
