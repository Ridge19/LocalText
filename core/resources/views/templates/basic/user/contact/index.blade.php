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
                        <div class="row gy-3 gx-2 gx-md-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">@lang('Mobile Number')</label>
                                <input type="search" name="search" value="{{ request()->search }}"
                                    class="form-control form--control" placeholder="@lang('Search with mobile code or mobile number')">
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
            <!-- Contact Table  -->
            <div class="card custom--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom--table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Added At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contacts as $key => $contact)
                                    <tr>
                                        <td>{{ __(@$contacts->firstItem() + $loop->index) }}</td>
                                        <td>+{{ @$contact->dial_code }}{{ @$contact->mobile }}</td>
                                        <td> @php echo $contact->statusBadge @endphp </td>
                                        <td>{{ showDateTime(@$contact->created_at, 'd M Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end flex-wrap gap-2">
                                                <a href="{{ route('user.sms.send', ['dial_code' => $contact->dial_code, 'mobile' => $contact->mobile]) }}"
                                                    class="btn btn-outline-info btn--sm">
                                                    <i class="las la-sms"></i>
                                                    @lang('Send SMS')
                                                </a>
                                                <button type="button" data-contact='@json($contact)'
                                                    class="btn btn-outline-primary btn--sm editBtn">
                                                    <i class="la la-pen"></i> @lang('Edit')
                                                </button>
                                                @if ($contact->status == Status::DISABLE)
                                                    <button type="button"
                                                        class="btn btn-outline-success btn--sm confirmationBtn"
                                                        data-action="{{ route('user.contact.status', $contact->id) }}"
                                                        data-question="@lang('Are you sure to active this contact?')">
                                                        <i class="la la-eye"></i> @lang('Active')
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn--sm confirmationBtn"
                                                        data-action="{{ route('user.contact.status', $contact->id) }}"
                                                        data-question="@lang('Are you sure to inactive this contact?')">
                                                        <i class="la la-eye-slash"></i> @lang('Inactive')
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
                @if ($contacts->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($contacts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal custom--modal fade" id="contactModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <h4 class="modal-title">@lang('Add New Contact')</h4>
                    <button class="btn btn-close" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="post" id="form">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Country')</label>
                                    <select name="country" class="form-control form--control select2--dropdown">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}"
                                                value="{{ $country->country }}" data-code="{{ $key }}">
                                                {{ __($country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Mobile')</label>
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input type="number" name="mobile" value="{{ old('mobile') }}"
                                            class="form-control form--control" required>
                                    </div>
                                    <small class="text--danger"></small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input type="text" class="form-control form--control" name="firstname"
                                        value="{{ old('firstname') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Last Name')</label>
                                    <input type="text" class="form-control form--control" name="lastname"
                                        value="{{ old('lastname') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input type="text" class="form-control form--control" name="email"
                                        value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input type="text" class="form-control form--control" name="city"
                                        value="{{ old('city') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('State')</label>
                                    <input type="text" class="form-control form--control" name="state"
                                        value="{{ old('state') }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Zip')</label>
                                    <input type="text" class="form-control form--control" name="zip"
                                        value="{{ old('zip') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base w-100 h-45"
                            id="btn-save">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal custom--modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <h4 class="modal-title">@lang('Import Contact')</h4>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('user.contact.import') }}" id="importForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <x-file-upload-warning />
                        </div>
                        <div class="form-group">
                            <label>@lang('Select File')</label>
                            <input type="file" class="form-control form--control" name="file"
                                accept=".txt,.csv,.xlsx">
                            <x-file-upload-link />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base w-100 h-45">@lang('Upload')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal custom--modal fade" id="exportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal--header">
                    <h4 class="modal-title">@lang('Export Filter')</h4>
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('user.contact.export') }}" id="importForm"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Export Column')</label>
                            <div class="d-flex gap-4 flex-wrap">
                                @foreach ($columns as $column)
                                    <div>
                                        <input type="checkbox" name="columns[]" value="{{ $column }}"
                                            id="colum-{{ $column }}" checked>
                                        <label class="form-check-label" for="colum-{{ $column }}">
                                            {{ __(keyToTitle($column)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Order By')</label>
                            <select name="order_by" class="form-control form--control">
                                <option value="ASC">@lang('ASC')</option>
                                <option value="DESC">@lang('DESC')</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Export Item')</label>
                            <select class="form-control select2--dropdown export-item" name="export_item"
                                max-item="{{ $contacts->total() }}" data-minimum-results-for-search="-1">
                                <option value="10">@lang('10')</option>
                                <option value="50">@lang('50')</option>
                                <option value="100">@lang('100')</option>
                                @if ($contacts->total() > 100)
                                    <option value="{{ $contacts->total() }}">{{ __($contacts->total()) }}</option>
                                @endif
                                <option value="custom">@lang('Custom')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="Submit" class="btn btn--base w-100 h-45 contactExport">@lang('Export')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn--outline-base btn--sm addBtn">
        <i class="las la-plus"></i> @lang('New')
    </button>
    <button type="button" class="btn btn--outline-info btn--sm importBtn">
        <i class="las la-cloud-upload-alt"></i> @lang('Import')
    </button>
    <button type="button" class="btn btn--outline-warning btn--sm exportBtn">
        <i class="las la-cloud-download-alt"></i> @lang('Export')
    </button>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            let contactModal = $("#contactModal");

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', true).trigger("change");
            @endif

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $('.addBtn').on('click', function(e) {

                let action = "{{ route('user.contact.store') }}";
                contactModal.find(".modal-title").text("@lang('New Contact')");
                let form = contactModal.find('form');
                form.trigger('reset');

                let firstItem = form.find('.select2--dropdown').find('option:first').val();
                form.find('.select2--dropdown').val(firstItem).trigger('change');
                form.attr('action', action);

                contactModal.modal('show');
            });

            $('.editBtn').on('click', function(e) {

                let action = "{{ route('user.contact.update', ':id') }}";
                let contact = $(this).data('contact');
                setFormValue(contact, 'form')

                contactModal.find(".modal-title").text("@lang('Filter')");
                contactModal.find('form').attr('action', action.replace(':id', contact.id));
                contactModal.modal('show');
            });

            $(".importBtn").on('click', function(e) {
                let importModal = $("#importModal");
                importModal.modal('show');
            });

            $('#importForm').on('submit', function(event) {

                event.preventDefault();
                let formData = new FormData($(this)[0]);
                let time = 0;
                $.ajax({
                    url: $(this).attr('action'),
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#importModal').find('.modal-header').addClass('animate-border');
                    },
                    complete: function(e) {
                        $('#importModal').find('.modal-header').removeClass('animate-border');
                    },
                    success: function(response) {
                        if (response.success) {
                            notify('success', response.message);
                            $("#importModal").modal('hide');
                            setTimeout(() => {
                                location.reload()
                            }, 2000);
                        } else {
                            notify('error', response.errors && response.errors.length > 0 ?
                                response
                                .errors : response.message || "@lang('Mobile Number')");
                        }
                    },

                });
            });

            $(".exportBtn").on('click', function(e) {
                let modal = $("#exportModal");
                modal.modal('show')
            });

            $("#exportModal form").on('submit', function(e) {
                $("#exportModal").modal('hide');
            });

        })(jQuery);
    </script>
@endpush
