@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="row align-items-center">
                            <div class="col-lg-3 form-group">
                                <label>@lang('Search')</label>
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}" placeholder="@lang('Search by mobile / city/ state / zip / country')"class="form-control form--control">
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
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Mobile')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('State')</th>
                                    <th>@lang('Zip')</th>
                                    <th>@lang('Country')</th>

                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                    <tr>
                                        <td>
                                            @if ($contact->user)
                                                <span class="fw-bold">{{ $contact->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a href="{{ appendQuery('username', @$contact->user->username) }}"><span>@</span>{{ $contact->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>+{{ @$contact->dial_code . @$contact->mobile }}</td>
                                        <td>{{ $contact->firstname . ' ' . $contact->lastname }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->city }}</td>
                                        <td>{{ $contact->state }}</td>
                                        <td>{{ $contact->zip }}</td>
                                        <td>{{ $contact->country }}</td>

                                        <td> @php echo $contact->statusBadge @endphp </td>
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
                    <div class="card-footer py-4">
                        {{ paginateLinks($contacts) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @php
        if(Route::is('admin.group.contact.view')){
            $route = route('admin.group.contact.export', $group->id);
        }else{
            $route = route('admin.contact.export');
        }
    @endphp

    <x-export-modal :columns="$columns" :collection="$contacts" route="{{ $route }}" />
@endsection

@push('breadcrumb-plugins')
    <button type="button" class="btn btn-outline--warning btn-sm exportBtn">
        <i class="las la-cloud-download-alt"></i> @lang('Export')
    </button>
@endpush
