@extends('layouts.admin.app')
@push('meta')
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Lion POS is a powerful Bootstrap based Inventory Management Admin Template designed for businesses, offering seamless invoicing, project tracking, and estimates.">
    <meta name="keywords"
        content="inventory management, admin dashboard, bootstrap template, invoicing, estimates, business management, responsive admin, POS system">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    <title>Lion POS - Inventory Management & Admin Dashboard</title>
@endpush
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create New Vehicle Type</h4>
                <h6 class="text-muted">Add a new vehicle type to organize your fleet</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                        class="ti ti-chevron-up"></i></a>
            </li>
        </ul>
        <div class="page-btn">
            <a href="{{ route('admin.category.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Vehicle Types
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="ti ti-category me-2 text-primary"></i>
                Category Information
            </h5>
        </div>

        <form action="{{ route('admin.category.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-circle me-2 fs-5"></i>
                            <strong>There was a problem with your request:</strong>
                        </div>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-circle-check me-2 fs-5"></i>
                            {{ session()->get('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle me-2 fs-5"></i>
                            {{ session()->get('error') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Column 1: Basic Details -->
                    <div class="col-lg-6">
                        <h6 class="mb-3 border-bottom pb-2">Basic Details</h6>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Category Name <span class="text-danger ms-1">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Enter category name" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                URL Slug <span class="text-danger ms-1">*</span>
                            </label>
                            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="category-slug" required>
                            <div class="form-text">This will be used in URLs. Auto-generated from the name.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Parent Category</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Select Parent Category --</option>
                                @foreach ($categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Type Key</label>
                            <input type="text" name="type_key" class="form-control" value="{{ old('type_key') }}" placeholder="e.g., suv, sedan, mini">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Service Mode</label>
                            <select name="service_mode" class="form-select">
                                <option value="instant" {{ old('service_mode') == 'instant' ? 'selected' : '' }}>Instant</option>
                                <option value="scheduled" {{ old('service_mode') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="both" {{ old('service_mode') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tagline</label>
                            <input type="text" name="tagline" class="form-control" value="{{ old('tagline') }}">
                        </div>
                    </div>

                    <!-- Column 2: Styling and Settings -->
                    <div class="col-lg-6">
                        <h6 class="mb-3 border-bottom pb-2">Pricing & Configuration</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Starting Fare</label>
                                <input type="text" name="starting_fare" class="form-control" value="{{ old('starting_fare') }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Price Label</label>
                                <input type="text" name="price_label" class="form-control" value="{{ old('price_label') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">ETA</label>
                                <input type="text" name="eta" class="form-control" value="{{ old('eta') }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Max Capacity</label>
                                <input type="number" name="max_capacity" class="form-control" value="{{ old('max_capacity') }}" min="1">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Driver Search Radius (KM)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0.1" max="500"
                                       name="driver_search_radius_km"
                                       class="form-control"
                                       value="{{ old('driver_search_radius_km', 5.00) }}"
                                       placeholder="e.g. 5.00"
                                       required>
                                <span class="input-group-text">KM</span>
                            </div>
                            <small class="text-muted">
                                Maximum radius to search for drivers when a booking is requested for this category.
                                (e.g. Bike = 3, Sedan = 7, SUV = 10)
                            </small>
                        </div>

                        <h6 class="mb-3 border-bottom pb-2 mt-4">UI/Styling</h6>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-semibold">Accent Color</label>
                                <input type="color" name="accent_color" class="form-control form-control-color w-100" value="{{ old('accent_color', '#000000') }}">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-semibold">Gradient Start</label>
                                <input type="color" name="gradient_start" class="form-control form-control-color w-100" value="{{ old('gradient_start', '#000000') }}">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-semibold">Gradient End</label>
                                <input type="color" name="gradient_end" class="form-control form-control-color w-100" value="{{ old('gradient_end', '#000000') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Category Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Category Icon</label>
                                <input type="file" name="icon" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <h6 class="mb-3 border-bottom pb-2 mt-4">Status & Rules</h6>
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <div>
                                <span class="fw-semibold d-block">Category Status</span>
                                <span class="text-muted small">Enable or disable this category</span>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', 1) == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                        
                        <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <div>
                                <span class="fw-semibold d-block">Drop Location Required</span>
                                <span class="text-muted small">Require destination on booking</span>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="drop_location_required" value="0">
                                <input type="checkbox" class="form-check-input" name="drop_location_required" value="1" {{ old('drop_location_required', 1) == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light py-3 d-flex justify-content-end">
                <a href="{{ route('admin.category.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Save Category</button>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#name').on('keyup', function() {
                var name = $(this).val();
                var slug = name.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove non-word characters
                    .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                    .replace(/^-+|-+$/g, ''); // Trim hyphens from start and end
                $('#slug').val(slug);
            });
        });
    </script>
@endpush
