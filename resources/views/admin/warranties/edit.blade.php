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
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Warranty</h4>
                <h6>Update warranty details</h6>
            </div>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.warranty.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Warranty List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">

        <div class="card-header">
            <h4 class="fw-bold mb-0">Edit Warranty</h4>
        </div>

        <form action="{{ route('admin.warranty.update', $warranty->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="row">

                    <!-- Warranty Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Warranty <span class="text-danger">*</span></label>
                        <input type="text" name="warranty" class="form-control"
                            value="{{ $warranty->warranty }}" required>
                    </div>

                    <!-- Duration -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Duration <span class="text-danger">*</span></label>
                        <input type="number" name="duration" class="form-control"
                            value="{{ $warranty->duration }}" required>
                    </div>

                    <!-- Period -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Period <span class="text-danger">*</span></label>
                        <select name="period" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Month" {{ $warranty->period == 'Month' ? 'selected' : '' }}>Month</option>
                            <option value="Year"  {{ $warranty->period == 'Year' ? 'selected' : '' }}>Year</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required>{{ $warranty->description }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mt-2">
                        <div class="status-toggle d-flex align-items-center justify-content-between">
                            <span class="status-label">Status</span>
                            <div>
                                <input type="checkbox" id="status" name="status" class="check" 
                                    {{ $warranty->status ? 'checked' : '' }}>
                                <label for="status" class="checktoggle"></label>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.warranty.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Warranty</button>
            </div>

        </form>

    </div>

</div>
@endsection
