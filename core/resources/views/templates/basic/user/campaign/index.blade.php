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
                                    <th>@lang('Title')</th>
                                    <th>@lang('Message')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $key => $campaign)
                                    <tr>
                                        <td>{{ __(@$campaigns->firstItem() + $loop->index) }}</td>
                                        <td>{{ @$campaign->title }}</td>
                                        <td>{{ strLimit(@$campaign->message, 100) }}
                                            @if (strlen(@$campaign->message) > 100)
                                                <span class="text--primary showMessage cursor-pointer" message="{{ @$campaign->message }}">
                                                    @lang('Read More')
                                                </span>
                                            @endif
                                        </td>
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
                    <div class="card-footer">
                        {{ paginateLinks($campaigns) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Campaign Message')</h4>
                    <button type="button" class="close btn btn--sm" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                </div>
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

        .cursor-pointer{
            cursor: pointer;
            user-select: none;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <a href="{{ route('user.campaign.create') }}" class="btn btn--base btn--sm">
        <i class="las la-plus"></i> @lang('New')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.showMessage').on('click', function(e) {
                let modal = $('#messageModal');
                let message = $(this).attr('message');
                $(modal).find('.modal-body').html(`
                <p>${message}</p>
            `)
                modal.modal('show')
            });
        })(jQuery);
    </script>
@endpush
