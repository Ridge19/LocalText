@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center">

        @include('Template::partials.alert_box')
        <div class="col-md-12 campaign_wrapper">
            <div class="card custom--card">
                <div class="card-header">
                    <h5 class="card-title">{{ __(@$pageTitle) }} </h5>
                </div>
                <div class="card-body">
                    <div class="create-campaign">
                        <div class="create-campaign-form">
                            <form method="post" enctype="multipart/form-data"
                                action="{{ route('user.campaign.store') }}" class="disableSubmission">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Title')</label>
                                            <input type="text" class="form-control form--control" name="title" value="{{ old('title') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>@lang('Device')</label>
                                            <select name="device" class="form-control form--control select2--dropdown"
                                                required>
                                                <option value="" selected disabled>@lang('Please Select One')</option>
                                                @foreach ($allDevice as $device)
                                                    <option value="{{ @$device->id }}" @selected(old('device'))
                                                        data-sim='@json($device->sim)'>
                                                        {{ __(@$device->device_name) }}-{{ __(@$device->device_model) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="required">@lang('Select SIM')</label>
                                            <select name="sim" class="form-control form--control select2--dropdown"
                                                data-minimum-results-for-search="-1" required
                                                data-selected="{{ old('sim') }}">
                                                <option value="" selected disabled>@lang('Please Select Sim')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
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
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="border-line-area mt-4 mb-2">
                                            <h6 class="border-line-title">@lang('Numbers you want to send message')</h6>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 number-selection-wrapper">
                                        <div class="form-group">
                                            <label class="form-label required">@lang('Select Numbers From')</label>
                                            <select name="selection_type" class="selectionType form-control form--control select2--dropdown" data-minimum-results-for-search="-1" required>
                                                <option value="" selected disabled>@lang('Select One')</option>
                                                <option value="{{ Status::CONTACT }}">@lang('Contacts')</option>
                                                <option value="{{ Status::GROUP }}">@lang('Groups')</option>
                                                <option value="{{ Status::DIRECT_INPUT }}">@lang('Direct Input')</option>
                                                <option value="{{ Status::DIRECT_INPUT_FROM_FILE }}">@lang('Direct Input From File')</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 common contact-wrapper">
                                        <div class="form-group unchanged">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                <label class="required">@lang('Mobile Numbers From Contact List')</label>
                                                <div class="form-check">
                                                    <input class="form-check-input select-all-contact" name="select_all_contact" type="checkbox" value="1" id="allContacts">
                                                    <label class="form-check-label" for="allContacts">
                                                      @lang('Select All Contacts')
                                                    </label>
                                                  </div>
                                            </div>
                                            <select class="form-control select2--dropdown contact-list" name="contact_list[]"
                                                multiple></select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 common group-wrapper d-none">
                                        <div class="form-group unchanged">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                <label class="required">@lang('Mobile Numbers From Group')</label>
                                                <div class="form-check">
                                                    <input class="form-check-input select-all-group" name="select_all_group" type="checkbox" value="1" id="allGroups">
                                                    <label class="form-check-label" for="allGroups">
                                                      @lang('Select All Groups')
                                                    </label>
                                                  </div>
                                            </div>
                                            <select class="form-control select2--dropdown group-list" name="group[]"
                                                multiple>
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}">{{ __($group->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 common country-wrapper d-none">
                                        <div class="form-group">
                                            <label class="form-label required">@lang('Country')</label>
                                            <select name="country" class="form-control form--control select2--dropdown">
                                                @foreach ($countries as $key => $country)
                                                    <option data-mobile_code="{{ $country->dial_code }}"
                                                        value="{{ $country->country }}" data-code="{{ $key }}">
                                                        {{ __($country->country) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 common mobile-wrapper d-none">
                                        <div class="form-group">
                                            <label class="form-label required">@lang('Mobile Numbers')</label>
                                            <input type="hidden" name="mobile_code">
                                            <input type="hidden" name="country_code">
                                            <textarea name="mobile_numbers" class="form-control form--control" placeholder="@lang('Enter each number on a new line. Press Enter or Space after typing a number.')">{{ old('mobile_numbers') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 common file-wrapper d-none">
                                        <div class="form-group">
                                            <label>@lang('Upload File') </label> <strong class="uploadInFo"><i
                                                    class="fas fa-info-circle text--primary"></i></strong>
                                            <input type="file" class="form-control form--control uploadFile"
                                                id="uploadFile" accept=".txt,.csv,.xlsx" name="file">
                                            <small class="file-size float-end text--primary"></small>
                                            <x-file-upload-link />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="border-line-area mt-4 mb-2">
                                            <h6 class="border-line-title">@lang('Write Message')</h6>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Template')</label>
                                        <select name="template_id" class="form-control form--control select2--dropdown"
                                            data-minimum-results-for-search="-1">
                                            <option value="" selected disabled>@lang('Select Template')</option>
                                            @foreach ($templates as $template)
                                                <option value="{{ @$template->id }}" @selected(old('template_id'))
                                                    data-message="{{ @$template->message }}">
                                                    {{ __(@$template->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('Message')</label>
                                        <textarea name="message" class="form--control" required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>@lang('Shortcode')</label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach (showShortCodes() as $value)
                                                <span class="btn btn--sm btn--danger shortcode-btn"
                                                    data-value="{{ $value }}">{{ $value }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                        <div class="phone-preview-wrapper">
                            <div class="phone-preview bg-img"
                                data-background-image="{{ asset($activeTemplateTrue . 'images/dashboard/phone.png') }}">
                                <div class="border">
                                    <div class="phone-screen">
                                        <div class="chat">
                                            <div class="messages">
                                            </div>
                                            <div class="bottom-area">
                                                <input type="text" disabled placeholder="@lang('Your message')"></input>
                                                <button class="btn" disabled>@lang('Send')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal custom--modal fade" id="info-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('File Upload Information')</h5>
                        <button type="button" class="btn close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="la la-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert--warning p-3 text-center" role="alert">
                                <p>
                                    @lang('The file you wish to upload has to be formatted as we provided template files. Any changes to these files will be considered as an invalid file format. Download links are provided below.')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

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
            "use strict";

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', true).trigger("change");
            @endif

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));

            $("select[name=device]").on('change', function(e) {
                generateSimOptions($(this));
            });

            if ($("select[name=device]").val()) {
                generateSimOptions($("select[name=device]"));
            }

            function generateSimOptions(device) {
                let sim = $(device).find('option:selected').data('sim');
                let html = "<option value='' selected disabled>@lang('Please select one')</option>";
                let selected = $("select[name=sim]").data('selected');
                if (sim) {
                    sim.forEach((element, index) => {
                    html +=
                        `<option value="${index}">${element.slot}-${element.name}</option>`
                    });
                }
                $("select[name=sim]").html(html);
            }

            function getContactList() {
                $('.contact-list').select2({
                    ajax: {
                        url: "{{ route('user.contact.search') }}",
                        type: "get",
                        dataType: 'json',
                        delay: 1000,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page,
                            };
                        },
                        processResults: function(response, params) {
                            params.page = params.page || 1;
                            let data = response.contacts.data;
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.mobile,
                                        id: item.id
                                    }
                                }),
                                pagination: {
                                    more: response.more
                                }
                            };
                        },
                        cache: false
                    },

                });
            }

            getContactList();
            $('.contact-wrapper').addClass('d-none');

            $("select[name=schedule]").on('change', function(e) {
                let schedule = $(this).val();
                if (schedule == 1) {
                    $("#date").addClass('d-none');
                } else {
                    $("#date").removeClass('d-none');
                }
            });

            $(".uploadInFo").on('click', function(e) {
                $("#info-modal").modal('show');
            });

            $(".uploadFile").on('change', function(e) {
                let file = e.target.files[0];
                let fileExtention = file.name.split('.').pop();
                if (fileExtention != 'csv' && fileExtention != 'xlsx' && fileExtention != 'txt') {
                    notify('error', "@lang('Title')");
                    document.querySelector('.uploadFile').value = '';
                    return false;
                }
                let size = fileSize(file.size);
                $(".file-size").text(size);
            });

            ///get the file size on js file upload
            function fileSize(bytes) {

                let marker = 1024; // Change to 1000 if required
                let kiloBytes = marker; // One Kilobyte is 1024 bytes
                let megaBytes = marker * marker; // One MB is 1024 KB
                let gigaBytes = marker * marker * marker; // One GB is 1024 MB
                let teraBytes = marker * marker * marker * marker; // One TB is 1024 GB

                if (bytes < kiloBytes) return bytes + " Bytes";
                else if (bytes < megaBytes) return (bytes / kiloBytes).toFixed(2) + " KB";
                else if (bytes < gigaBytes) return (bytes / megaBytes).toFixed(2) + " MB";
                else return (bytes / gigaBytes).toFixed(2) + " GB";
            }

            $('.select-all-contact').on('click', function(e) {
                if (this.checked) {
                    let message = "@lang('All Contacts')";
                    $('.contact-list').html(
                        `<option value="select_all_contact" selected disabled>${message}</option>`);
                    $('.contact-list').attr('disabled', true);
                } else {
                    $('.contact-list').find(`option[value=select_all_contact]`).remove();
                    $('.contact-list').attr('disabled', false);
                }
            });
            $('.select-all-group').on('click', function(e) {
                if (this.checked) {
                    let message = "@lang('All Groups')";
                    $('.group-list').html(
                        `<option value="select_all_group" selected disabled>${message}</option>`);
                    $('.group-list').attr('disabled', true);
                } else {
                    $('.group-list').find(`option[value=select_all_group]`).remove();
                    $('.group-list').attr('disabled', false);
                    let html = "";
                    @foreach ($groups as $group)
                        html += `<option value="{{ $group->id }}">{{ __($group->name) }}</option>`;
                    @endforeach
                    $('.group-list').html(html);
                    $('.group-list').select2({
                        dropdownParent: $('.group-wrapper').find('form-group')
                    });
                }
            });

            $("select[name=template_id]").on('change', function(e) {
                let message = $("select[name=template_id]").find('option:selected').data('message');
                $("textarea[name=message]").val(message);
                messagePreview();
            });

            $("textarea[name=message]").on('keyup keypress change keydown paste', function(e) {
                messagePreview();
            });

            if ($("textarea[name=message]").val()) {
                messagePreview();
            }

            $('.shortcode-btn').on('click', function (e) {
                let value = $(this).data('value');
                let textarea = $("textarea[name=message]");
                let cursorPos = textarea.prop("selectionStart");

                let text = textarea.val();

                let newText = text.substring(0, cursorPos) + value + text.substring(cursorPos);
                textarea.val(newText);

                textarea.focus();
                textarea.prop("selectionStart", cursorPos + value.length);
                textarea.prop("selectionEnd", cursorPos + value.length);

                messagePreview();
            });

            $('.date').datepicker({
                language: 'en',
                dateFormat: 'yyyy-mm-dd',
                timepicker: true,
                minDate: new Date(),
                timeFormat: 'hh:ii'
            });

            $('.date, .time-picker').on('keypress keyup change paste', function(e) {
                return false;
            });

            function messagePreview() {
                let message = $("textarea[name=message]").val();

                if (message.trim() === "") {
                    $('.phone-screen .chat .messages').html("");
                } else {
                    let formattedMessage = message.replace(/\n/g, "<br>");

                    $('.phone-screen .chat .messages').html(`
                        <div class="myMessage">${formattedMessage}</div>
                    `);
                }
            }

            $('.selectionType').on('change', function(){
                let type = $(this).val();

                $('.common').addClass('d-none');

                $('.contact-list').removeAttr('required');
                $('.group-list').removeAttr('required');
                $('[name=country]').removeAttr('required');
                $('[name=mobile_numbers]').removeAttr('required');
                $('[name=file]').removeAttr('required');

                $('.contact-list').val('').trigger('change');
                $('[name=select_all_contact]').prop('checked', false);
                $('.contact-list').attr('disabled', false);
                $('.group-list').val('').trigger('change');
                $('.group-list').attr('disabled', false);
                $('[name=select_all_group]').prop('checked', false);
                $('[name=mobile_numbers]').val('');
                $('[name=file]').val('');

                if(type == "{{ Status::CONTACT }}"){
                    $('.contact-wrapper').removeClass('d-none');
                    $('.contact-list').attr('required', true);

                }else if(type == "{{ Status::GROUP }}"){
                    $('.group-wrapper').removeClass('d-none');
                    $('.group-list').attr('required', true);

                }else if(type == "{{ Status::DIRECT_INPUT }}"){
                    $('.mobile-wrapper').removeClass('d-none');
                    $('.country-wrapper').removeClass('d-none');
                    $('[name=country]').attr('required', true);
                    $('[name=mobile_numbers]').attr('required', true);
                }else if(type == "{{ Status::DIRECT_INPUT_FROM_FILE }}"){
                    $('.file-wrapper').removeClass('d-none');
                    $('[name=file]').attr('required', true);
                }
            });

            $('[name=mobile_numbers]').on('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();

                    let textarea = $(this);
                    let value = textarea.val().trim() + '\n';

                    textarea.val(value);
                    textarea.prop({ selectionStart: value.length, selectionEnd: value.length });
                }
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .mobile-wrapper textarea{
            height: 215px;
        }

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

        .bottom-area {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0;
            background: #fff;
            display: flex;
            border-top: 1px solid hsl(var(--dark) / .1);
        }

        .bottom-area input {
            border: 0;
            padding-inline: 12px;
            flex: 1;
        }

        .bottom-area .btn {
            flex-shrink: 0;
            padding-inline: 12px;
            border: 0;
        }

        .shortcode-btn {
            cursor: pointer;
            padding: 4px 5px !important;
            font-size: 12px !important;
        }

        .phone-preview {
            width: 280px;
            height: 560px;
            border-radius: 10px !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            position: relative;
        }

        .campaign_wrapper .border {
            position: absolute;
            top: 12.1%;
            bottom: 11.6%;
            left: 50%;
            transform: translateX(-50%);
            overflow: hidden;
            width: calc(100% - 40px);
            border: 2px solid hsl(var(--dark) / .2) !important;
        }

        .chat .messages {
            display: block;
            overflow-x: hidden;
            /* overflow-y: scroll; */
            position: relative;
            height: 90%;
            width: 100%;
            padding: 2% 3%;
        }

        .chat .message {
            display: block;
            width: 98%;
            padding: 0.5%;
        }

        .chat .myMessage,
        .chat .fromThem {
            max-width: 90%;
        }

        .chat .myMessage,
        form.chat .fromThem {
            font-size: 12px;
        }

        .chat .myMessage {
            background: hsl(var(--dark) / .05);
            color: #000b16;
            float: right;
            clear: both;
            border-bottom-right-radius: 20px 0px \9;
        }

        .chat .myMessage:before {
            content: "";
            position: absolute;
            z-index: 1;
            bottom: -2px;
            right: -12px;
            height: 20px;
            border-right: 25px solid #f2f4f4;
            border-bottom-left-radius: 16px 14px;
            transform: translate(0, -2px);
        }

        .chat .myMessage:after {
            content: "";
            position: absolute;
            z-index: 1;
            bottom: -2px;
            right: -42px;
            width: 12px;
            height: 20px;
            background: white;
            border-bottom-left-radius: 10px;
            -webkit-transform: translate(-30px, -2px);
            transform: translate(-30px, -2px);
        }

        .chat .myMessage,
        .fromThem {
            position: relative;
            padding: 10px 20px;
            color: hsl(var(--dark));
            border-radius: 25px;
            clear: both;
            font: 400 15px 'Open Sans', sans-serif;
        }

        .chat .myMessage,
        .chat .fromThem {
            max-width: 80%;
            word-wrap: break-word;
            margin-bottom: 20px;
            margin-right: 12px;
        }

        .create-campaign {
            display: flex;
            align-items: flex-start;
            gap: 32px;
        }

        .create-campaign-form {
            flex: 1;
        }

        .phone-preview-wrapper {
            position: sticky;
            top: 200px;
            flex-shrink: 0;
        }

        .custom--card:has(.phone-preview-wrapper) {
            overflow: unset;
        }

        @media (max-width: 767px) {
            .phone-preview-wrapper {
                display: none;
            }
        }
    </style>
@endpush
