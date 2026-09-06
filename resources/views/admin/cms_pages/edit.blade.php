@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit CMS Page</h4>
                <h6 class="text-muted">Update content for {{ $page->title }}</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.cms-pages.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Pages
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <form action="{{ route('admin.cms-pages.update', $page->id) }}" method="POST">
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
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Page Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                        </div>
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Slug (Auto-generated)</label>
                            <input type="text" class="form-control bg-light" value="{{ $page->slug }}" disabled>
                            <small class="text-muted">Slug is automatically generated from the title and is used for API access.</small>
                        </div>
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Page Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control summernote" rows="10" required>{{ old('content', $page->content) }}</textarea>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="mb-4 d-flex align-items-center">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" {{ $page->status ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 fw-semibold" for="statusSwitch">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.cms-pages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Page</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        if ($('.summernote').length > 0) {
            $('.summernote').summernote({
                height: 300,
                minHeight: null,
                maxHeight: null,
                focus: false
            });
        }
    });
</script>
@endsection
