@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create Driver</h4>
                <h6 class="text-muted">Add a new driver profile</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Drivers
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3">
                        {{ session()->get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <h5 class="mb-4">Basic Information</h5>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Driver Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Driver Type</label>
                        <select name="driver_type" class="form-select">
                            <option value="">Select Type</option>
                            @foreach(['car','bike','auto','borewell','tractor','harvester','lorry','mini_van','bus','other'] as $type)
                                <option value="{{ $type }}" {{ old('driver_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Address Information</h5>
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="{{ old('pincode') }}">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Identification & License Details</h5>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Aadhaar Number</label>
                        <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">PAN Number</label>
                        <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">License Number</label>
                        <input type="text" name="license_number" class="form-control" value="{{ old('license_number') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">License Expiry</label>
                        <input type="date" name="license_expiry" class="form-control" value="{{ old('license_expiry') }}">
                    </div>
                    <div class="col-lg-8 col-md-12 mb-4">
                        <label class="form-label fw-semibold">License Categories</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['LMV', 'HMV', 'TR', 'Bike', 'Tractor'] as $cat)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="license_categories[]" value="{{ $cat }}" id="cat_{{ $cat }}" {{ in_array($cat, old('license_categories', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cat_{{ $cat }}">{{ $cat }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="mb-0">Vehicle Registration</h5>
                    <div class="form-check form-switch form-switch-md">
                        <input class="form-check-input" type="checkbox" role="switch" id="registerVehicleToggle" name="register_vehicle" value="1" {{ old('register_vehicle') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="registerVehicleToggle">Register New Vehicle</label>
                    </div>
                </div>

                <div id="vehicleFormSection" class="p-4 bg-light rounded-3 mb-4 border" style="display: {{ old('register_vehicle') ? 'block' : 'none' }};">
                    <p class="text-muted small mb-4"><i class="ti ti-info-circle me-1"></i> Fill these details to automatically register and assign a vehicle to this driver.</p>
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Vehicle Category <span class="text-danger">*</span></label>
                            <select name="vehicle_category_id" class="form-select">
                                <option value="">Select Category</option>
                                @foreach($vehicleCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('vehicle_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">e.g. Sedan, Auto, Bike</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Vehicle Number <span class="text-danger">*</span></label>
                            <input type="text" name="vehicle_number" class="form-control text-uppercase" value="{{ old('vehicle_number') }}" placeholder="e.g. MH01AB1234">
                            <div class="form-text">Number plate without spaces</div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Brand / Make</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" placeholder="e.g. Maruti Suzuki">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Model</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model') }}" placeholder="e.g. Swift Dzire">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Color</label>
                            <input type="text" name="color" class="form-control" value="{{ old('color') }}" placeholder="e.g. White">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Manufacturing Year</label>
                            <input type="number" name="manufacture_year" class="form-control" value="{{ old('manufacture_year') }}" placeholder="YYYY" min="1990" max="{{ date('Y') + 1 }}">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">RC Number</label>
                            <input type="text" name="rc_number" class="form-control text-uppercase" value="{{ old('rc_number') }}">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Seating Capacity</label>
                            <input type="number" name="seating_capacity" class="form-control" value="{{ old('seating_capacity') }}" min="1">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">Front Image</label>
                            <input type="file" name="front_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Assign Existing Vehicle <small class="text-muted fw-normal">(If not registering new)</small></label>
                        <select name="vehicle_id" class="form-select" id="existingVehicleSelect">
                            <option value="">-- Select Existing Vehicle --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Driver Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3" placeholder="Any internal notes about this driver...">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Documents Upload</h5>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">License Front</label>
                        <input type="file" name="license_front" class="form-control" accept="image/*">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">License Back</label>
                        <input type="file" name="license_back" class="form-control" accept="image/*">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Aadhaar Front</label>
                        <input type="file" name="aadhaar_front" class="form-control" accept="image/*">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Aadhaar Back</label>
                        <input type="file" name="aadhaar_back" class="form-control" accept="image/*">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">PAN Card</label>
                        <input type="file" name="pan_card_file" class="form-control" accept="image/*">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Police Verification</label>
                        <input type="file" name="police_verification_file" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Medical Certificate</label>
                        <input type="file" name="medical_certificate" class="form-control">
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Driver</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('registerVehicleToggle');
        const section = document.getElementById('vehicleFormSection');
        const existingSelect = document.getElementById('existingVehicleSelect');

        toggle.addEventListener('change', function() {
            if (this.checked) {
                section.style.display = 'block';
                existingSelect.disabled = true;
                existingSelect.value = ''; // clear existing vehicle selection
            } else {
                section.style.display = 'none';
                existingSelect.disabled = false;
            }
        });

        // Initialize on load
        if (toggle.checked) {
            existingSelect.disabled = true;
        }
    });
</script>
@endpush
