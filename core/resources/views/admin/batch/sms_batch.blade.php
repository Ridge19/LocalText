@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="row align-items-center">
                            <div class="col-lg-4 form-group">
                                <label>@lang('Batch Number')</label>
                                <input type="search" autocomplete="off" name="search" value="{{ request()->search ?? null }}"
                                    placeholder="@lang('Search with batch number or username')" class="form-control form--control">
                            </div>
                            <div class="col-lg-4 form-group">
                                <label>@lang('Date')</label>
                                <input name="date" type="search"
                                    class="datepicker-here form-control form--control bg--white pe-2 date-range"
                                    placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="col-lg-4 form-group">
                                <button class="btn btn--primary w-100 h-45 mt-4" type="submit">
                                    <i class="fas fa-filter"></i>
                                    @lang('Filter')
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
                                    <th>@lang('Batch Number')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Total SMS')</th>
                                    <th>@lang('Total Delivered')</th>
                                    <th>@lang('Total Pending')</th>
                                    <th>@lang('Total Processing')</th>
                                    <th>@lang('Total Schedule')</th>
                                    <th>@lang('Total Failed')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
                                    <tr>
                                        <td>
                                            @if ($batch->user)
                                                <span class="fw-bold">{{ @$batch->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a
                                                        href="{{ appendQuery('search', @$batch->user->username) }}"><span>@</span>{{ $batch->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ __($batch->batch_id) }}</strong>
                                        </td>

                                        <td> {{ showDateTime(@$batch->created_at) }} <br>
                                            <small>{{ __(diffForHumans($batch->created_at)) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ __(@$batch->sms->count()) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge--success">{{ __(@$batch->sms->where('status', 1)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge--warning">{{ __(@$batch->sms->where('status', 2)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge--primary">{{ __(@$batch->sms->where('status', 4)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge--primary">{{ __(@$batch->sms->where('status', 3)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge--danger">{{ __(@$batch->sms->where('status', 9)->count()) }}</span>
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
                @if ($batches->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($batches) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('script-lib')
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        const datePicker = $('.date-range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            showDropdowns: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                    .endOf('month')
                ],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        });
        const changeDatePickerText = (event, startDate, endDate) => {
            $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
        }


        $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
            .startDate, picker.endDate));

        if ($('.date-range').val()) {
            let dateRange = $('.date-range').val().split(' - ');
            $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
            $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
        }
    </script>
@endpush
