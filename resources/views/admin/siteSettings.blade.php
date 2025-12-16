@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Site Settings</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Site Settings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Set Site Info</h5>
                <form action="{{ url('/admin/updateSiteInfo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="site_info_id" value="{{ !empty($setting) ? $setting->id : null }}">
                    <hr>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Swiper Display Settings</label>
                        <p class="text-danger mb-1">Enable this toggle if you want all swiper slides to show video only.</p>
                        <div class="form-check form-switch">
                            <input class="form-check-input form-check-input-lg" type="checkbox" id="swiperVideoOnly" name="swiper_video_only" value="1" {{ old('swiper_video_only', $setting?->swiper_video_only) ? 'checked' : '' }}>
                            <label class="form-check-label" for="swiperVideoOnly">Swiper Video Only</label>
                        </div>
                    </div>
                    <hr>
                    <br>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingnameInput" placeholder="Enter Site Name" name="site_name" value="{{ old('site_name', $setting?->site_name) }}">
                        <label for="floatingnameInput">Site Name</label>
                    </div>

                    <div class="form-floating mb-3">
                        <label for="floatingDescInput">Site Description</label>
                        <textarea name="description" class="form-control" id="floatingDescInput" cols="30" rows="10" placeholder="Site Description">{{ old('description', $setting?->description) }}</textarea>
                    </div>

                    <fieldset class="mb-3">
                        <p>Site Logo</p>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="floatingLogoWInput" name="logo">
                        </div>
                    </fieldset>

                    <fieldset class="mb-3">
                        <p>Site Favicon</p>
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="floatingFaviconInput" name="favicon">
                        </div>
                    </fieldset>


                    <div>
                        <button type="submit" class="btn btn-primary w-md">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Display Current Settings -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Current Settings</h5>
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td><strong>Swiper Video Only</strong></td>
                            <td>{{ $setting?->swiper_video_only ? 'Yes' : 'No' }}</td>
                        </tr>


                        <tr>
                            <td><strong>Site Name</strong></td>
                            <td>{{ $setting?->site_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Description</strong></td>
                            <td>{{ $setting?->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Logo</strong></td>
                            <td>
                                @if($setting?->logo)
                                    <img src="{{ asset($setting->logo) }}" alt="Logo" class="avatar-xl">
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Favicon</strong></td>
                            <td>
                                @if($setting?->favicon)
                                    <img src="{{ asset($setting->favicon) }}" alt="Favicon" class="avatar-xs rounded-circle">
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection