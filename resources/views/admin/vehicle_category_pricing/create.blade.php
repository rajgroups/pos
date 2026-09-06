@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create Pricing Rule</h4>
                <h6 class="text-muted">Define pricing for a vehicle category</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.vehicle-pricing.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Pricing Rules
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <form action="{{ route('admin.vehicle-pricing.store') }}" method="POST">
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

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Vehicle Category <span class="text-danger">*</span></label>
                            <select name="vehicle_category_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('vehicle_category_id') == $category->id ? 'selected' : '' }} class="fw-bold">
                                        {{ $category->name }}
                                    </option>
                                    @foreach($category->subCategories as $child)
                                        <option value="{{ $child->id }}" {{ old('vehicle_category_id') == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp;&nbsp;-- {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Base Fare <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="base_fare" class="form-control" value="{{ old('base_fare', 0) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per KM Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_km_rate" class="form-control" value="{{ old('per_km_rate', 0) }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Day Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_day_rate" class="form-control" value="{{ old('per_day_rate', 0) }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Ton Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_ton_rate" class="form-control" value="{{ old('per_ton_rate', 0) }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Night Charge Percentage <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="night_charge_percentage" class="form-control" value="{{ old('night_charge_percentage', 0) }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pricing Type <span class="text-danger">*</span></label>
                            <select name="pricing_type" class="form-select" required>
                                <option value="standard" {{ old('pricing_type') == 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="premium" {{ old('pricing_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Minimum Fare <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="minimum_fare" class="form-control" value="{{ old('minimum_fare', 0) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Hour Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_hour_rate" class="form-control" value="{{ old('per_hour_rate', 0) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Acre Rate <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_acre_rate" class="form-control" value="{{ old('per_acre_rate', 0) }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Waiting Charge Per Hour <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="waiting_charge_per_hour" class="form-control" value="{{ old('waiting_charge_per_hour', 0) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Surge Multiplier <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="surge_multiplier" class="form-control" value="{{ old('surge_multiplier', 1.0) }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status (Active)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.vehicle-pricing.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Pricing Rule</button>
            </div>
        </form>
    </div>
@endsection
