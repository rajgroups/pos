@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create CMS Page</h4>
                <h6 class="text-muted">Add a new content page</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.cms-pages.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Pages
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <form action="{{ route('admin.cms-pages.store') }}" method="POST">
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
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Page Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Privacy Policy">
                        </div>
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Page Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control summernote" rows="10" required>{{ old('content') }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="mb-4 d-flex align-items-center">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked>
                                <label class="form-check-label ms-2 fw-semibold" for="statusSwitch">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.cms-pages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Page</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<!-- Assuming summernote or a similar WYSIWYG is included in the admin layout -->
<script>
    $(document).ready(function() {
        if ($('.summernote').length > 0) {
            $('.summernote').summernote({
                height: 300,
                minHeight: null,
                maxHeight: null,
                focus: true
            });
        }
    });
</script>
@endsection
