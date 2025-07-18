@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="row align-items-center">
                            <div class="col-lg-3 form-group">
                                <label>@lang('Title')</label>
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}" placeholder="@lang('Search with title')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Username')</label>
                                <input type="search" autocomplete="off" name="username" value="{{ request()->username ?? null }}" placeholder="@lang('Search with username')"class="form-control form--control">
                            </div>
                            <div class="col-lg-3 form-group">
                                <label>@lang('Status')</label>
                                <select class="form-control form--control select2" name="status" data-minimum-results-for-search="-1">
                                    <option value="" selected>@lang('All')</option>
                                    <option value="{{ Status::CAMPAIGN_INITIAL }}" @selected(request()->status === (string) Status::CAMPAIGN_INITIAL)>
                                        @lang('Initiated')</option>
                                    <option value="{{ Status::CAMPAIGN_RUNNING }}" @selected(request()->status == Status::CAMPAIGN_RUNNING)>
                                        @lang('Running')</option>
                                    <option value="{{ Status::CAMPAIGN_PENDING }}" @selected(request()->status == Status::CAMPAIGN_PENDING)>
                                        @lang('Pending')</option>
                                    <option value="{{ Status::CAMPAIGN_FINISHED }}" @selected(request()->status == Status::CAMPAIGN_FINISHED)>
                                        @lang('Finished')</option>
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
                                    <th>@lang('Campaign Title')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                    <tr>
                                        <td>
                                            @if ($campaign->user)
                                                <span class="fw-bold">{{ $campaign->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a href="{{ appendQuery('username', @$campaign->user->username) }}"><span>@</span>{{ $campaign->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>{{ __(@$campaign->title) }}</td>

                                        <td>{{ strlimit(__($campaign->message), 50) }}</td>
                                        <td>
                                            @php echo $campaign->campaignBadge() @endphp
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
                @if ($campaigns->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($campaigns) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
