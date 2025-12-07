@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Warranty - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">Add Warranty</h4>
            <h6>Create warranty details for products</h6>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.variant-attributes.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Variant List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
            <h4 class="fw-bold mb-0 d-flex align-items-center">
                <i class="ti ti-shield-check me-2"></i> Warranty Information
            </h4>
        </div>

        <form action="{{ route('admin.warranty.store') }}" method="POST">
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

                    <!-- Warranty Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warranty Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-tag text-primary"></i>
                            </span>
                            <input type="text" name="warranty" class="form-control @error('warranty') is-invalid @enderror"
                                   placeholder="e.g., Standard 1 Year Warranty"
                                   value="{{ old('warranty') }}" required>
                        </div>
                        @error('warranty')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Enter a descriptive warranty name
                            </div>
                        @enderror
                    </div>

                    <!-- Warranty Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warranty Type <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-category text-primary"></i>
                            </span>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Warranty Type</option>
                                <option value="Standard" {{ old('type') == 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="Extended" {{ old('type') == 'Extended' ? 'selected' : '' }}>Extended</option>
                                <option value="Replacement" {{ old('type') == 'Replacement' ? 'selected' : '' }}>Replacement</option>
                                <option value="Service Warranty" {{ old('type') == 'Service Warranty' ? 'selected' : '' }}>Service Warranty</option>
                            </select>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Choose the type of warranty
                            </div>
                        @enderror
                    </div>

                    <!-- Warranty Code -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warranty Code</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-barcode text-primary"></i>
                            </span>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                   placeholder="e.g., WR-001"
                                   value="{{ old('code') }}">
                        </div>
                        @error('code')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Optional unique code for reference
                            </div>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Duration <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-calendar-time text-primary"></i>
                            </span>
                            <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror"
                                   placeholder="e.g., 12"
                                   value="{{ old('duration') }}" required min="1">
                        </div>
                        @error('duration')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Duration number (1, 12, 24, etc.)
                            </div>
                        @enderror
                    </div>

                    <!-- Period -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Period <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-clock text-primary"></i>
                            </span>
                            <select name="period" class="form-select @error('period') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Period</option>
                                <option value="Day" {{ old('period') == 'Day' ? 'selected' : '' }}>Day(s)</option>
                                <option value="Week" {{ old('period') == 'Week' ? 'selected' : '' }}>Week(s)</option>
                                <option value="Month" {{ old('period') == 'Month' ? 'selected' : '' }}>Month(s)</option>
                                <option value="Year" {{ old('period') == 'Year' ? 'selected' : '' }}>Year(s)</option>
                            </select>
                        </div>
                        @error('period')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Time period for warranty
                            </div>
                        @enderror
                    </div>

                    <!-- Start After (Days) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Starts After (Days)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-calendar-plus text-primary"></i>
                            </span>
                            <input type="number" name="start_after" class="form-control @error('start_after') is-invalid @enderror"
                                   placeholder="e.g., 7"
                                   value="{{ old('start_after', 0) }}" min="0">
                        </div>
                        @error('start_after')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> 0 = starts immediately after purchase
                            </div>
                        @enderror
                    </div>

                    <!-- Lifetime Warranty -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1 d-flex align-items-center">
                                        <i class="ti ti-infinity text-warning me-2"></i> Lifetime Warranty
                                    </h6>
                                    <p class="text-muted small mb-0">Enable for unlimited warranty period</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="lifetime" name="lifetime" value="1"
                                           {{ old('lifetime') ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Max Claims Allowed -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Maximum Claims Allowed</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="ti ti-repeat text-primary"></i>
                            </span>
                            <input type="number" name="max_claims" class="form-control @error('max_claims') is-invalid @enderror"
                                   placeholder="e.g., 3"
                                   value="{{ old('max_claims') }}" min="0">
                        </div>
                        @error('max_claims')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Leave blank for unlimited claims
                            </div>
                        @enderror
                    </div>

                    <!-- Replacement Allowed -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1 d-flex align-items-center">
                                        <i class="ti ti-replace text-success me-2"></i> Replacement Allowed
                                    </h6>
                                    <p class="text-muted small mb-0">Allow product replacement under warranty</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="replacement_allowed" name="replacement_allowed" value="1"
                                           {{ old('replacement_allowed') ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Description <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light align-items-start">
                                <i class="ti ti-file-text text-primary mt-1"></i>
                            </span>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" placeholder="Describe the warranty coverage and benefits" required>{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Brief description of warranty coverage
                            </div>
                        @enderror
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Terms & Conditions</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light align-items-start">
                                <i class="ti ti-notebook text-primary mt-1"></i>
                            </span>
                            <textarea name="terms" class="form-control @error('terms') is-invalid @enderror"
                                      rows="4" placeholder="Enter warranty terms, conditions, and limitations...">{{ old('terms') }}</textarea>
                        </div>
                        @error('terms')
                            <div class="text-danger small mt-1 d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i> {{ $message }}
                            </div>
                        @else
                            <div class="form-text text-muted d-flex align-items-center mt-1">
                                <i class="ti ti-info-circle me-1"></i> Detailed terms and conditions
                            </div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1 d-flex align-items-center">
                                        <i class="ti ti-toggle-right text-primary me-2"></i> Warranty Status
                                    </h6>
                                    <p class="text-muted small mb-0">Enable or disable this warranty</p>
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

            <div class="card-footer bg-light py-3 rounded-bottom-3 d-flex justify-content-between">
                <a href="{{ route('admin.warranty.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="ti ti-x me-2"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ti ti-plus me-2"></i> Create Warranty
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lifetimeCheckbox = document.getElementById('lifetime');
        const durationInput = document.querySelector('input[name="duration"]');
        const periodSelect = document.querySelector('select[name="period"]');

        function toggleDurationFields() {
            if (lifetimeCheckbox.checked) {
                durationInput.disabled = true;
                periodSelect.disabled = true;
                durationInput.value = '';
                periodSelect.value = '';
            } else {
                durationInput.disabled = false;
                periodSelect.disabled = false;
            }
        }

        // Initial state
        toggleDurationFields();

        // Add event listener
        lifetimeCheckbox.addEventListener('change', toggleDurationFields);
    });
</script>
@endpush
