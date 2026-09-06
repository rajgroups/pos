@extends('layouts.admin.app')
@section('title', 'Driver Profile')

@section('content')
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="profile-banner position-relative" style="height: 140px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-top-left-radius: 8px; border-top-right-radius: 8px;">
        </div>
        <div class="d-flex align-items-end px-4 pb-4 position-relative" style="margin-top: -60px;">
            <div class="avatar avatar-xxl me-4 border border-4 border-white shadow-sm rounded-circle bg-white" style="width: 120px; height: 120px;">
                <img src="{{ $driver->profile_photo ? asset('storage/'.$driver->profile_photo) : asset('resource/admin/assets/img/profiles/avatar-02.jpg') }}" alt="Driver" class="rounded-circle w-100 h-100 object-fit-cover">
            </div>
            <div class="pb-2">
                <div class="d-flex align-items-center mb-1">
                    <h3 class="mb-0 text-dark fw-bold me-2">{{ $driver->name }}</h3>
                    @if($driver->is_verified)
                        <i class="ti ti-discount-check-filled text-primary fs-20" title="Verified"></i>
                    @endif
                </div>
                <p class="mb-0 text-muted fs-15">
                    Driver ID: #{{ $driver->id }} &bull; 
                    <span class="badge bg-light text-dark border me-1">{{ ucfirst(str_replace('_', ' ', $driver->driver_type)) }}</span>
                    @if($driver->is_online)
                        <span class="badge bg-success-transparent text-success border border-success-transparent"><i class="ti ti-point-filled"></i> Online</span>
                    @else
                        <span class="badge bg-danger-transparent text-danger border border-danger-transparent"><i class="ti ti-point-filled"></i> Offline</span>
                    @endif
                </p>
            </div>
            <div class="ms-auto pb-2">
                <a href="{{ route('admin.drivers.edit', $driver->id) }}" class="btn btn-primary"><i class="ti ti-edit me-2"></i>Edit Profile</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Driver Details -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Personal Details</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-phone me-2"></i>Phone</span>
                        <span class="fw-medium text-dark">{{ $driver->phone }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-mail me-2"></i>Email</span>
                        <span class="fw-medium text-dark">{{ $driver->email ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-calendar me-2"></i>Date of Birth</span>
                        <span class="fw-medium text-dark">{{ $driver->dob ? $driver->dob->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-user me-2"></i>Gender</span>
                        <span class="fw-medium text-dark">{{ $driver->gender ? ucfirst($driver->gender) : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-wallet me-2"></i>Wallet Balance</span>
                        <span class="fw-medium text-success">₹{{ $driver->wallet_balance ?? '0.00' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-map-pin me-2"></i>Address</span>
                        <span class="fw-medium text-dark text-end" style="max-width: 60%;">
                            {{ $driver->address ? $driver->address . ',' : '' }}
                            {{ $driver->city ? $driver->city . ',' : '' }}
                            {{ $driver->state ? $driver->state . ' -' : '' }}
                            {{ $driver->pincode ?? 'N/A' }}
                            @if(!$driver->address && !$driver->city && !$driver->state && !$driver->pincode) N/A @endif
                        </span>
                    </li>
                    <li class="d-flex justify-content-between mb-0">
                        <span class="text-muted"><i class="ti ti-shield-check me-2"></i>Account Status</span>
                        <span class="badge bg-{{ $driver->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($driver->status) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Identity & Compliance -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Identity & Compliance</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-id me-2"></i>Aadhaar No.</span>
                        <span class="fw-medium text-dark">{{ $driver->aadhaar_number ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-credit-card me-2"></i>PAN No.</span>
                        <span class="fw-medium text-dark">{{ $driver->pan_number ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-id-badge me-2"></i>License No.</span>
                        <span class="fw-medium text-dark">{{ $driver->license_number ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-calendar-time me-2"></i>License Expiry</span>
                        <span class="fw-medium {{ $driver->license_expiry && $driver->license_expiry->isPast() ? 'text-danger' : 'text-dark' }}">{{ $driver->license_expiry ? $driver->license_expiry->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-category me-2"></i>License Categories</span>
                        <span class="fw-medium text-dark text-end">
                            @if(is_array($driver->license_categories) && count($driver->license_categories) > 0)
                                {{ implode(', ', $driver->license_categories) }}
                            @else
                                {{ $driver->license_categories ?? 'N/A' }}
                            @endif
                        </span>
                    </li>
                    <li class="d-flex justify-content-between mb-0 flex-column">
                        <span class="text-muted mb-2"><i class="ti ti-message me-2"></i>Admin Remarks</span>
                        <div class="bg-light p-3 rounded text-dark fs-14">
                            {{ $driver->remarks ?? 'No remarks available.' }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Assigned Vehicle -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Assigned Vehicle</h5>
            </div>
            <div class="card-body">
                @if($driver->vehicle)
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-lg me-3 bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ti ti-car fs-24 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold"><a href="{{ route('admin.vehicles.show', $driver->vehicle->id) }}" class="text-dark">{{ $driver->vehicle->vehicle_number }}</a></h6>
                            <span class="badge bg-{{ $driver->vehicle->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($driver->vehicle->status) }}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 border-bottom pb-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="ti ti-tag me-2"></i>Brand & Model</span>
                            <span class="fw-medium">{{ $driver->vehicle->brand }} {{ $driver->vehicle->model }}</span>
                        </li>
                        <li class="mb-3 border-bottom pb-2 d-flex justify-content-between">
                            <span class="text-muted"><i class="ti ti-color-swatch me-2"></i>Color & Year</span>
                            <span class="fw-medium">{{ $driver->vehicle->color }} &bull; {{ $driver->vehicle->manufacture_year }}</span>
                        </li>
                        <li class="mb-0 text-end">
                            <a href="{{ route('admin.vehicles.show', $driver->vehicle->id) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-arrow-right me-1"></i> View Full Details</a>
                        </li>
                    </ul>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-car-off fs-48 mb-2 text-light"></i>
                        <p class="mb-0">No Vehicle Assigned</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Uploaded Documents</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($driver->documents as $doc)
                        <li class="list-group-item p-3 d-flex align-items-center">
                            <div class="avatar avatar-md me-3 flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded"><i class="ti ti-file-text fs-20"></i></span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $doc->documentType?->name ?? 'Document' }}</h6>
                                <p class="text-muted mb-0 fs-13">Exp: {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="ms-2">
                                <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($doc->status) }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item p-5 text-center text-muted border-0">
                            No documents uploaded.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
