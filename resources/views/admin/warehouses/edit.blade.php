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
    <div class="page-header d-flex justify-content-between align-items-center">
        <div class="page-title">
            <h4 class="fw-bold">Edit Warehouse</h4>
            <h6>Update warehouse details</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Warehouse List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">

        <div class="card-header">
            <h4 class="fw-bold mb-0">Edit Warehouse</h4>
        </div>

        <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row">

                    <!-- Warehouse Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                        <input type="text" name="warehouse_name" class="form-control" value="{{ old('warehouse_name', $warehouse->warehouse_name) }}" required>
                    </div>

                    <!-- Contact Person -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person <span class="text-danger">*</span></label>
                        <select name="contact_person" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Steven" {{ old('contact_person', $warehouse->contact_person) == 'Steven' ? 'selected' : '' }}>Steven</option>
                            <option value="Gravely" {{ old('contact_person', $warehouse->contact_person) == 'Gravely' ? 'selected' : '' }}>Gravely</option>
                        </select>
                    </div>

                    <!-- Email -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $warehouse->email) }}" required>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $warehouse->phone) }}" required>
                    </div>

                    <!-- Phone Work -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone (Work)</label>
                        <input type="text" name="phone_work" class="form-control" value="{{ old('phone_work', $warehouse->phone_work) }}">
                    </div>

                    <!-- Address -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $warehouse->address) }}" required>
                    </div>

                    <!-- City -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <select name="city" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Varrel" {{ old('city', $warehouse->city) == 'Varrel' ? 'selected' : '' }}>Varrel</option>
                            <option value="Los Angeles" {{ old('city', $warehouse->city) == 'Los Angeles' ? 'selected' : '' }}>Los Angeles</option>
                            <option value="Munich" {{ old('city', $warehouse->city) == 'Munich' ? 'selected' : '' }}>Munich</option>
                        </select>
                    </div>

                    <!-- State -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <select name="state" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Bavaria" {{ old('state', $warehouse->state) == 'Bavaria' ? 'selected' : '' }}>Bavaria</option>
                            <option value="New York City" {{ old('state', $warehouse->state) == 'New York City' ? 'selected' : '' }}>New York City</option>
                            <option value="California" {{ old('state', $warehouse->state) == 'California' ? 'selected' : '' }}>California</option>
                        </select>
                    </div>

                    <!-- Country -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <select name="country" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Germany" {{ old('country', $warehouse->country) == 'Germany' ? 'selected' : '' }}>Germany</option>
                            <option value="Mexico" {{ old('country', $warehouse->country) == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                            <option value="United States" {{ old('country', $warehouse->country) == 'United States' ? 'selected' : '' }}>United States</option>
                        </select>
                    </div>

                    <!-- Postal Code -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $warehouse->postal_code) }}" required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-12 mb-3">
                        <div class="status-toggle d-flex justify-content-between align-items-center">
                            <span class="status-label">Status</span>
                            <input type="checkbox" id="warehouse_status" name="status" class="check" {{ $warehouse->status ? 'checked' : '' }}>
                            <label for="warehouse_status" class="checktoggle mb-0"></label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Warehouse</button>
            </div>

        </form>

    </div>

</div>
@endsection
