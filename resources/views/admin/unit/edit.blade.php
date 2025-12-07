@extends('layouts.admin.app')

@push('meta')
<title>Edit Unit - POS</title>
<meta charset="utf-8">
<meta name="description" content="Edit measurement units for POS inventory">
@endpush

@section('content')

<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4 class="fw-bold">Edit Unit</h4>
            <h6 class="text-muted">Update measurement units for your POS inventory</h6>
        </div>
    </div>

    <ul class="table-top-head">
        <li><a data-bs-toggle="tooltip" title="Refresh"><i class="ti ti-refresh"></i></a></li>
        <li><a data-bs-toggle="tooltip" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li>
    </ul>

    <div class="page-btn">
        <a href="{{ route('admin.unit.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-arrow-left me-1"></i>Back to Units
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">

    <div class="card-header bg-light py-3">
        <h5 class="card-title mb-0">
            <i class="ti ti-edit text-primary me-2"></i>
            Edit Unit Information
        </h5>
    </div>

    <form action="{{ route('admin.unit.update', $unit->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3">
                    <strong>Please fix the following issues:</strong>
                    <ul class="mt-2 mb-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success rounded-3">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="row">

                {{-- Unit Name --}}
                <div class="col-lg-6 mb-4">
                    <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-tag"></i></span>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $unit->name) }}"
                               required>
                    </div>
                    @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Short Name --}}
                <div class="col-lg-6 mb-4">
                    <label class="form-label fw-semibold">Short Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-letter-spacing"></i></span>
                        <input type="text"
                               name="shortname"
                               class="form-control @error('shortname') is-invalid @enderror"
                               value="{{ old('shortname', $unit->shortname) }}"
                               required>
                    </div>
                    @error('shortname') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- No of Products --}}
                <div class="col-lg-6 mb-4">
                    <label class="form-label fw-semibold">No of Products <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-box"></i></span>
                        <input type="number"
                               name="no_of_product"
                               class="form-control @error('no_of_product') is-invalid @enderror"
                               value="{{ old('no_of_product', $unit->no_of_product) }}"
                               required>
                    </div>
                    @error('no_of_product') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Type --}}
                <div class="col-lg-6 mb-4">
                    <label class="form-label fw-semibold">Unit Type <span class="text-muted">(optional)</span></label>
                    <select name="type" class="form-select">
                        <option value="">-- Select Type --</option>
                        <option value="weight" {{ $unit->type == 'weight' ? 'selected' : '' }}>Weight</option>
                        <option value="length" {{ $unit->type == 'length' ? 'selected' : '' }}>Length</option>
                        <option value="volume" {{ $unit->type == 'volume' ? 'selected' : '' }}>Volume</option>
                        <option value="quantity" {{ $unit->type == 'quantity' ? 'selected' : '' }}>Quantity</option>
                    </select>
                </div>

                {{-- Description --}}
                <div class="col-lg-12 mb-4">
                    <label class="form-label fw-semibold">Description (optional)</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $unit->description) }}</textarea>
                </div>

                {{-- Status --}}
                <div class="col-lg-12">
                    <div class="card bg-light border-0">
                        <div class="card-body py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold">Status</span>
                                <p class="small text-muted mb-0">Enable or disable this unit</p>
                            </div>

                            <div class="form-check form-switch">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox"
                                       name="status"
                                       value="1"
                                       class="form-check-input"
                                       {{ old('status', $unit->status) == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- end row --}}
        </div>

        <div class="card-footer bg-light py-3 d-flex justify-content-between">
            <a href="{{ route('admin.unit.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-x"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i>Update Unit
            </button>
        </div>

    </form>

</div>

@endsection

@push('css')
<style>
.card { border-radius: 12px; }
.input-group-text { border-radius: 8px 0 0 8px; }
.form-control, .form-select { border-radius: 0 8px 8px 0; }
.page-header { border-radius: 12px; background: #fff; padding: 20px; }
.alert { border-radius: 10px; }
.btn { border-radius: 8px; }
</style>
@endpush
