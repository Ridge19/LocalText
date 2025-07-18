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
                                @forelse ($batches as $batch)
                                    <tr>
                                        <td>{{ __($batches->firstItem() + $loop->index) }}</td>
                                        <td>
                                            <strong>{{ __($batch->batch_id) }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                {{ showDateTime(@$batch->created_at) }} <br>
                                                <small>{{ __(diffForHumans($batch->created_at)) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge--base">{{ __(@$batch->sms->count()) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--success">{{ __(@$batch->sms->where('status', Status::SMS_DELIVERED)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--warning">{{ __(@$batch->sms->where('status', Status::SMS_PENDING)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--info">{{ __(@$batch->sms->where('status', Status::SMS_PROCESSING)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ __(@$batch->sms->where('status', Status::SMS_SCHEDULED)->count()) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--danger">{{ __(@$batch->sms->where('status', Status::SMS_FAILED)->count()) }}</span>
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
                    <div class="card-footer">
                        {{ paginateLinks($batches) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
