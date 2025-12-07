@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Warehouse - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold"><i class="ti ti-building-warehouse text-primary me-2"></i>Edit Warehouse</h4>
            <p class="text-muted mb-0">Update warehouse details</p>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Warehouse List
            </a>
        </div>
    </div>

    <!-- Warehouse Form -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
            <h4 class="fw-bold mb-0 d-flex align-items-center">
                <i class="ti ti-edit me-2"></i> Update Warehouse Information
            </h4>
        </div>

        <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body p-4">

                {{-- Validation Errors --}}
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

                {{-- Success --}}
                @if(session('success'))
                    <div class="alert alert-success rounded-2 mb-4 d-flex align-items-center">
                        <i class="ti ti-circle-check me-2 fs-5"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="row g-4">

                    <!-- Warehouse Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Warehouse Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-building text-primary"></i></span>
                            <input type="text" name="warehouse_name"
                                   class="form-control @error('warehouse_name') is-invalid @enderror"
                                   value="{{ old('warehouse_name', $warehouse->warehouse_name) }}" required>
                        </div>
                        @error('warehouse_name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Warehouse Code -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Warehouse Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-barcode text-primary"></i></span>
                            <input type="text" name="warehouse_code"
                                   class="form-control @error('warehouse_code') is-invalid @enderror"
                                   value="{{ old('warehouse_code', $warehouse->warehouse_code) }}" required>
                        </div>
                        @error('warehouse_code')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Warehouse Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Warehouse Type <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-category text-primary"></i></span>
                            <select name="warehouse_type" class="form-select @error('warehouse_type') is-invalid @enderror">
                                <option value="">Select</option>
                                @foreach(['Owned','Rented','Franchise','Partner','Distribution Hub'] as $type)
                                    <option value="{{ $type }}"
                                        {{ old('warehouse_type', $warehouse->warehouse_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('warehouse_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contact Person -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Person <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-user text-primary"></i></span>
                            <input type="text" name="contact_person"
                                   class="form-control @error('contact_person') is-invalid @enderror"
                                   value="{{ old('contact_person', $warehouse->contact_person) }}" required>
                        </div>
                        @error('contact_person')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-mail text-primary"></i></span>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $warehouse->email) }}" required>
                        </div>
                        @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alternate Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Alternate Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-mail-opened text-primary"></i></span>
                            <input type="email" name="email_alt"
                                   class="form-control @error('email_alt') is-invalid @enderror"
                                   value="{{ old('email_alt', $warehouse->email_alt) }}">
                        </div>
                        @error('email_alt')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-phone text-primary"></i></span>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone', $warehouse->phone) }}" required>
                        </div>
                        @error('phone')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Work -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone (Work)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-phone-calling text-primary"></i></span>
                            <input type="text" name="phone_work"
                                   class="form-control @error('phone_work') is-invalid @enderror"
                                   value="{{ old('phone_work', $warehouse->phone_work) }}">
                        </div>
                        @error('phone_work')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Website / Map Link -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Website / Google Map Link</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-link text-primary"></i></span>
                            <input type="url" name="map_link"
                                   class="form-control @error('map_link') is-invalid @enderror"
                                   value="{{ old('map_link', $warehouse->map_link) }}">
                        </div>
                        @error('map_link')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-map-pin text-primary"></i></span>
                            <input type="text" name="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $warehouse->address) }}" required>
                        </div>
                        @error('address')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- City -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-building-community text-primary"></i></span>
                            <input type="text" name="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   value="{{ old('city', $warehouse->city) }}" required>
                        </div>
                        @error('city')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- State -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">State <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-map text-primary"></i></span>
                            <input type="text" name="state"
                                   class="form-control @error('state') is-invalid @enderror"
                                   value="{{ old('state', $warehouse->state) }}" required>
                        </div>
                        @error('state')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-flag text-primary"></i></span>
                            <input type="text" name="country"
                                   class="form-control @error('country') is-invalid @enderror"
                                   value="{{ old('country', $warehouse->country) }}" required>
                        </div>
                        @error('country')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Postal Code <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-mailbox text-primary"></i></span>
                            <input type="text" name="postal_code"
                                   class="form-control @error('postal_code') is-invalid @enderror"
                                   value="{{ old('postal_code', $warehouse->postal_code) }}" required>
                        </div>
                        @error('postal_code')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Latitude -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Latitude</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-map text-primary"></i></span>
                            <input type="text" name="latitude"
                                   class="form-control @error('latitude') is-invalid @enderror"
                                   value="{{ old('latitude', $warehouse->latitude) }}">
                        </div>
                        @error('latitude')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Longitude</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-map text-primary"></i></span>
                            <input type="text" name="longitude"
                                   class="form-control @error('longitude') is-invalid @enderror"
                                   value="{{ old('longitude', $warehouse->longitude) }}">
                        </div>
                        @error('longitude')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Storage Capacity -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Storage Capacity (Sq Ft)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-dimensions text-primary"></i></span>
                            <input type="number" name="capacity"
                                   class="form-control @error('capacity') is-invalid @enderror"
                                   value="{{ old('capacity', $warehouse->capacity) }}">
                        </div>
                        @error('capacity')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Opening Hours -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Opening Hours</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-clock text-primary"></i></span>
                            <input type="text" name="opening_hours"
                                   class="form-control @error('opening_hours') is-invalid @enderror"
                                   value="{{ old('opening_hours', $warehouse->opening_hours) }}">
                        </div>
                        @error('opening_hours')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-notes text-primary"></i></span>
                            <textarea name="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3">{{ old('notes', $warehouse->notes) }}</textarea>
                        </div>
                        @error('notes')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1">
                                        <i class="ti ti-toggle-right text-primary me-2"></i> Warehouse Status
                                    </h6>
                                    <p class="text-muted small mb-0">Enable or disable this warehouse</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <label class="form-check">
                                        <input type="radio" name="status" value="1"
                                               class="form-check-input"
                                               {{ old('status', $warehouse->status) == 1 ? 'checked' : '' }}>
                                        <span class="badge bg-success">Active</span>
                                    </label>

                                    <label class="form-check">
                                        <input type="radio" name="status" value="0"
                                               class="form-check-input"
                                               {{ old('status', $warehouse->status) == 0 ? 'checked' : '' }}>
                                        <span class="badge bg-danger">Inactive</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- row end -->
            </div>

            <!-- Footer Buttons -->
            <div class="card-footer bg-light py-3 rounded-bottom-3 d-flex justify-content-between">
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="ti ti-x me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ti ti-device-floppy me-2"></i> Update Warehouse
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Optional: Prevent auto-regenerating code when editing
        const nameInput = document.querySelector('input[name="warehouse_name"]');
        const codeInput = document.querySelector('input[name="warehouse_code"]');

        nameInput?.addEventListener('blur', function () {
            if (!codeInput.value.trim()) {
                const name = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase();
                const prefix = name.substring(0, 2) || 'WH';
                const rand = Math.floor(100 + Math.random() * 900);
                codeInput.value = prefix + '-' + rand;
            }
        });

    });
</script>
@endpush
