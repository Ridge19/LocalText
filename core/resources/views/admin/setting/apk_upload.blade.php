@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-md-12 mb-30">
            <div class="card bl--5 border--warning">
                <div class="card-body">
                    <p class="text--warning"><b>@lang('Warning:')</b> @lang('Please make sure you are uploading the correct APK build. Uploading an incorrect or unverified APK may cause system malfunctions and affect user experience.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.upload.apk') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                <label>@lang('Upload APK')</label>
                                <a href="{{ apkUrl() }}" download=""><i class="las la-download"></i> @lang('Download APK')</a>
                            </div>
                            <input type="file" accept=".apk" class="form-control" name="file" required>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
