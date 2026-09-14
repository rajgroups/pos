{{-- @dd($errors->all()); --}}
@extends('layouts.admin.app')
@push('meta')
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Lion POS - Edit Brand">
    <meta name="keywords"
        content="brand, edit brand, brand management, inventory management">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="noindex, nofollow">
    <title>Edit Brand - {{ $brand->name }} | Lion POS</title>
@endpush
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Brand</h4>
                <h6 class="text-muted">Update brand information</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                        class="ti ti-chevron-up"></i></a>
            </li>
        </ul>
        <div class="page-btn">
            <a href="{{ route('admin.brand.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Brands
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3">
            <h5 class="card-title mb-0">
                <i class="ti ti-category me-2 text-primary"></i>
                Edit Brand Information
            </h5>
        </div>

        <form action="{{ route('admin.brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-circle me-2 fs-5"></i>
                            <strong>There were some errors:</strong>
                        </div>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-circle-check me-2 fs-5"></i>
                            {{ session()->get('success') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle me-2 fs-5"></i>
                            {{ session()->get('error') }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Brand Name
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ti ti-tag text-muted"></i>
                                </span>
                                <input type="text" name="name" id="name"
                                    class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror"
                                    value="{{ old('name', $brand->name) }}" placeholder="Enter Brand name" required>
                            </div>
                            <div class="form-text">Enter a descriptive name for your brand</div>
                            @if ($errors->has('name'))
                                <div class="invalid-feedback d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                URL Slug
                                <span class="text-danger ms-1">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="ti ti-link text-muted"></i>
                                </span>
                                <input type="text" name="slug" id="slug"
                                    class="form-control border-start-0 ps-0 @error('slug') is-invalid @enderror"
                                    value="{{ old('slug', $brand->slug) }}" placeholder="brand-slug" required>
                            </div>
                            <div class="form-text">This will be used in URLs. Auto-generated from the name.</div>
                            @if ($errors->has('slug'))
                                <div class="invalid-feedback d-block">
                                    <i class="ti ti-info-circle me-1"></i>
                                    {{ $errors->first('slug') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <div class="card bg-light border-0">
                                <div class="card-body py-3">
                                    <div
                                        class="status-toggle modal-status d-flex justify-content-between align-items-center">

                                        <div>
                                            <span class="status-label fw-semibold">Brand Status</span>
                                            <p class="text-muted mb-0 small">Enable or disable this brand</p>
                                        </div>

                                        <div class="form-check form-switch">
                                            <!-- Hidden input for OFF (0) -->
                                            <input type="hidden" name="status" value="0">

                                            <!-- Checkbox for ON (1) -->
                                            <input type="checkbox"
                                                class="form-check-input @error('status') is-invalid @enderror"
                                                name="status" id="status" value="1"
                                                {{ old('status', $brand->status) == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>

                                    @if ($errors->has('status'))
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="ti ti-info-circle me-1"></i>
                                            {{ $errors->first('status') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Current Images -->
                        <div class="row">
                            @if($brand->icon)
                            <div class="col-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <p class="mb-2 small text-muted">Current Icon</p>
                                        <img src="{{ $brand->icon_url }}" alt="{{ $brand->name }} Icon"
                                             class="img-fluid rounded" style="max-height: 80px;">
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($brand->image)
                            <div class="col-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <p class="mb-2 small text-muted">Current Image</p>
                                        <img src="{{ $brand->image_url }}" alt="{{ $brand->name }} Image"
                                             class="img-fluid rounded" style="max-height: 80px;">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <!-- Brand Icon Upload -->
                        <div class="card border-0 mb-4">
                            <div class="card-header bg-light py-3">
                                <h6 class="card-title mb-0">
                                    <i class="ti ti-icons me-2 text-primary"></i>
                                    Update Brand Icon (Optional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="custom-file-container" data-upload-id="iconImage">
                                    <div class="text-center mb-3">
                                        <div class="icon-upload-preview mb-3 mx-auto">
                                            @if($brand->icon)
                                                <img src="{{ $brand->icon_url }}" class="img-fluid rounded icon-preview" alt="Icon preview" style="max-height: 80px;">
                                            @else
                                                <div class="icon-preview-placeholder">
                                                    <i class="ti ti-photo fs-1 text-muted"></i>
                                                    <p class="mt-2 mb-0 text-muted small">No icon selected</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <label class="custom-file-container__custom-file btn btn-outline-secondary w-100">
                                        <input type="file" name="icon"
                                            class="custom-file-container__custom-file__custom-file-input d-none"
                                            accept="image/*">
                                        <span class="custom-file-container__custom-file__custom-file-control">
                                            <i class="ti ti-upload me-2"></i>Choose New Icon
                                        </span>
                                    </label>
                                    <div class="text-center mt-2">
                                        <a href="javascript:void(0)"
                                            class="custom-file-container__icon-clear text-danger small"
                                            title="Clear Icon">
                                            <i class="ti ti-trash me-1"></i>Remove New Icon
                                        </a>
                                    </div>
                                    <div class="form-text text-center">Recommended size: 64x64px. PNG with transparent background.</div>
                                    <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
                                </div>
                                @if ($errors->has('icon'))
                                    <div class="invalid-feedback d-block mt-2">
                                        <i class="ti ti-info-circle me-1"></i>
                                        {{ $errors->first('icon') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <!-- Brand Image Upload -->
                        <div class="card border-0">
                            <div class="card-header bg-light py-3">
                                <h6 class="card-title mb-0">
                                    <i class="ti ti-photo me-2 text-primary"></i>
                                    Update Brand Image (Optional)
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="custom-file-container" data-upload-id="brandImage">
                                    <div class="text-center mb-3">
                                        <div class="image-upload-preview mb-3 mx-auto">
                                            @if($brand->image)
                                                <img src="{{ $brand->image_url }}" class="img-fluid rounded image-preview" alt="Brand preview">
                                            @else
                                                <div class="image-preview-placeholder">
                                                    <i class="ti ti-cloud-upload fs-1 text-muted"></i>
                                                    <p class="mt-2 mb-0 text-muted">No image selected</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <label class="custom-file-container__custom-file btn btn-outline-primary w-100">
                                        <input type="file" name="image"
                                            class="custom-file-container__custom-file__custom-file-input d-none"
                                            accept="image/*">
                                        <span class="custom-file-container__custom-file__custom-file-control">
                                            <i class="ti ti-upload me-2"></i>Choose New Image
                                        </span>
                                    </label>
                                    <div class="text-center mt-2">
                                        <a href="javascript:void(0)"
                                            class="custom-file-container__image-clear text-danger small"
                                            title="Clear Image">
                                            <i class="ti ti-trash me-1"></i>Remove New Image
                                        </a>
                                    </div>
                                    <div class="form-text text-center">Recommended size: 300x300px. JPG, PNG or GIF.</div>
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light py-3 d-flex justify-content-between">
                <a href="{{ route('admin.brand.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-x me-1"></i>Cancel
                </a>
                <div>
                    <button type="button" class="btn btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>
                    <button type="submit" id="update_brand_btn" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Update Brand
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="ti ti-alert-triangle text-danger fs-1"></i>
                        </div>
                        <h6 class="mb-3">Are you sure you want to delete this brand?</h6>
                        <p class="text-muted">
                            Brand: <strong>{{ $brand->name }}</strong><br>
                            This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.brand.destroy', $brand->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Brand</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Name to slug conversion
            $('#name').on('keyup', function() {
                let name = $(this).val();
                let slug = name.toLowerCase()
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/[^\w\-]+/g, '') // Remove special characters except hyphens
                    .replace(/\-\-+/g, '-') // Replace multiple hyphens with single hyphen
                    .trim('-'); // Trim hyphens from start and end

                $('#slug').val(slug); // Update slug field
            });

            // Brand Image upload preview
            let imageFileInput = $("input[name='image']");
            let imagePreview = $(".image-preview");
            let imagePlaceholder = $(".image-preview-placeholder");
            let imageClearButton = $(".custom-file-container__image-clear");

            imageFileInput.on("change", function(event) {
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        imagePlaceholder.addClass('d-none');
                        imagePreview.attr('src', e.target.result).removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            imageClearButton.on("click", function() {
                imageFileInput.val(""); // Clear file input
                imagePlaceholder.removeClass('d-none');
                imagePreview.addClass('d-none').attr('src', '');
            });

            // Brand Icon upload preview
            let iconFileInput = $("input[name='icon']");
            let iconPreview = $(".icon-preview");
            let iconPlaceholder = $(".icon-preview-placeholder");
            let iconClearButton = $(".custom-file-container__icon-clear");

            iconFileInput.on("change", function(event) {
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        if (iconPlaceholder.length) {
                            iconPlaceholder.addClass('d-none');
                        }
                        iconPreview.attr('src', e.target.result).removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            iconClearButton.on("click", function() {
                iconFileInput.val(""); // Clear file input
                if (iconPlaceholder.length) {
                    iconPlaceholder.removeClass('d-none');
                }
                iconPreview.addClass('d-none').attr('src', '');
            });

            // Form validation styling
            $('form').on('submit', function() {
                $('#update_brand_btn').prop('disabled', true).html(
                    '<i class="ti ti-loader me-1"></i>Updating...');
            });

            // Check if there's an existing icon/image on page load
            @if($brand->icon)
                $('.icon-preview-placeholder').addClass('d-none');
            @endif

            @if($brand->image)
                $('.image-preview-placeholder').addClass('d-none');
            @endif
        });
    </script>
@endpush

@push('css')
    <style>
        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }

        .input-group-text {
            transition: all 0.3s ease;
        }

        .image-upload-preview {
            width: 200px;
            height: 200px;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .icon-upload-preview {
            width: 120px;
            height: 120px;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .image-upload-preview:hover,
        .icon-upload-preview:hover {
            border-color: #0d6efd;
        }

        .image-preview {
            max-height: 100%;
            object-fit: cover;
        }

        .icon-preview {
            max-height: 100%;
            object-fit: contain;
        }

        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .alert {
            border: none;
            border-radius: 10px;
        }

        .page-header {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }

        .table-top-head {
            margin: 0;
        }

        .table-top-head li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f8f9fa;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .table-top-head li a:hover {
            background: #e9ecef;
            color: #495057;
        }

        /* Different styling for icon upload */
        .custom-file-container[data-upload-id="iconImage"] .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }

        .custom-file-container[data-upload-id="iconImage"] .btn:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            border-color: #5a6fd8;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        /* Current images styling */
        .current-images .card {
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .current-images .card:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
        }
    </style>
@endpush
