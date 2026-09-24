@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create Pricing Rule</h4>
                <h6 class="text-muted">Define pricing, commission & tax for a vehicle category</h6>
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

                {{-- ── Category & Pricing Type ──────────────────────────────── --}}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Vehicle Category <span class="text-danger">*</span></label>
                            <select name="vehicle_category_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('vehicle_category_id') == $category->id ? 'selected' : '' }} class="fw-bold">
                                        {{ $category->name }}
                                        @if($category->driver_search_radius_km)
                                            (Radius: {{ $category->driver_search_radius_km }} km)
                                        @endif
                                    </option>
                                    @foreach($category->subCategories as $child)
                                        <option value="{{ $child->id }}" {{ old('vehicle_category_id') == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp;&nbsp;-- {{ $child->name }}
                                            @if($child->driver_search_radius_km)
                                                (Radius: {{ $child->driver_search_radius_km }} km)
                                            @endif
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pricing Type <span class="text-danger">*</span></label>
                            <select name="pricing_type" class="form-select" required>
                                <option value="distance" {{ old('pricing_type') == 'distance' ? 'selected' : '' }}>Distance (Per KM)</option>
                                <option value="hourly"   {{ old('pricing_type') == 'hourly'   ? 'selected' : '' }}>Hourly</option>
                                <option value="daily"    {{ old('pricing_type') == 'daily'    ? 'selected' : '' }}>Daily</option>
                                <option value="acre"     {{ old('pricing_type') == 'acre'     ? 'selected' : '' }}>Per Acre</option>
                                <option value="weight"   {{ old('pricing_type') == 'weight'   ? 'selected' : '' }}>Per Ton (Weight)</option>
                                <option value="fixed"    {{ old('pricing_type') == 'fixed'    ? 'selected' : '' }}>Fixed Fare</option>
                                <option value="standard" {{ old('pricing_type', 'standard') == 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="premium"  {{ old('pricing_type') == 'premium'  ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ── Fare fields ───────────────────────────────────────────── --}}
                <h6 class="fw-bold text-muted text-uppercase mb-3 border-bottom pb-2">Fare Configuration</h6>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Base Fare (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="base_fare" class="form-control" value="{{ old('base_fare', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Minimum Fare (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="minimum_fare" class="form-control" value="{{ old('minimum_fare', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per KM Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_km_rate" class="form-control" value="{{ old('per_km_rate', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Hour Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_hour_rate" class="form-control" value="{{ old('per_hour_rate', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Day Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_day_rate" class="form-control" value="{{ old('per_day_rate', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Acre Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_acre_rate" class="form-control" value="{{ old('per_acre_rate', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Per Ton Rate (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="per_ton_rate" class="form-control" value="{{ old('per_ton_rate', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Waiting Charge / Hour (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="waiting_charge_per_hour" class="form-control" value="{{ old('waiting_charge_per_hour', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Night Charge (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="night_charge_percentage" class="form-control" value="{{ old('night_charge_percentage', 0) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Surge Multiplier <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="1" name="surge_multiplier" class="form-control" value="{{ old('surge_multiplier', 1.0) }}" required>
                        </div>
                    </div>
                </div>

                {{-- ── Commission ────────────────────────────────────────────── --}}
                <h6 class="fw-bold text-muted text-uppercase mb-3 border-bottom pb-2">Admin Commission</h6>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Commission Type <span class="text-danger">*</span></label>
                            <select name="commission_type" id="commission_type" class="form-select" required>
                                <option value="percentage" {{ old('commission_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed"      {{ old('commission_type') == 'fixed'      ? 'selected' : '' }}>Fixed Amount (₹)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold" id="commission_value_label">
                                Commission Value (%) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="commission_value"
                                       id="commission_value"
                                       class="form-control"
                                       value="{{ old('commission_value', 0) }}" required>
                                <span class="input-group-text" id="commission_suffix">%</span>
                            </div>
                            <small class="text-muted" id="commission_hint">
                                Percentage of gross fare deducted as admin commission.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- ── Tax / GST ─────────────────────────────────────────────── --}}
                <h6 class="fw-bold text-muted text-uppercase mb-3 border-bottom pb-2">Tax / GST</h6>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">GST / Tax Percentage (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="tax_percentage"
                                       class="form-control"
                                       value="{{ old('tax_percentage', 0) }}" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Tax applied to the fare subtotal and collected from the user.</small>
                        </div>
                    </div>
                </div>

                {{-- ── Status ────────────────────────────────────────────────── --}}
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.vehicle-pricing.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Pricing Rule</button>
            </div>
        </form>
    </div>

    <script>
        // Toggle commission suffix label between % and ₹
        document.getElementById('commission_type').addEventListener('change', function () {
            const isFixed = this.value === 'fixed';
            document.getElementById('commission_suffix').textContent = isFixed ? '₹' : '%';
            document.getElementById('commission_value_label').innerHTML =
                'Commission Value (' + (isFixed ? '₹' : '%') + ') <span class="text-danger">*</span>';
            document.getElementById('commission_hint').textContent = isFixed
                ? 'Fixed INR amount deducted as admin commission per booking.'
                : 'Percentage of gross fare deducted as admin commission.';
            if (isFixed) {
                document.getElementById('commission_value').removeAttribute('max');
            } else {
                document.getElementById('commission_value').setAttribute('max', '100');
            }
        });
    </script>
@endsection
