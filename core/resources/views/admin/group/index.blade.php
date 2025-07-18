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
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}"
                                    placeholder="@lang('Search with group name')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Username')</label>
                                <input type="search" autocomplete="off" name="username"
                                    value="{{ request()->username ?? null }}"
                                    placeholder="@lang('Search with username')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Status')</label>
                                <select class="form-control form--control select2" name="status"
                                    data-minimum-results-for-search="-1">
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
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Contacts')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $group)
                                    <tr>
                                        <td>
                                            @if ($group->user)
                                                <span class="fw-bold">{{ @$group->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a
                                                        href="{{ appendQuery('username', @$group->user->username) }}"><span>@</span>{{ $group->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td> {{ __(@$group->name) }}</td>
                                        <td>
                                            {{ __($group->total_contact) }}
                                        </td>
                                        <td> @php echo $group->statusBadge @endphp </td>
                                        <td>
                                            <div class="d-flex align-items-center flex-wrap justify-content-end">
                                                <a class="btn btn--sm btn-outline--primary"
                                                    href="{{ route('admin.group.contact.view', $group->id) }}">
                                                    <i class="las la-list-alt"></i>@lang('Contacts')
                                                </a>
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
                    <div class="card-footer py-4">
                        {{ paginateLinks($groups) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-export-modal :columns="$columns" :collection="$groups" route="{{ route('admin.group.export') }}" />
@endsection
@push('breadcrumb-plugins')
    <button type="button" class="btn btn-outline--warning btn-sm exportBtn">
        <i class="las la-cloud-download-alt"></i> @lang('Export')
    </button>
@endpush
