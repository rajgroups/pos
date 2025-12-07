@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<title>Edit Store - Lion POS</title>
@endpush

@section('content')

<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold mb-2">Edit Store</h4>
            <h6 class="text-muted">Update the details of this store</h6>
        </div>
        <div class="right-items">
            <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-primary">
                <i class="fa fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Store Edit Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.stores.update', $store->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-store me-2"></i>Basic Information
                        </h5>
                    </div>

                    {{-- Store Name --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Store Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-store text-primary"></i></span>
                            <input type="text" name="store_name" class="form-control"
                                   value="{{ old('store_name', $store->store_name) }}" required>
                        </div>
                    </div>

                    {{-- Owner Name --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Owner Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-user text-primary"></i></span>
                            <input type="text" name="owner_name" class="form-control"
                                   value="{{ old('owner_name', $store->owner_name) }}">
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Store Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-link text-primary"></i></span>
                            <input type="text" name="slug" class="form-control"
                                   value="{{ old('slug', $store->slug) }}">
                        </div>
                        <small class="text-muted">Leave empty to auto-generate</small>
                    </div>

                    {{-- Website --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Website URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-globe text-primary"></i></span>
                            <input type="url" name="website" class="form-control"
                                   value="{{ old('website', $store->website) }}">
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-address-book me-2"></i>Contact Information
                        </h5>
                    </div>

                    {{-- Email --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-envelope text-primary"></i></span>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $store->email) }}">
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-phone text-primary"></i></span>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $store->phone) }}">
                        </div>
                    </div>

                    {{-- Tax ID --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Tax ID / GST</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa fa-id-card text-primary"></i></span>
                            <input type="text" name="tax_id" class="form-control"
                                   value="{{ old('tax_id', $store->tax_id) }}">
                        </div>
                    </div>

                    {{-- Currency --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Currency</label>
                        <select name="currency" class="form-select">
                            @foreach(['INR','USD','EUR','GBP','AED','SAR'] as $cur)
                                <option value="{{ $cur }}" {{ old('currency', $store->currency) == $cur ? 'selected' : '' }}>
                                    {{ $cur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Address --}}
                    <div class="col-lg-12 mb-3">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $store->address) }}</textarea>
                    </div>

                    {{-- Lat/Lng --}}
                    <div class="col-lg-6 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Latitude</label>
                                <input type="text" name="latitude" class="form-control"
                                       value="{{ old('latitude', $store->latitude) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Longitude</label>
                                <input type="text" name="longitude" class="form-control"
                                       value="{{ old('longitude', $store->longitude) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Timezone --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Timezone</label>
                        <select name="timezone" class="form-select">
                            @foreach(['Asia/Kolkata','Asia/Dubai','America/New_York','Europe/London','Asia/Singapore'] as $tz)
                                <option value="{{ $tz }}" {{ old('timezone', $store->timezone) == $tz ? 'selected' : '' }}>
                                    {{ $tz }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-images me-2"></i>Media & Images
                        </h5>
                    </div>

                    {{-- Logo --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Store Logo</label>
                        <div class="mb-2">
                            <img src="{{ $store->logo_url }}" class="img-thumbnail" width="120">
                        </div>
                        <input type="file" name="logo" class="form-control" onchange="previewLogo(this)">
                        <div id="logoPreview"></div>
                    </div>

                    {{-- Banner --}}
                    <div class="col-lg-6 mb-3">
                        <label class="form-label fw-semibold">Store Banner</label>
                        <div class="mb-2">
                            <img src="{{ $store->banner_url }}" class="img-fluid rounded" style="max-height:120px;">
                        </div>
                        <input type="file" name="banner" class="form-control" onchange="previewBanner(this)">
                        <div id="bannerPreview"></div>
                    </div>

                    {{-- Gallery --}}
                    <div class="col-lg-12 mb-3">
                        <label class="form-label fw-semibold">Gallery Images</label>

                        <!-- Existing Gallery -->
                        <div class="row mb-3">
                            @foreach($store->gallery_urls as $img)
                                <div class="col-lg-2 col-md-3 col-sm-4 mb-3 text-center">
                                    <img src="{{ $img }}" class="img-thumbnail" style="height:120px;object-fit:cover;">

                                    <input type="checkbox" name="gallery_keep[]" value="{{ $store->gallery[$loop->index] }}" checked>
                                    <span class="small">Keep</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Upload new gallery -->
                        <input type="file" name="gallery[]" class="form-control" multiple onchange="previewGallery(this)">
                        <div class="row mt-3" id="galleryPreview"></div>
                    </div>
                </div>

                <!-- Opening Hours Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-clock me-2"></i>Opening Hours
                        </h5>
                    </div>

                    @php
                        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    @endphp

                    @foreach($days as $day)
                    @php
                        $data = $store->opening_hours[$day] ?? ['open'=>1,'open_time'=>'09:00','close_time'=>'21:00'];
                    @endphp

                    <div class="col-lg-6 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <label class="form-check-label fw-semibold">
                                    <input type="checkbox"
                                           name="opening_hours[{{ $day }}][open]"
                                           value="1"
                                           class="form-check-input day-checkbox"
                                           {{ $data['open'] ? 'checked' : '' }}>
                                    {{ ucfirst($day) }}
                                </label>

                                <div class="row mt-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Open Time</label>
                                        <input type="time"
                                               name="opening_hours[{{ $day }}][open_time]"
                                               class="form-control"
                                               value="{{ $data['open_time'] }}"
                                               {{ !$data['open'] ? 'disabled' : '' }}>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Close Time</label>
                                        <input type="time"
                                               name="opening_hours[{{ $day }}][close_time]"
                                               class="form-control"
                                               value="{{ $data['close_time'] }}"
                                               {{ !$data['open'] ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <input type="hidden"
                                       name="opening_hours[{{ $day }}][open]"
                                       class="open-status"
                                       value="{{ $data['open'] }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- SEO Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-search me-2"></i>SEO Settings
                        </h5>
                    </div>

                    {{-- Meta Title --}}
                    <div class="col-lg-12 mb-3">
                        <label class="form-label fw-semibold">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="{{ old('meta_title', $store->meta_title) }}">
                    </div>

                    {{-- Meta Description --}}
                    <div class="col-lg-12 mb-3">
                        <label class="form-label fw-semibold">Meta Description</label>
                        <textarea name="meta_description" rows="3" class="form-control">{{ old('meta_description', $store->meta_description) }}</textarea>
                    </div>

                    @php
                        $keywords = $store->meta_keywords;

                        // If it's a string, convert it into array
                        if (is_string($keywords)) {
                            $keywords = explode(',', $keywords);
                        }

                        // If still null, make empty array
                        $keywords = $keywords ?? [];
                    @endphp

                    <input type="text" name="meta_keywords" class="form-control"
                        value="{{ old('meta_keywords', implode(',', $keywords)) }}">

                </div>

                <!-- Social Media Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-share-alt me-2"></i>Social Media Links
                        </h5>
                    </div>

                    @foreach(['facebook','twitter','instagram','linkedin','youtube','whatsapp'] as $sm)
                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-{{ $sm }} text-primary"></i>
                            </span>
                            <input type="text" name="social_media[{{ $sm }}]" class="form-control"
                                   value="{{ old("social_media.$sm", $store->social_media[$sm] ?? '') }}">
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Additional Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-info-circle me-2"></i>Additional Information
                        </h5>
                    </div>

                    {{-- Description --}}
                    <div class="col-lg-8 mb-3">
                        <label class="form-label fw-semibold">Store Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $store->description) }}</textarea>
                    </div>

                    {{-- Status --}}
                    <div class="col-lg-4 mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="1" {{ old('status', $store->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $store->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="d-flex justify-content-end gap-2 p-3 bg-light rounded">
                    <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fa fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa fa-save me-2"></i>Update Store
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    /** PREVIEW SCRIPTS (logo, banner, gallery) **/
    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        preview.innerHTML = '';
        if (input.files[0]) {
            let reader = new FileReader();
            reader.onload = e => {
                let img = document.createElement('img');
                img.className = 'img-thumbnail';
                img.style.maxWidth = '150px';
                img.src = e.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewBanner(input) {
        const preview = document.getElementById('bannerPreview');
        preview.innerHTML = '';
        if (input.files[0]) {
            let reader = new FileReader();
            reader.onload = e => {
                let img = document.createElement('img');
                img.className = 'img-fluid rounded';
                img.style.maxHeight = '150px';
                img.src = e.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewGallery(input) {
        const preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';
        for (let file of input.files) {
            let reader = new FileReader();
            reader.onload = e => {
                let col = document.createElement('div');
                col.className = 'col-md-2 mb-2';

                let img = document.createElement('img');
                img.className = 'img-thumbnail';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.src = e.target.result;

                col.appendChild(img);
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush

@endsection
