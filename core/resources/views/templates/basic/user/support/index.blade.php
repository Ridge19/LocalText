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
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supports as $support)
                                    <tr>
                                        <td>
                                            <a href="{{ route('ticket.view', $support->ticket) }}" class="fw-bold">
                                                [@lang('Ticket')#{{ $support->ticket }}] {{ __($support->subject) }}
                                            </a>
                                        </td>
                                        <td>
                                            @php echo $support->statusBadge; @endphp
                                        </td>
                                        <td>
                                            @if ($support->priority == Status::PRIORITY_LOW)
                                                <span class="badge badge--dark">@lang('Low')</span>
                                            @elseif($support->priority == Status::PRIORITY_MEDIUM)
                                                <span class="badge badge--warning">@lang('Medium')</span>
                                            @elseif($support->priority == Status::PRIORITY_HIGH)
                                                <span class="badge badge--danger">@lang('High')</span>
                                            @endif
                                        </td>
                                        <td>{{ diffForHumans($support->last_reply) }}</td>
                                        <td>
                                            <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--base btn--sm">
                                                <i class="las la-desktop"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">
                                            {{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($supports->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($supports) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.ticket.open') }}" class="btn btn--sm btn--base">
        <i class="fas fa-plus"></i> @lang('New Ticket')
    </a>
@endpush
