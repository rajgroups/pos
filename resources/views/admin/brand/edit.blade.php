@extends('layouts.admin.app')

@push('meta')
<!-- Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Brand - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Brand</h4>
                <h6>Update brand details</h6>
            </div>
        </div>

        <div class="page-btn">
            <a href="{{ route('admin.brand.index') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i> List of Brand
            </a>
        </div>
    </div>

    <div class="card">

        <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="fw-bold mb-0">Edit Brand</h4>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('admin.brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">

                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Brand Logo</label>

                    <div class="row align-items-center">

                        <!-- Preview -->
                        <div class="col-md-4 col-12 text-center mb-3 mb-md-0">

                            <div id="previewBox"
                                class="border rounded-3 d-flex align-items-center justify-content-center"
                                style="height: 150px; width: 150px; margin:auto; background:#f8f9fc; cursor:pointer; overflow:hidden;">

                                <img id="previewImage"
                                    src="{{ $brand->image ? asset('uploads/brand/'.$brand->image) : '' }}"
                                    style="{{ $brand->image ? 'display:block;' : 'display:none;' }}
                                           width:100%; height:100%; object-fit:cover;">

                                @if(!$brand->image)
                                <div id="uploadPlaceholder" class="text-muted">
                                    <i class="ti ti-upload fs-1"></i>
                                    <p class="small mb-0">Upload Image</p>
                                </div>
                                @endif
                            </div>

                        </div>

                        <!-- File Input -->
                        <div class="col-md-6 col-12">
                            <input type="file" name="image" id="imageInput" class="form-control mb-2" accept="image/*">
                            <p class="text-muted small">Allowed: JPG, PNG • Max 2MB</p>
                        </div>

                    </div>
                </div>


                <!-- Brand Name + Status -->
                <div class="row mb-3">

                    <!-- Brand Name -->
                    <div class="col-md-6 col-12 mb-3 mb-md-0">
                        <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="brand_name" class="form-control"
                            value="{{ $brand->brand_name }}" required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-4 col-12 d-flex align-items-center">
                        <div class="w-100">
                            <label class="form-label d-block">Status</label>

                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="status" name="status"
                                       {{ $brand->status ? 'checked' : '' }}>
                                <label class="form-check-label" for="status"></label>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.brand.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Brand</button>
            </div>

        </form>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const previewImage = document.getElementById('previewImage');
    const previewBox = document.getElementById('previewBox');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imageInput = document.getElementById('imageInput');

    imageInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                previewImage.src = e.target.result;
                previewImage.style.display = "block";
                if (uploadPlaceholder) uploadPlaceholder.style.display = "none";
            }

            reader.readAsDataURL(file);
        }
    });

    previewBox.addEventListener('click', () => {
        imageInput.click();
    });
</script>
@endpush
