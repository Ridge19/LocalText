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
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}" placeholder="@lang('Search with device name or device id')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Username')</label>
                                <input type="search" autocomplete="off" name="username" value="{{ request()->username ?? null }}" placeholder="@lang('Search with username')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Status')</label>
                                <select class="form-control form--control select2" name="status" data-minimum-results-for-search="-1">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="1" @selected(request()->status == Status::CONNECTED)>@lang('Connected')</option>
                                    <option value="0" @selected(request()->status === (string) Status::DISCONNECTED)>@lang('Disconnected')</option>
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
                    <div class="table-responsive--md  table-responsive" id="device-table">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Device Name')</th>
                                    <th>@lang('Model')</th>
                                    <th>@lang('Device ID')</th>
                                    <th>@lang('Android Verison')</th>
                                    <th>@lang('App Version')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allDevice as $device)
                                    <tr>
                                        <td>
                                            @if ($device->user)
                                                <span class="fw-bold">{{ @$device->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a href="{{ appendQuery('username', @$device->user->username) }}"><span>@</span>{{ $device->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ __($device->device_name) }}</strong>
                                        </td>
                                        <td>{{ __(@$device->device_model) }}</td>

                                        <td>{{ __(@$device->device_id) }}</td>
                                        <td>{{ __(@$device->android_version) }}</td>
                                        <td>{{ __(@$device->app_version) }}</td>
                                        <td class="device-status">
                                            @if ($device->status)
                                                <span class="badge badge--success">@lang('Connected')</span>
                                            @else
                                                <span class="badge badge--danger">@lang('Disconnected')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-message-row">
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($allDevice->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($allDevice) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
