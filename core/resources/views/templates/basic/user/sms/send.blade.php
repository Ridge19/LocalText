@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center">

        @include('Template::partials.alert_box')

        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body">
                    <form method="POST" method="POST" id="message-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Device')</label>
                                    <select name="device" class="form-control form--control select2--dropdown" required>
                                        <option value="" selected disabled>@lang('Please Select One')</option>
                                        @foreach ($allDevice as $device)
                                            <option value="{{ $device->id }}" data-sim='@json($device->sim)'>
                                                {{ __($device->device_name) }}-{{ __($device->device_model) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Select SIM')</label>
                                    <select name="sim" class="form-control form--control select2--dropdown"
                                        data-minimum-results-for-search="-1" required>
                                        <option value="" selected disabled>@lang('Please Select Device')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12" id="schedule">
                                <div class="form-group">
                                    <label>@lang('Schedule')</label>
                                    <select name="schedule" class="form-control form--control select2--dropdown"
                                        data-minimum-results-for-search="-1" required>
                                        <option value="1">@lang('Send Now')</option>
                                        <option value="2">@lang('Add Schedule')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 d-none" id="date">
                                <div class="form-group">
                                    <label for="date">@lang('Date')</label>
                                    <div class="input-group">
                                        <input type="text" name="date" class="form-control form--control date"
                                            autocomplete="off">
                                        <span class="input-group-text">
                                            <i class="las la-clock"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Country')</label>
                                    <select name="country" class="form-control form--control select2--dropdown">
                                        @foreach ($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" @selected(request()->dial_code == $country->dial_code)
                                                value="{{ $country->country }}" data-code="{{ $key }}">
                                                {{ __($country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Mobile')</label>
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="hidden" name="mobile_code">
                                        <input type="hidden" name="country_code">
                                        <input type="number" name="mobile" value="{{ request()->mobile ?? old('mobile') }}"
                                            class="form-control form--control" required>
                                    </div>
                                    <small class="text--danger"></small>
                                </div>
                            </div>
                        </div>
                        <div class="border-line-area mt-2 mb-2">
                            <h6 class="border-line-title">@lang('Write SMS')</h6>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold">@lang('Template')</label>
                            <select name="template" class="form-control form--control select2--dropdown"
                                data-minimum-results-for-search="-1">
                                <option selected disabled>@lang('Select One')</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}" message="{{ $template->message }}">
                                        {{ __($template->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold">@lang('Sms')</label>
                            <textarea name="message" required class="form-control form--control" cols="30" rows="6"></textarea>
                            <div class="message-count mt-2"></div>
                        </div>
                        <button type="submit" class="btn btn--base w-100 h-45" @disabled(!$allDevice->count())>@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="info-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('File Upload Information')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-close" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="alert alert-warning p-3 text-center" role="alert">
                            <p>
                                @lang('The file you wish to upload has to be formatted as we provided template files. Any changes to these files will be considered as an invalid file format. Download links are provided below.')
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- custom-loadder --}}
    <div class="adz-modal">
        <div class="adz-modal__card">
            <span class="adz-modal__text">@lang('Loading....')</span>
            <div class="adz-progressbar">
                <div class="adz-progressbar__bg"></div>
                <div class="adz-progressbar__buffer"></div>
                <div class="adz-progressbar__line">
                    <div class="adz-progressbar__indeterminate long"></div>
                    <div class="adz-progressbar__indeterminate short"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('user.sms.index') }}" class="btn btn--base btn--sm addBtn">
        <i class="las la-list"></i> @lang('SMS History')
    </a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/datepicker.en.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/datepicker.min.css') }}">
@endpush

@push('script')
    <script>
        (function($) {

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', true).trigger("change");
            @endif

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));

            $form = $('#message-form');
            $("select[name=device]").on('change', function(e) {
                let sim = $("select[name=device]").find('option:selected').data('sim');
                let html = "<option value='' selected disabled>@lang('Please select one')</option>";
                sim.forEach((element, index) => {
                    html +=
                        `<option value="${index}">${element.slot}-${element.name}</option>`
                });
                $("select[name=sim]").html(html)
            });

            $('.date').datepicker({
                language: 'en',
                dateFormat: 'yyyy-mm-dd',
                timepicker: true,
                minDate: new Date()
            });

            $('.date, .time-picker').on('keypress keyup change paste', function(e) {
                return false;
            });

            $("select[name=schedule]").on('change', function(e) {
                let schedule = $(this).val();
                if (schedule == 1) {
                    $("#schedule").addClass('col-lg-12')
                    $("#schedule").removeClass('col-lg-6');
                    $("#date").addClass('d-none');
                } else {
                    $("#schedule").removeClass('col-lg-12')
                    $("#schedule").addClass('col-lg-6');
                    $("#date").removeClass('d-none');
                }
            });

            $("select[name=template]").on('change', function(e) {
                let message = $(this).find('option:selected').attr('message');
                $("textarea[name=message]").val(message)
                messageCount();
            });

            $("textarea[name=message]").on('keyup keypress change keydown paste', function(e) {
                messageCount();
            });

            function messageCount() {
                let message = $("textarea[name=message]").val();
                let word = message.split(" ");

                $('.message-count').removeClass('d-none');
                $(".message-count").html(`
                <small><span class='text--success'>${message.length}</span> Characters</small> <br>
                <small><span class="text--success">${word.length}</span> Words</small> <br>
            `);
            };

            $form.on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData($(this)[0])
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function(e) {
                        $(".adz-modal ").css('display', 'block');
                    },
                    complete: function(e) {
                        $(".adz-modal ").css('display', 'none');
                        $("html").animate({
                            scrollTop: 0
                        }, "fast");
                    },
                    success: function(response) {
                        if (response.success) {
                            notify('success', response.message);
                            resetForm();
                            $('.message-count').addClass('d-none');
                            $('#date').addClass('d-none');
                            $('#schedule').addClass('col-lg-12');
                        } else {
                            notify('error', response.errors && response.errors.length > 0 ? response
                                .errors : response.message || "@lang('Somehting went to wrong')")
                        }
                    },
                    error: function(e) {
                        notify('error', "@lang('Something went to wrong')")
                    }
                });
            });

            function resetForm() {
                $form.trigger('reset');
                $('.select2--dropdown').val(" ");
                select2ReInitialization();
            }

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select2-container--default .select2-selection--multiple {
            border-color: #ddd;
            min-height: calc(1.8rem + 1rem + 2px) !important;
            height: auto;
        }

        .select2-container .select2-selection--single {
            border-color: #ddd;
            min-height: calc(1.8rem + 1rem + 2px) !important;
            height: auto;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            /* line-height: 45px; */
        }

        .uploadInFo {
            cursor: pointer;
        }

        .datepickers-container {
            z-index: 9999999999;
        }

        .adz-modal {
            width: 300px;
            border-radius: 4px;
            pointer-events: auto;
            box-shadow: 0 11px 15px -7px rgb(0 0 0 / 20%), 0 24px 38px 3px rgb(0 0 0 / 14%), 0 9px 46px 8px rgb(0 0 0 / 12%);
            top: 50%;
            left: 50%;
            z-index: 99999999999999999999;
            position: absolute;
            display: none;
        }

        .adz-modal__card {
            padding: 5px 24px 15px;
            box-shadow: 0 3px 1px -2px rgb(0 0 0 / 20%), 0 2px 2px 0 rgb(0 0 0 / 14%), 0 1px 5px 0 rgb(0 0 0 / 12%);
            background-color: #1867c0;
            border-radius: 4px;
        }

        .adz-modal__text {
            display: block;
            margin-bottom: 5px;
            color: hsla(0, 0%, 100%, .7);
        }

        .adz-progressbar {
            width: 100%;
            height: 4px;
            background: transparent;
            overflow: hidden;
            position: relative;
            transition: .2s cubic-bezier(.4, 0, .6, 1);
        }

        .adz-progressbar__bg {
            width: 100%;
            position: absolute;
            bottom: 0;
            top: 0;
            left: 0;
            opacity: 0.3;
            border-color: #fff !important;
            background-color: #fff !important;
            transition: inherit;
        }

        .adz-progressbar__buffer {
            width: 100%;
            height: inherit;
            position: absolute;
            left: 0;
            top: 0;
            transition: inherit;
        }

        .adz-progressbar__indeterminate {
            width: auto;
            height: inherit;
            animation-play-state: running;
            animation-duration: 2.2s;
            animation-iteration-count: infinite;
            position: absolute;
            bottom: 0;
            left: 0;
            top: 0;
            right: auto;
            will-change: left, right;
            border-color: #fff !important;
            background-color: #fff !important;
        }

        .adz-progressbar__indeterminate.long {
            animation-name: indeterminate-ltr;
        }

        .adz-progressbar__indeterminate.short {
            animation-name: indeterminate-short-ltr;
        }

        @keyframes indeterminate-ltr {
            0% {
                left: -90%;
                right: 100%;
            }

            60% {
                left: -90%;
                right: 100%;
            }

            100% {
                left: 100%;
                right: -35%;
            }
        }

        @keyframes indeterminate-short-ltr {
            0% {
                left: -200%;
                right: 100%;
            }

            60% {
                left: 107%;
                right: -8%;
            }

            100% {
                left: 107%;
                right: -8%;
            }
        }
    </style>
@endpush
