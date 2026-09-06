@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Vehicle</h4>
                <h6 class="text-muted">Update details for {{ $vehicle->vehicle_number }}</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Vehicles
            </a>
        </div>
    </div>

    <form action="{{ route('admin.vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Column: Basic Details & Compliance -->
            <div class="col-lg-8">
                <!-- Basic Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Basic Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Registration Number <span class="text-danger">*</span></label>
                                <input type="text" name="vehicle_number" class="form-control text-uppercase" value="{{ old('vehicle_number', $vehicle->vehicle_number) }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Vehicle Category <span class="text-danger">*</span></label>
                                <select name="vehicle_category_id" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        @if($category->subCategories->isNotEmpty())
                                            <optgroup label="{{ $category->name }}">
                                                @foreach($category->subCategories as $sub)
                                                    <option value="{{ $sub->id }}" {{ old('vehicle_category_id', $vehicle->vehicle_category_id) == $sub->id ? 'selected' : '' }}>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $category->id }}" {{ old('vehicle_category_id', $vehicle->vehicle_category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Brand <span class="text-danger">*</span></label>
                                <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand) }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Model <span class="text-danger">*</span></label>
                                <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model) }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Color <span class="text-danger">*</span></label>
                                <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color) }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Manufacture Year <span class="text-danger">*</span></label>
                                <input type="number" name="manufacture_year" class="form-control" value="{{ old('manufacture_year', $vehicle->manufacture_year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Seating Capacity <span class="text-danger">*</span></label>
                                <input type="number" name="seating_capacity" class="form-control" value="{{ old('seating_capacity', $vehicle->seating_capacity) }}" min="1" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Load Capacity (Tons)</label>
                                <input type="number" step="0.01" name="load_capacity" class="form-control" value="{{ old('load_capacity', $vehicle->load_capacity) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance & Documents -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Compliance Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- RC -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">RC Number</label>
                                <input type="text" name="rc_number" class="form-control text-uppercase" value="{{ old('rc_number', $vehicle->rc_number) }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">RC Expiry</label>
                                <input type="date" name="rc_expiry" class="form-control" value="{{ old('rc_expiry', $vehicle->rc_expiry ? \Carbon\Carbon::parse($vehicle->rc_expiry)->format('Y-m-d') : '') }}">
                            </div>
                            
                            <!-- Insurance -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Insurance Number</label>
                                <input type="text" name="insurance_number" class="form-control text-uppercase" value="{{ old('insurance_number', $vehicle->insurance_number) }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Insurance Expiry</label>
                                <input type="date" name="insurance_expiry" class="form-control" value="{{ old('insurance_expiry', $vehicle->insurance_expiry ? \Carbon\Carbon::parse($vehicle->insurance_expiry)->format('Y-m-d') : '') }}">
                            </div>

                            <!-- Permit -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Permit Number</label>
                                <input type="text" name="permit_number" class="form-control text-uppercase" value="{{ old('permit_number', $vehicle->permit_number) }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Permit Expiry</label>
                                <input type="date" name="permit_expiry" class="form-control" value="{{ old('permit_expiry', $vehicle->permit_expiry ? \Carbon\Carbon::parse($vehicle->permit_expiry)->format('Y-m-d') : '') }}">
                            </div>

                            <!-- Fitness -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Fitness Certificate Number</label>
                                <input type="text" name="fitness_certificate_number" class="form-control text-uppercase" value="{{ old('fitness_certificate_number', $vehicle->fitness_certificate_number) }}">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold">Fitness Expiry</label>
                                <input type="date" name="fitness_expiry" class="form-control" value="{{ old('fitness_expiry', $vehicle->fitness_expiry ? \Carbon\Carbon::parse($vehicle->fitness_expiry)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Images, Driver, Settings -->
            <div class="col-lg-4">
                <!-- Vehicle Settings -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $vehicle->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $vehicle->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ old('status', $vehicle->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Assign Driver</label>
                            <select name="driver_id" class="form-select">
                                <option value="">-- No Driver Assigned --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id', $vehicle->driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified', $vehicle->is_verified) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_verified">Verified</label>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Vehicle Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Front Image</label>
                            <input type="file" name="front_image" class="form-control" accept="image/*">
                            @if($vehicle->front_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$vehicle->front_image) }}" alt="Front" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Back Image</label>
                            <input type="file" name="back_image" class="form-control" accept="image/*">
                            @if($vehicle->back_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$vehicle->back_image) }}" alt="Back" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Side Image</label>
                            <input type="file" name="side_image" class="form-control" accept="image/*">
                            @if($vehicle->side_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/'.$vehicle->side_image) }}" alt="Side" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Vehicle</button>
            </div>
        </div>
    </form>
@endsection
