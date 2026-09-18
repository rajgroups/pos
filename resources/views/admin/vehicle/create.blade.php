@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create Vehicle</h4>
                <h6 class="text-muted">Add a new vehicle to the fleet</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-primary shadow-sm">
                <i class="ti ti-arrow-left me-1"></i>Back to Vehicles
            </a>
        </div>
    </div>
    
    <div class="profile-banner position-relative rounded-3 mb-4 shadow-sm" style="height: 100px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        <div class="position-absolute bottom-0 start-0 p-4 text-white">
            <h5 class="mb-0 fw-bold d-flex align-items-center"><i class="ti ti-car fs-24 me-2"></i> New Vehicle Registration</h5>
        </div>
    </div>

    <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                                <label class="form-label fw-semibold"><i class="ti ti-id me-1 text-muted"></i> Registration Number <span class="text-danger">*</span></label>
                                <input type="text" name="vehicle_number" class="form-control text-uppercase" value="{{ old('vehicle_number') }}" placeholder="e.g. TN01AB1234" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-category me-1 text-muted"></i> Vehicle Category <span class="text-danger">*</span></label>
                                <select name="vehicle_category_id" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        @if($category->subCategories->isNotEmpty())
                                            <optgroup label="{{ $category->name }}">
                                                @foreach($category->subCategories as $sub)
                                                    <option value="{{ $sub->id }}" {{ old('vehicle_category_id') == $sub->id ? 'selected' : '' }}>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            <option value="{{ $category->id }}" {{ old('vehicle_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-tag me-1 text-muted"></i> Brand <span class="text-danger">*</span></label>
                                <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="e.g. Toyota" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-car-turbine me-1 text-muted"></i> Model <span class="text-danger">*</span></label>
                                <input type="text" name="model" class="form-control" value="{{ old('model') }}" placeholder="e.g. Innova" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-color-swatch me-1 text-muted"></i> Color <span class="text-danger">*</span></label>
                                <input type="text" name="color" class="form-control" value="{{ old('color') }}" placeholder="e.g. White" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-calendar me-1 text-muted"></i> Manufacture Year <span class="text-danger">*</span></label>
                                <input type="number" name="manufacture_year" class="form-control" value="{{ old('manufacture_year') }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="e.g. 2023" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-users me-1 text-muted"></i> Seating Capacity <span class="text-danger">*</span></label>
                                <input type="number" name="seating_capacity" class="form-control" value="{{ old('seating_capacity', 4) }}" min="1" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-weight me-1 text-muted"></i> Load Capacity (Tons)</label>
                                <input type="number" step="0.01" name="load_capacity" class="form-control" value="{{ old('load_capacity') }}" placeholder="For commercial/goods vehicles">
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
                                <label class="form-label fw-semibold"><i class="ti ti-file-certificate me-1 text-muted"></i> RC Number</label>
                                <input type="text" name="rc_number" class="form-control text-uppercase" value="{{ old('rc_number') }}" placeholder="Registration Certificate No.">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-calendar-due me-1 text-muted"></i> RC Expiry</label>
                                <input type="date" name="rc_expiry" class="form-control" value="{{ old('rc_expiry') }}">
                            </div>
                            
                            <!-- Insurance -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-shield-check me-1 text-muted"></i> Insurance Number</label>
                                <input type="text" name="insurance_number" class="form-control text-uppercase" value="{{ old('insurance_number') }}" placeholder="Insurance Policy No.">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-calendar-due me-1 text-muted"></i> Insurance Expiry</label>
                                <input type="date" name="insurance_expiry" class="form-control" value="{{ old('insurance_expiry') }}">
                            </div>

                            <!-- Permit -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-file-text me-1 text-muted"></i> Permit Number</label>
                                <input type="text" name="permit_number" class="form-control text-uppercase" value="{{ old('permit_number') }}" placeholder="Vehicle Permit No.">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-calendar-due me-1 text-muted"></i> Permit Expiry</label>
                                <input type="date" name="permit_expiry" class="form-control" value="{{ old('permit_expiry') }}">
                            </div>

                            <!-- Fitness -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-activity me-1 text-muted"></i> Fitness Certificate No.</label>
                                <input type="text" name="fitness_certificate_number" class="form-control text-uppercase" value="{{ old('fitness_certificate_number') }}" placeholder="Fitness Cert. No.">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-calendar-due me-1 text-muted"></i> Fitness Expiry</label>
                                <input type="date" name="fitness_expiry" class="form-control" value="{{ old('fitness_expiry') }}">
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
                            <label class="form-label fw-semibold"><i class="ti ti-toggle-right me-1 text-muted"></i> Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold"><i class="ti ti-steering-wheel me-1 text-muted"></i> Assign Driver</label>
                            <select name="driver_id" class="form-select">
                                <option value="">-- No Driver Assigned --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-switch form-switch-md mb-0">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold cursor-pointer" for="is_verified">Officially Verified</label>
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
                            <label class="form-label fw-semibold"><i class="ti ti-photo me-1 text-muted"></i> Front Image</label>
                            <input type="file" name="front_image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold"><i class="ti ti-photo me-1 text-muted"></i> Back Image</label>
                            <input type="file" name="back_image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold"><i class="ti ti-photo me-1 text-muted"></i> Side Image</label>
                            <input type="file" name="side_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Vehicle</button>
            </div>
        </div>
    </form>
@endsection
