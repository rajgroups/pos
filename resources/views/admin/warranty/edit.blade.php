@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Warranty - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">Edit Warranty</h4>
            <h6>Update warranty details for products</h6>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.warranty.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Warranty List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-lg rounded-3">
        <div class="card-header bg-gradient-primary text-white py-3 rounded-top-3">
            <h4 class="fw-bold mb-0 d-flex align-items-center">
                <i class="ti ti-edit me-2"></i> Edit Warranty
            </h4>
        </div>

        <form action="{{ route('admin.warranty.update', $warranty->id) }}" method="POST">
            @csrf
            @method('PUT')

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

                <div class="row g-4">

                    <!-- Warranty Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warranty Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-tag text-primary"></i></span>
                            <input type="text" name="warranty" class="form-control"
                                   value="{{ old('warranty', $warranty->warranty) }}" required>
                        </div>
                    </div>

                    <!-- Warranty Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warranty Type <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-category text-primary"></i></span>
                            <select name="type" class="form-select" required>
                                <option value="Standard" {{ old('type', $warranty->type) == 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="Extended" {{ old('type', $warranty->type) == 'Extended' ? 'selected' : '' }}>Extended</option>
                                <option value="Replacement" {{ old('type', $warranty->type) == 'Replacement' ? 'selected' : '' }}>Replacement</option>
                                <option value="Service Warranty" {{ old('type', $warranty->type) == 'Service Warranty' ? 'selected' : '' }}>Service Warranty</option>
                            </select>
                        </div>
                    </div>

                    <!-- Warranty Code -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Warranty Code</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-barcode text-primary"></i></span>
                            <input type="text" name="code" class="form-control"
                                   value="{{ old('code', $warranty->code) }}">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Duration</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-calendar-time text-primary"></i></span>
                            <input type="number" name="duration" class="form-control"
                                   value="{{ old('duration', $warranty->duration) }}" min="1">
                        </div>
                    </div>

                    <!-- Period -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Period</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-clock text-primary"></i></span>
                            <select name="period" class="form-select">
                                <option value="" disabled>Select</option>
                                <option value="Day" {{ old('period', $warranty->period) == 'Day' ? 'selected' : '' }}>Day(s)</option>
                                <option value="Week" {{ old('period', $warranty->period) == 'Week' ? 'selected' : '' }}>Week(s)</option>
                                <option value="Month" {{ old('period', $warranty->period) == 'Month' ? 'selected' : '' }}>Month(s)</option>
                                <option value="Year" {{ old('period', $warranty->period) == 'Year' ? 'selected' : '' }}>Year(s)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Starts After -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Starts After (Days)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-calendar-plus text-primary"></i></span>
                            <input type="number" name="start_after" class="form-control"
                                   value="{{ old('start_after', $warranty->start_after) }}" min="0">
                        </div>
                    </div>

                    <!-- Lifetime -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1">
                                        <i class="ti ti-infinity text-warning me-2"></i> Lifetime Warranty
                                    </h6>
                                    <p class="text-muted small mb-0">Enable unlimited warranty period</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="lifetime" name="lifetime" value="1"
                                           {{ old('lifetime', $warranty->lifetime) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Max Claims -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Maximum Claims Allowed</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ti ti-repeat text-primary"></i></span>
                            <input type="number" name="max_claims" class="form-control"
                                   value="{{ old('max_claims', $warranty->max_claims) }}" min="0">
                        </div>
                    </div>

                    <!-- Replacement Allowed -->
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1">
                                        <i class="ti ti-replace text-success me-2"></i> Replacement Allowed
                                    </h6>
                                    <p class="text-muted small mb-0">Allow product replacement under warranty</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="replacement_allowed" name="replacement_allowed" value="1"
                                           {{ old('replacement_allowed', $warranty->replacement_allowed) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required>{{ old('description', $warranty->description) }}</textarea>
                    </div>

                    <!-- Terms -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Terms & Conditions</label>
                        <textarea name="terms" class="form-control" rows="4">{{ old('terms', $warranty->terms) }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <div class="card border">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-1"><i class="ti ti-toggle-right text-primary me-2"></i> Warranty Status</h6>
                                    <p class="text-muted small mb-0">Enable or disable this warranty</p>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="1"
                                               {{ old('status', $warranty->status) == 1 ? 'checked' : '' }}>
                                        <span class="badge bg-success">Active</span>
                                    </label>

                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="0"
                                               {{ old('status', $warranty->status) == 0 ? 'checked' : '' }}>
                                        <span class="badge bg-danger">Inactive</span>
                                    </label>
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
                    <i class="ti ti-device-floppy me-2"></i> Update Warranty
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

    toggleDurationFields();
    lifetimeCheckbox.addEventListener('change', toggleDurationFields);
});
</script>
@endpush
