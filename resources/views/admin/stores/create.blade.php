@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<title>Add Store - Lion POS</title>
@endpush

@section('content')

<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold mb-2">Add New Store</h4>
            <h6 class="text-muted">Create a new store under your multistore system</h6>
        </div>
        <div class="right-items">
            <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-primary">
                <i class="fa fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Store Create Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.stores.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-store me-2"></i>Basic Information
                        </h5>
                    </div>

                    {{-- Store Name --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                Store Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-store text-primary"></i>
                                </span>
                                <input type="text" name="store_name" class="form-control"
                                       placeholder="Enter your store name"
                                       value="{{ old('store_name') }}" required>
                            </div>
                            @error('store_name')
                                <div class="text-danger small mt-1">
                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Owner Name --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-user me-1 text-muted"></i>Owner Name
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-user text-primary"></i>
                                </span>
                                <input type="text" name="owner_name" class="form-control"
                                       placeholder="Store owner name"
                                       value="{{ old('owner_name') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Store Slug --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-link me-1 text-muted"></i>Store Slug
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-link text-primary"></i>
                                </span>
                                <input type="text" name="slug" class="form-control"
                                       placeholder="store-slug"
                                       value="{{ old('slug') }}">
                            </div>
                            <small class="text-muted">Leave empty to auto-generate from store name</small>
                        </div>
                    </div>

                    {{-- Website --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-globe me-1 text-muted"></i>Website URL
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-globe text-primary"></i>
                                </span>
                                <input type="url" name="website" class="form-control"
                                       placeholder="https://example.com"
                                       value="{{ old('website') }}">
                            </div>
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

                    {{-- Store Email --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-envelope me-1 text-muted"></i>Store Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-envelope text-primary"></i>
                                </span>
                                <input type="email" name="email" class="form-control"
                                       placeholder="example@mail.com"
                                       value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">
                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-phone me-1 text-muted"></i>Phone Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-phone text-primary"></i>
                                </span>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="+91 98765 43210"
                                       value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Tax ID --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-id-card me-1 text-muted"></i>Tax ID / GST
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-id-card text-primary"></i>
                                </span>
                                <input type="text" name="tax_id" class="form-control"
                                       placeholder="GSTIN/Tax Identification Number"
                                       value="{{ old('tax_id') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Currency --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-money-bill me-1 text-muted"></i>Currency
                            </label>
                            <select name="currency" class="form-select">
                                <option value="INR" {{ old('currency') == 'INR' ? 'selected' : '' }}>₹ INR - Indian Rupee</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>$ USD - US Dollar</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>€ EUR - Euro</option>
                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>£ GBP - British Pound</option>
                                <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED - UAE Dirham</option>
                                <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR - Saudi Riyal</option>
                            </select>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="col-lg-12 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-map-marker-alt me-1 text-muted"></i>Store Address
                            </label>
                            <textarea class="form-control" name="address" rows="3"
                                      placeholder="Enter full store address">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    {{-- Location Coordinates --}}
                    <div class="col-lg-6 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fa fa-location-dot me-1 text-muted"></i>Latitude
                                </label>
                                <input type="text" name="latitude" class="form-control"
                                       placeholder="28.6139"
                                       value="{{ old('latitude') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fa fa-location-dot me-1 text-muted"></i>Longitude
                                </label>
                                <input type="text" name="longitude" class="form-control"
                                       placeholder="77.2090"
                                       value="{{ old('longitude') }}">
                            </div>
                        </div>
                        <small class="text-muted">For store location on maps</small>
                    </div>

                    {{-- Timezone --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-clock me-1 text-muted"></i>Timezone
                            </label>
                            <select name="timezone" class="form-select">
                                <option value="Asia/Kolkata" {{ old('timezone') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                                <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                <option value="Asia/Singapore" {{ old('timezone') == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (SGT)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-images me-2"></i>Media & Images
                        </h5>
                    </div>

                    {{-- Store Logo --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-image me-1 text-muted"></i>Store Logo
                            </label>
                            <div class="card border-dashed">
                                <div class="card-body text-center p-4">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-2">Click to upload logo</p>
                                    <input type="file" name="logo" class="form-control"
                                           onchange="previewLogo(this)">
                                    <div class="mt-3" id="logoPreview"></div>
                                    <small class="text-muted d-block mt-2">
                                        Recommended: 300x300px, PNG/JPG, Max: 2MB
                                    </small>
                                </div>
                            </div>
                            @error('logo')
                                <div class="text-danger small mt-1">
                                    <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    {{-- Store Banner --}}
                    <div class="col-lg-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-image me-1 text-muted"></i>Store Banner
                            </label>
                            <div class="card border-dashed">
                                <div class="card-body text-center p-4">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-2">Click to upload banner</p>
                                    <input type="file" name="banner" class="form-control"
                                           onchange="previewBanner(this)">
                                    <div class="mt-3" id="bannerPreview"></div>
                                    <small class="text-muted d-block mt-2">
                                        Recommended: 1200x400px, JPG/PNG, Max: 5MB
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Gallery Images --}}
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-images me-1 text-muted"></i>Gallery Images
                            </label>
                            <div class="card border-dashed">
                                <div class="card-body text-center p-4">
                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-2">Drag & drop or click to select multiple images</p>
                                    <input type="file" name="gallery[]" class="form-control" multiple
                                           onchange="previewGallery(this)">
                                    <div class="row mt-3" id="galleryPreview"></div>
                                    <small class="text-muted d-block mt-2">
                                        Max 10 images, 5MB each. Supports JPG, PNG, WebP
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opening Hours Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-clock me-2"></i>Opening Hours
                        </h5>
                        <p class="text-muted mb-3">Set your store's operating hours for each day</p>
                    </div>

                    @php
                        $days = [
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday',
                        ];
                    @endphp

                    @foreach($days as $key => $day)
                    <div class="col-lg-6 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-check-label fw-semibold">
                                        <input type="checkbox" name="opening_hours[{{ $key }}][open]"
                                               value="1" class="form-check-input day-checkbox"
                                               data-day="{{ $key }}" checked>
                                        {{ $day }}
                                    </label>
                                    <span class="badge bg-success">Open</span>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Opening Time</label>
                                        <input type="time" name="opening_hours[{{ $key }}][open_time]"
                                               class="form-control opening-time" data-day="{{ $key }}"
                                               value="09:00">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small">Closing Time</label>
                                        <input type="time" name="opening_hours[{{ $key }}][close_time]"
                                               class="form-control closing-time" data-day="{{ $key }}"
                                               value="21:00">
                                    </div>
                                </div>
                                <input type="hidden" name="opening_hours[{{ $key }}][open]" value="1"
                                       class="open-status" data-day="{{ $key }}">
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
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-heading me-1 text-muted"></i>Meta Title
                            </label>
                            <input type="text" name="meta_title" class="form-control"
                                   placeholder="Meta title for SEO (50-60 characters)"
                                   value="{{ old('meta_title') }}">
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" id="titleProgress"></div>
                            </div>
                            <small class="text-muted"><span id="titleCount">0</span>/60 characters</small>
                        </div>
                    </div>

                    {{-- Meta Description --}}
                    <div class="col-lg-12 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-align-left me-1 text-muted"></i>Meta Description
                            </label>
                            <textarea class="form-control" name="meta_description" rows="3"
                                      placeholder="Meta description for SEO (150-160 characters)">{{ old('meta_description') }}</textarea>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" id="descProgress"></div>
                            </div>
                            <small class="text-muted"><span id="descCount">0</span>/160 characters</small>
                        </div>
                    </div>

                    {{-- Meta Keywords --}}
                    <div class="col-lg-12 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-tags me-1 text-muted"></i>Meta Keywords
                            </label>
                            <input type="text" name="meta_keywords" class="form-control"
                                   placeholder="keyword1, keyword2, keyword3"
                                   value="{{ old('meta_keywords') }}">
                            <small class="text-muted">Separate keywords with commas</small>
                        </div>
                    </div>
                </div>

                <!-- Social Media Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-share-alt me-2"></i>Social Media Links
                        </h5>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-facebook text-primary"></i>
                            </span>
                            <input type="url" name="social_media[facebook]" class="form-control"
                                   placeholder="Facebook Profile URL"
                                   value="{{ old('social_media.facebook') }}">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-twitter text-info"></i>
                            </span>
                            <input type="url" name="social_media[twitter]" class="form-control"
                                   placeholder="Twitter Profile URL"
                                   value="{{ old('social_media.twitter') }}">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-instagram text-danger"></i>
                            </span>
                            <input type="url" name="social_media[instagram]" class="form-control"
                                   placeholder="Instagram Profile URL"
                                   value="{{ old('social_media.instagram') }}">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-linkedin text-primary"></i>
                            </span>
                            <input type="url" name="social_media[linkedin]" class="form-control"
                                   placeholder="LinkedIn Profile URL"
                                   value="{{ old('social_media.linkedin') }}">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-youtube text-danger"></i>
                            </span>
                            <input type="url" name="social_media[youtube]" class="form-control"
                                   placeholder="YouTube Channel URL"
                                   value="{{ old('social_media.youtube') }}">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fab fa-whatsapp text-success"></i>
                            </span>
                            <input type="text" name="social_media[whatsapp]" class="form-control"
                                   placeholder="WhatsApp Number"
                                   value="{{ old('social_media.whatsapp') }}">
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="fw-bold text-primary border-bottom pb-2 mb-4">
                            <i class="fa fa-info-circle me-2"></i>Additional Information
                        </h5>
                    </div>

                    {{-- Store Description --}}
                    <div class="col-lg-8 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-align-left me-1 text-muted"></i>Store Description
                            </label>
                            <textarea class="form-control" name="description" rows="4"
                                      placeholder="Write detailed description about your store...">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-lg-4 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="fa fa-toggle-on me-1 text-muted"></i>Status
                            </label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="1" class="text-success" {{ old('status', 1) == '1' ? 'selected' : '' }}>
                                    <i class="fa fa-circle-check me-2"></i>Active
                                </option>
                                <option value="0" class="text-danger" {{ old('status') == '0' ? 'selected' : '' }}>
                                    <i class="fa fa-circle-xmark me-2"></i>Inactive
                                </option>
                            </select>
                            <div class="form-text">
                                Active stores will be visible to customers
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2 p-3 bg-light rounded">
                            <button type="reset" class="btn btn-outline-secondary px-4">
                                <i class="fa fa-redo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fa fa-save me-2"></i>Save Store
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Image Preview Functions
    function previewLogo(input) {
        const preview = document.getElementById('logoPreview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-thumbnail';
                img.style.maxWidth = '150px';
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewBanner(input) {
        const preview = document.getElementById('bannerPreview');
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-fluid rounded';
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewGallery(input) {
        const preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';

        if (input.files) {
            for (let i = 0; i < input.files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-lg-3 col-md-4 col-sm-6 mb-3';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.width = '100%';
                    img.style.height = '120px';
                    img.style.objectFit = 'cover';

                    col.appendChild(img);
                    preview.appendChild(col);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    }

    // SEO Character Counters
    document.addEventListener('DOMContentLoaded', function() {
        // Meta Title Counter
        const titleInput = document.querySelector('input[name="meta_title"]');
        const titleCount = document.getElementById('titleCount');
        const titleProgress = document.getElementById('titleProgress');

        titleInput.addEventListener('input', function() {
            const length = this.value.length;
            titleCount.textContent = length;

            const percentage = Math.min((length / 60) * 100, 100);
            titleProgress.style.width = percentage + '%';

            if (length > 60) {
                titleProgress.classList.remove('bg-success', 'bg-warning');
                titleProgress.classList.add('bg-danger');
            } else if (length > 50) {
                titleProgress.classList.remove('bg-success', 'bg-danger');
                titleProgress.classList.add('bg-warning');
            } else {
                titleProgress.classList.remove('bg-warning', 'bg-danger');
                titleProgress.classList.add('bg-success');
            }
        });

        // Meta Description Counter
        const descInput = document.querySelector('textarea[name="meta_description"]');
        const descCount = document.getElementById('descCount');
        const descProgress = document.getElementById('descProgress');

        descInput.addEventListener('input', function() {
            const length = this.value.length;
            descCount.textContent = length;

            const percentage = Math.min((length / 160) * 100, 100);
            descProgress.style.width = percentage + '%';

            if (length > 160) {
                descProgress.classList.remove('bg-success', 'bg-warning');
                descProgress.classList.add('bg-danger');
            } else if (length > 150) {
                descProgress.classList.remove('bg-success', 'bg-danger');
                descProgress.classList.add('bg-warning');
            } else {
                descProgress.classList.remove('bg-warning', 'bg-danger');
                descProgress.classList.add('bg-success');
            }
        });

        // Opening Hours Toggle
        document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const day = this.dataset.day;
                const card = this.closest('.card');
                const badge = card.querySelector('.badge');
                const timeInputs = card.querySelectorAll('.opening-time, .closing-time');
                const hiddenInput = card.querySelector('.open-status');

                if (this.checked) {
                    badge.textContent = 'Open';
                    badge.className = 'badge bg-success';
                    timeInputs.forEach(input => input.disabled = false);
                    hiddenInput.value = '1';
                } else {
                    badge.textContent = 'Closed';
                    badge.className = 'badge bg-secondary';
                    timeInputs.forEach(input => input.disabled = true);
                    hiddenInput.value = '0';
                }
            });
        });

        // Auto-generate slug from store name
        const storeNameInput = document.querySelector('input[name="store_name"]');
        const slugInput = document.querySelector('input[name="slug"]');

        storeNameInput.addEventListener('blur', function() {
            if (!slugInput.value.trim()) {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^\w\s]/gi, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                slugInput.value = slug;
            }
        });
    });
</script>
@endpush

@endsection
