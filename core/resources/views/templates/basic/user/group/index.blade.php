@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn--base showFilterBtn btn--sm"><i class="las la-filter"></i>
                    @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-3">
                <div class="card-body">
                    <form>
                        <div class="row gy-3 gx-2 gx-md-4">
                            <div class="col-12 col-md-4">
                                <label class="form-label">@lang('Group Name')</label>
                                <input type="search" name="search" value="{{ request()->search }}"
                                    class="form-control form--control" placeholder="@lang('Search with group name')">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label d-block">@lang('Status')</label>
                                <select class="form-select form--control select2--dropdown"
                                    data-minimum-results-for-search="-1" name="status">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="1" @selected(request()->status === (string) Status::ENABLE)>@lang('Active')</option>
                                    <option value="0" @selected(request()->status === (string) Status::DISABLE)>@lang('Inactive')</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <button class="btn btn--base btn--md w-100"><i class="las la-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom--table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Contacts')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($groups as $key => $group)
                                    <tr>
                                        <td>{{ __(@$groups->firstItem() + $loop->index) }}</td>
                                        <td>{{ @$group->name }}</td>
                                        <td>{{ __(@$group->total_contact) }}</td>
                                        <td>
                                            @php echo $group->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-end">
                                                <button type="button" data-group='@json($group)'
                                                    class="btn btn--outline-primary btn--sm editBtn ">
                                                    <i class="la la-pen me-1"></i> @lang('Edit')
                                                </button>
                                                <a class="btn btn--outline-info btn--sm"
                                                    href="{{ route('user.group.contact.view', $group->id) }}">
                                                    <i class="las la-eye me-1"></i> @lang('View Contact')
                                                </a>
                                                @if ($group->status == Status::DISABLE)
                                                    <button type="button"
                                                        class="btn btn--outline-success btn--sm  confirmationBtn"
                                                        data-action="{{ route('user.group.status', $group->id) }}"
                                                        data-question="@lang('Are you sure to active this group?')">
                                                        <i class="la la-eye me-1"></i>@lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn--outline-danger btn--sm confirmationBtn"
                                                        data-action="{{ route('user.group.status', $group->id) }}"
                                                        data-question="@lang('Are you sure to inactive this group?')">
                                                        <i class="la la-eye-slash me-1"></i>@lang('Inactive')
                                                    </button>
                                                @endif
                                            </div>
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
                @if ($groups->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($groups) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="groupModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <h4 class="modal-title">@lang('Add New Contact')</h4>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class="las la-times"></i></button>
                </div>
                <form method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="fw-bold required">@lang('Name')</label>
                            <input required type="text" class="form-control form--control" name="name"
                                value="{{ old('name') }}">
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

@push('style')
    <style>
        .select2 w-100-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <button type="button" class="btn btn--base btn--sm addBtn">
        <i class="las la-plus"></i> @lang('New')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            let groupModal = $("#groupModal");

            $('.addBtn').on('click', function(e) {
                let action = "{{ route('user.group.store') }}";
                groupModal.find(".modal-title").text("@lang('Add Group')");
                groupModal.find('form').trigger('reset');
                groupModal.find('form').attr('action', action);
                groupModal.modal('show');
            });

            $('.editBtn').on('click', function(e) {
                let action = "{{ route('user.group.update', ':id') }}";
                let group = $(this).data('group');
                groupModal.find('input[name=name]').val(group.name);
                groupModal.find(".modal-title").text("@lang('Edit Group')");
                groupModal.find('form').attr('action', action.replace(':id', group.id));
                groupModal.modal('show');

            });

        })(jQuery);
    </script>
@endpush
