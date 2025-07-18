@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="policy-breadcrumb py-60">
        <div class="container">
            <h3 class="title m-0">{{ __($pageTitle) }}</h3>
        </div>
    </div>
    <section class="py-60 policy-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="description">
                        @php
                            echo $policy->data_values->details;
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .policy-section ul,.policy-section ol  {
            padding-left: 20px;
        }

        .policy-section ul,
        .policy-section li {
            list-style: auto;
        }

        .policy-breadcrumb {
            background-color: #F8F8FB;
            text-align: center;
        }
    </style>
@endpush
