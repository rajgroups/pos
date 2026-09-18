@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Driver</h4>
                <h6 class="text-muted">Update driver profile</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Drivers
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <form action="{{ route('admin.drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
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
                        <input type="text" name="name" class="form-control" value="{{ old('name', $driver->name) }}" required>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $driver->phone) }}" required>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $driver->email) }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob', $driver->dob ? $driver->dob->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $driver->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $driver->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $driver->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Driver Type</label>
                        <select name="driver_type" class="form-select">
                            <option value="">Select Type</option>
                            @foreach(['car','bike','auto','borewell','tractor','harvester','lorry','mini_van','bus','other'] as $type)
                                <option value="{{ $type }}" {{ old('driver_type', $driver->driver_type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Address Information</h5>
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $driver->address) }}</textarea>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $driver->city) }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $driver->state) }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Pincode</label>
                        <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $driver->pincode) }}">
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Identification & License Details</h5>
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">Aadhaar Number</label>
                        <input type="text" name="aadhaar_number" class="form-control" value="{{ old('aadhaar_number', $driver->aadhaar_number) }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">PAN Number</label>
                        <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number', $driver->pan_number) }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">License Number</label>
                        <input type="text" name="license_number" class="form-control" value="{{ old('license_number', $driver->license_number) }}">
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <label class="form-label fw-semibold">License Expiry</label>
                        <input type="date" name="license_expiry" class="form-control" value="{{ old('license_expiry', $driver->license_expiry ? $driver->license_expiry->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-lg-8 col-md-12 mb-4">
                        <label class="form-label fw-semibold">License Categories</label>
                        <div class="d-flex flex-wrap gap-3">
                            @foreach(['LMV', 'HMV', 'TR', 'Bike', 'Tractor'] as $cat)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="license_categories[]" value="{{ $cat }}" id="cat_{{ $cat }}" {{ in_array($cat, old('license_categories', is_array($driver->license_categories) ? $driver->license_categories : [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cat_{{ $cat }}">{{ $cat }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Vehicle Assignment & Status</h5>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">Assign Vehicle</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">-- No Vehicle Assigned --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $driver->vehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $driver->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $driver->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blocked" {{ old('status', $driver->status) == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>
                    <div class="col-lg-12 mb-4">
                        <label class="form-label fw-semibold">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $driver->remarks) }}</textarea>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-4">Documents Upload</h5>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        @if($driver->profile_photo)
                            <a href="{{ asset('storage/' . $driver->profile_photo) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">License Front</label>
                        <input type="file" name="license_front" class="form-control" accept="image/*">
                        @if($driver->license_front)
                            <a href="{{ asset('storage/' . $driver->license_front) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">License Back</label>
                        <input type="file" name="license_back" class="form-control" accept="image/*">
                        @if($driver->license_back)
                            <a href="{{ asset('storage/' . $driver->license_back) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Aadhaar Front</label>
                        <input type="file" name="aadhaar_front" class="form-control" accept="image/*">
                        @if($driver->aadhaar_front)
                            <a href="{{ asset('storage/' . $driver->aadhaar_front) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Aadhaar Back</label>
                        <input type="file" name="aadhaar_back" class="form-control" accept="image/*">
                        @if($driver->aadhaar_back)
                            <a href="{{ asset('storage/' . $driver->aadhaar_back) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">PAN Card</label>
                        <input type="file" name="pan_card_file" class="form-control" accept="image/*">
                        @if($driver->pan_card_file)
                            <a href="{{ asset('storage/' . $driver->pan_card_file) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Police Verification</label>
                        <input type="file" name="police_verification_file" class="form-control">
                        @if($driver->police_verification_file)
                            <a href="{{ asset('storage/' . $driver->police_verification_file) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <label class="form-label fw-semibold">Medical Certificate</label>
                        <input type="file" name="medical_certificate" class="form-control">
                        @if($driver->medical_certificate)
                            <a href="{{ asset('storage/' . $driver->medical_certificate) }}" target="_blank" class="text-primary mt-1 d-block"><i class="ti ti-eye"></i> View Current</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Driver</button>
            </div>
        </form>
    </div>
@endsection
