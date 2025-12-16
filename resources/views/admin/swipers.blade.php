@extends('admin.layout.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Swipers</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Swipers</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Form -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Add Swiper Slide</h5>
                <p class="card-title-desc">Manage homepage swiper slides</p>

                <form action="{{ url('/admin/addSwiper') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if(!$setting->swiper_video_only)
                        <!-- Image Mode -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingTitleInput" name="title" placeholder="Enter Slide Title">
                            <label for="floatingTitleInput">Title</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingSubtitleInput" name="subtitle" placeholder="Enter Slide Subtitle">
                            <label for="floatingSubtitleInput">Subtitle</label>
                        </div>

                        <fieldset class="mb-3">
                            <p>Add Image Slide</p>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </fieldset>
                    @else
                        @if($swipers->where('is_video', true)->count() > 0)
                            <div class="alert alert-warning">
                                A video swiper already exists. You cannot add another.
                            </div>
                        @else
                            <!-- Video Mode Input Fields -->
                            <fieldset class="mb-3">
                                <p>Add Video URL</p>
                                <input type="url" name="video_url" class="form-control mb-2" placeholder="Enter YouTube or online video link" required>
                            </fieldset>

                            <fieldset class="mb-3">
                                <p>Video Titles & Subtitles</p>
                                <div id="videoTextContainer">
                                    <div class="row mb-2 video-text-entry">
                                        <div class="col-6">
                                            <input type="text" name="slides_text[0][title]" class="form-control" placeholder="Title" required>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="slides_text[0][subtitle]" class="form-control" placeholder="Subtitle" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm" id="addVideoText">+ Add More</button>
                            </fieldset>
                        @endif
                    @endif

                    <div>
                        <button type="submit" class="btn btn-primary w-md float-end">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Swiper Slides</h4>
                <hr>

                <div class="accordion" id="swiperAccordion">
                    @forelse($swipers as $index => $swiper)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button fw-medium {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                    Swiper {{ $index + 1 }}
                                    <span class="badge bg-{{ $swiper->is_video ? 'danger' : 'primary' }} ms-2">
                                        {{ $swiper->is_video ? 'Video' : 'Image' }}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#swiperAccordion">
                                <div class="accordion-body">
                                    <div class="row align-items-center">
                                        <!-- Left Side: Text -->
                                        <div class="col-md-6">
                                            @if(!$swiper->is_video)
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Title</label>
                                                    <div>{{ $swiper->title }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Subtitle</label>
                                                    <div>{{ $swiper->subtitle }}</div>
                                                </div>
                                            @else
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Titles & Subtitles</label>
                                                    @foreach($swiper->slides_text as $slide)
                                                        <div><strong>{{ $slide['title'] }}</strong>: {{ $slide['subtitle'] }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Right Side: Media -->
                                        <div class="col-md-6 text-center">
                                            @if($swiper->is_video && $swiper->video_url)
                                                <iframe width="100%" height="200" src="{{ $swiper->video_url }}" frameborder="0" allowfullscreen style="border-radius: 6px;"></iframe>
                                            @elseif($swiper->image)
                                                <img src="{{ asset($swiper->image) }}" alt="Swiper Image" class="img-fluid float-end" style="max-width: 80%; border-radius: 6px;">
                                            @else
                                                <div class="text-muted">No media available</div>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editSwiper{{ $swiper->id }}" class="btn btn-info"><i class="mdi mdi-pencil"></i></a>
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deleteSwiper{{ $swiper->id }}" class="btn btn-danger"><i class="mdi mdi-delete"></i></a>

                                        <!-- Edit Modal -->
                                        <div id="editSwiper{{ $swiper->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0">
                                                    <div class="modal-header p-3">
                                                        <h5 class="modal-title">Edit Swiper Slide</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="{{ url('/admin/editSwiper') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="swiper_id" value="{{ $swiper->id }}">

                                                            <div class="row">
                                                                <!-- Left: Text Fields -->
                                                                <div class="col-md-7">
                                                                    @if($swiper->is_video)
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Video URL</label>
                                                                            <input type="url" name="video_url" class="form-control" value="{{ $swiper->video_url }}" required>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Titles & Subtitles</label>
                                                                            <div id="editVideoTextContainer{{ $swiper->id }}">
                                                                                @foreach($swiper->slides_text as $index => $slide)
                                                                                    <div class="row mb-2 video-text-entry">
                                                                                        <div class="col-6">
                                                                                            <input type="text" name="slides_text[{{ $index }}][title]" class="form-control" placeholder="Title" value="{{ $slide['title'] }}" required>
                                                                                        </div>
                                                                                        <div class="col-6">
                                                                                            <input type="text" name="slides_text[{{ $index }}][subtitle]" class="form-control" placeholder="Subtitle" value="{{ $slide['subtitle'] }}" required>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="addEditVideoText({{ $swiper->id }}, {{ count($swiper->slides_text) }})">+ Add More</button>
                                                                        </div>
                                                                    @else
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Title</label>
                                                                            <input type="text" name="title" class="form-control" value="{{ $swiper->title }}" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-semibold">Subtitle</label>
                                                                            <input type="text" name="subtitle" class="form-control" value="{{ $swiper->subtitle }}" required>
                                                                        </div>
                                                                    @endif
                                                                </div>

                                                                <!-- Right: Media Preview & Upload -->
                                                                <div class="col-md-5 text-center">
                                                                    @if(!$swiper->is_video)
                                                                        <label class="form-label fw-semibold">Current Image</label>
                                                                        <div>
                                                                            <img src="{{ asset($swiper->image) }}" alt="Swiper Image" class="img-fluid mb-2" style="max-width: 100%; border-radius: 8px;">
                                                                        </div>
                                                                        <input type="file" name="image" class="form-control mt-2" accept="image/*">
                                                                    @else
                                                                        <label class="form-label fw-semibold">Current Video Preview</label>
                                                                        <div>
                                                                            <iframe width="100%" height="150" src="{{ $swiper->video_url }}" frameborder="0" allowfullscreen style="border-radius: 8px;"></iframe>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="text-end mt-3">
                                                                <button type="submit" class="btn btn-primary">Update Swiper</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div id="deleteSwiper{{ $swiper->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-body text-center p-4">
                                                        <div class="text-end">
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="mt-2">
                                                            <h4>Are you sure?</h4>
                                                            <p class="text-muted">You are about to delete this Swiper?</p>

                                                            <form action="{{ url('/admin/deleteSwiper') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="swiper_id" value="{{ $swiper->id }}">
                                                                <div class="mt-3">
                                                                    <button type="submit" class="btn btn-danger w-100">Yes, Delete</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning mt-3" role="alert">
                            No swiper slides yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let videoIndex = 1; // Starts after the first entry

            const addBtn = document.getElementById('addVideoText');
            const container = document.getElementById('videoTextContainer');

            if (addBtn && container) {
                addBtn.addEventListener('click', function () {
                    const entry = document.createElement('div');
                    entry.classList.add('row', 'mb-2', 'video-text-entry');
                    entry.innerHTML = `
                        <div class="col-6">
                            <input type="text" name="slides_text[${videoIndex}][title]" class="form-control" placeholder="Title" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="slides_text[${videoIndex}][subtitle]" class="form-control" placeholder="Subtitle" required>
                        </div>
                    `;
                    container.appendChild(entry);
                    videoIndex++;
                });
            }
        });
    </script>

    <script>
        function addEditVideoText(swiperId, startIndex) {
            const container = document.getElementById(`editVideoTextContainer${swiperId}`);
            let index = startIndex;

            const entry = document.createElement('div');
            entry.classList.add('row', 'mb-2', 'video-text-entry');
            entry.innerHTML = `
                <div class="col-6">
                    <input type="text" name="slides_text[${index}][title]" class="form-control" placeholder="Title" required>
                </div>
                <div class="col-6">
                    <input type="text" name="slides_text[${index}][subtitle]" class="form-control" placeholder="Subtitle" required>
                </div>
            `;
            container.appendChild(entry);
            index++;
        }
    </script>


@endsection

