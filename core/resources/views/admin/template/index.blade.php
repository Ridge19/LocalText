@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="row align-items-center">
                            <div class="col-lg-3 form-group">
                                <label>@lang('Name')</label>
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}" placeholder="@lang('Search with template name')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Username')</label>
                                <input type="search" autocomplete="off" name="username" value="{{ request()->username ?? null }}" placeholder="@lang('Search with username')"class="form-control form--control">

                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Status')</label>
                                <select class="form-control form--control select2" name="status" data-minimum-results-for-search="-1">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="1" @selected(request()->status == Status::ENABLE)>@lang('Active')</option>
                                    <option value="0" @selected(request()->status === (string) Status::DISABLE)>@lang('Inactive')</option>
                                </select>
                            </div>
                            <div class="col-lg-3 form-group">
                                <button class="btn btn--primary w-100 h-45 mt-4" type="submit">
                                    <i class="fas fa-filter"></i>@lang('Filter')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card  ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr>
                                        <td>
                                            @if ($template->user)
                                                <span class="fw-bold">{{ @$template->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a href="{{ appendQuery('username', @$template->user->username) }}"><span>@</span>{{ $template->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>{{ __(@$template->name) }}</td>
                                        <td>{{ __(strLimit(@$template->message, 50)) }}</td>

                                        <td> @php echo $template->statusBadge @endphp </td>
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
                    <div class="card-footer py-4">
                        {{ paginateLinks($templates) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-export-modal :columns="$columns" :collection="$templates" route="{{ route('admin.template.export') }}" />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-outline--warning btn-sm exportBtn">
        <i class="las la-cloud-download-alt"></i> @lang('Export')
    </button>
@endpush
