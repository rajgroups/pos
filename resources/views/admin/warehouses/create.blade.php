@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Warehouse - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold"><i class="ti ti-building-warehouse text-primary me-2"></i>Add Warehouse</h4>
            <p class="text-muted mb-0">Create and manage warehouse storage locations</p>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-2"></i> Warehouse List
            </a>
        </div>
    </div>

    <!-- Warehouse Form -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
            <h4 class="fw-bold mb-0 d-flex align-items-center">
                <i class="ti ti-building-warehouse me-2"></i> Warehouse Information
            </h4>
        </div>

        <form action="{{ route('admin.warehouses.store') }}" method="POST">
            @csrf

            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-2 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-circle me-2 fs-5"></i>
                            <strong>Please fix the following errors:</strong>
                        </div>
                        <ul class="mt-2 mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success rounded-2 mb-4 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2 fs-5"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row g-4">

                    <!-- Warehouse Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warehouse Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-building text-primary"></i>
                            </span>
                            <input type="text" name="warehouse_name"
                                   class="form-control @error('warehouse_name') is-invalid @enderror"
                                   placeholder="e.g., Main Storage Hub"
                                   value="{{ old('warehouse_name') }}" required>
                        </div>
                        @error('warehouse_name')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Enter a descriptive warehouse name
                            </div>
                        @enderror
                    </div>

                    <!-- Warehouse Code -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warehouse Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-barcode text-primary"></i>
                            </span>
                            <input type="text" name="warehouse_code"
                                   class="form-control @error('warehouse_code') is-invalid @enderror"
                                   placeholder="e.g., WH-001"
                                   value="{{ old('warehouse_code') }}" required>
                        </div>
                        @error('warehouse_code')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Unique code for warehouse identification
                            </div>
                        @enderror
                    </div>

                    <!-- Warehouse Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warehouse Type <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-category text-primary"></i>
                            </span>
                            <select name="warehouse_type" class="form-select @error('warehouse_type') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Warehouse Type</option>
                                <option value="Owned" {{ old('warehouse_type') == 'Owned' ? 'selected' : '' }}>Owned</option>
                                <option value="Rented" {{ old('warehouse_type') == 'Rented' ? 'selected' : '' }}>Rented</option>
                                <option value="Franchise" {{ old('warehouse_type') == 'Franchise' ? 'selected' : '' }}>Franchise</option>
                                <option value="Partner" {{ old('warehouse_type') == 'Partner' ? 'selected' : '' }}>Partner</option>
                                <option value="Distribution Hub" {{ old('warehouse_type') == 'Distribution Hub' ? 'selected' : '' }}>Distribution Hub</option>
                            </select>
                        </div>
                        @error('warehouse_type')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Select the warehouse ownership type
                            </div>
                        @enderror
                    </div>

                    <!-- Contact Person -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Contact Person <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-user text-primary"></i>
                            </span>
                            <input type="text" name="contact_person"
                                   class="form-control @error('contact_person') is-invalid @enderror"
                                   placeholder="e.g., John Doe"
                                   value="{{ old('contact_person') }}" required>
                        </div>
                        @error('contact_person')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Primary contact person name
                            </div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-mail text-primary"></i>
                            </span>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="e.g., contact@warehouse.com"
                                   value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Primary email address
                            </div>
                        @enderror
                    </div>

                    <!-- Alternate Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Alternate Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-mail-opened text-primary"></i>
                            </span>
                            <input type="email" name="email_alt"
                                   class="form-control @error('email_alt') is-invalid @enderror"
                                   placeholder="e.g., support@warehouse.com"
                                   value="{{ old('email_alt') }}">
                        </div>
                        @error('email_alt')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Backup email address (optional)
                            </div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Phone <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-phone text-primary"></i>
                            </span>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="e.g., +1 245 454 657"
                                   value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Primary contact number
                            </div>
                        @enderror
                    </div>

                    <!-- Phone Work -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Phone (Work)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-phone-calling text-primary"></i>
                            </span>
                            <input type="text" name="phone_work"
                                   class="form-control @error('phone_work') is-invalid @enderror"
                                   placeholder="e.g., +1 457 456 8755"
                                   value="{{ old('phone_work') }}">
                        </div>
                        @error('phone_work')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Office/work number (optional)
                            </div>
                        @enderror
                    </div>

                    <!-- Website or Map Link -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-dark">Website / Google Map Link</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-link text-primary"></i>
                            </span>
                            <input type="url" name="map_link"
                                   class="form-control @error('map_link') is-invalid @enderror"
                                   placeholder="https://maps.google.com/..."
                                   value="{{ old('map_link') }}">
                        </div>
                        @error('map_link')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Google Maps link or website URL
                            </div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold text-dark">Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-map-pin text-primary"></i>
                            </span>
                            <input type="text" name="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   placeholder="e.g., 123 Warehouse Street, Industrial Area"
                                   value="{{ old('address') }}" required>
                        </div>
                        @error('address')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Complete street address
                            </div>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">City <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-building-community text-primary"></i>
                            </span>
                            <input type="text" name="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   placeholder="e.g., Los Angeles"
                                   value="{{ old('city') }}" required>
                        </div>
                        @error('city')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- State -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">State <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-map text-primary"></i>
                            </span>
                            <input type="text" name="state"
                                   class="form-control @error('state') is-invalid @enderror"
                                   placeholder="e.g., California"
                                   value="{{ old('state') }}" required>
                        </div>
                        @error('state')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Country <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-flag text-primary"></i>
                            </span>
                            <input type="text" name="country"
                                   class="form-control @error('country') is-invalid @enderror"
                                   placeholder="e.g., United States"
                                   value="{{ old('country') }}" required>
                        </div>
                        @error('country')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Postal Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-mailbox text-primary"></i>
                            </span>
                            <input type="text" name="postal_code"
                                   class="form-control @error('postal_code') is-invalid @enderror"
                                   placeholder="e.g., 90001"
                                   value="{{ old('postal_code') }}" required>
                        </div>
                        @error('postal_code')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> ZIP or postal code
                            </div>
                        @enderror
                    </div>

                    <!-- Latitude -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Latitude</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-map-2 text-primary"></i>
                            </span>
                            <input type="text" name="latitude"
                                   class="form-control @error('latitude') is-invalid @enderror"
                                   placeholder="e.g., 34.0522"
                                   value="{{ old('latitude') }}">
                        </div>
                        @error('latitude')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Geographic coordinates (optional)
                            </div>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Longitude</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-map-2 text-primary"></i>
                            </span>
                            <input type="text" name="longitude"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   placeholder="e.g., -118.2437"
                                   value="{{ old('longitude') }}">
                        </div>
                        @error('longitude')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Storage Capacity -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Storage Capacity (Sq Ft)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-dimensions text-primary"></i>
                            </span>
                            <input type="number" name="capacity"
                                   class="form-control @error('capacity') is-invalid @enderror"
                                   placeholder="e.g., 5000"
                                   value="{{ old('capacity') }}" min="0">
                        </div>
                        @error('capacity')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Total storage area in square feet
                            </div>
                        @enderror
                    </div>

                    <!-- Opening Hours -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Opening Hours</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-clock text-primary"></i>
                            </span>
                            <input type="text" name="opening_hours"
                                   class="form-control @error('opening_hours') is-invalid @enderror"
                                   placeholder="e.g., 9:00 AM - 6:00 PM, Mon-Fri"
                                   value="{{ old('opening_hours') }}">
                        </div>
                        @error('opening_hours')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Business hours schedule
                            </div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Notes</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light align-items-start">
                                <i class="ti ti-notes text-primary mt-1"></i>
                            </span>
                            <textarea name="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Internal instructions, special requirements, or warehouse notes...">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Additional information about the warehouse
                            </div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1 d-flex align-items-center">
                                        <i class="ti ti-toggle-right text-primary me-2"></i> Warehouse Status
                                    </h6>
                                    <p class="text-muted small mb-0">Enable or disable this warehouse</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                               id="status_active" value="1"
                                               {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_active">
                                            <span class="badge bg-success">Active</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status"
                                               id="status_inactive" value="0"
                                               {{ old('status') == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status_inactive">
                                            <span class="badge bg-danger">Inactive</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="card-footer bg-light py-3 rounded-bottom-3 d-flex justify-content-between">
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="ti ti-x me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ti ti-plus me-2"></i> Create Warehouse
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate warehouse code on name input
        const nameInput = document.querySelector('input[name="warehouse_name"]');
        const codeInput = document.querySelector('input[name="warehouse_code"]');

        if (nameInput && codeInput && !codeInput.value) {
            nameInput.addEventListener('blur', function() {
                if (!codeInput.value && this.value) {
                    // Generate code from name: first 2 chars + random numbers
                    const name = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                    const prefix = name.length >= 2 ? name.substring(0, 2) : 'WH';
                    const randomNum = Math.floor(100 + Math.random() * 900);
                    codeInput.value = prefix + '-' + randomNum;
                }
            });
        }

        // Address autocomplete integration (requires Google Maps API)
        const addressInput = document.querySelector('input[name="address"]');
        if (addressInput && typeof google !== 'undefined') {
            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['address']
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    document.querySelector('input[name="latitude"]').value = place.geometry.location.lat();
                    document.querySelector('input[name="longitude"]').value = place.geometry.location.lng();
                }
            });
        }
    });
</script>
@endpush
