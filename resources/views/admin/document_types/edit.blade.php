@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Document Type</h4>
                <h6 class="text-muted">Update document type settings</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Document Types
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <form action="{{ route('admin.document-types.update', $documentType->id) }}" method="POST">
            @csrf
            @method('PUT')
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
                
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3">
                        {{ session()->get('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Document Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $documentType->name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $documentType->slug) }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Target Type <span class="text-danger">*</span></label>
                            <select name="for_type" class="form-select" required>
                                <option value="driver" {{ old('for_type', $documentType->for_type) == 'driver' ? 'selected' : '' }}>Driver</option>
                                <option value="vehicle" {{ old('for_type', $documentType->for_type) == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                                <option value="both" {{ old('for_type', $documentType->for_type) == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Flags <span class="text-danger">*</span></label>
                            
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_required" value="0">
                                <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ old('is_required', $documentType->is_required) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label">Is Required (Mandatory)</label>
                            </div>
                            
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="has_expiry" value="0">
                                <input class="form-check-input" type="checkbox" name="has_expiry" value="1" {{ old('has_expiry', $documentType->has_expiry) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label">Has Expiry Date</label>
                            </div>

                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $documentType->is_active) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.document-types.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Document Type</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#name').on('keyup', function() {
            let name = $(this).val();
            let slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            $('#slug').val(slug);
        });
    });
</script>
@endpush
