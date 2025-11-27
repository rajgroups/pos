@extends('layouts.admin.app')
@push('meta')
<!-- Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description"
   content="Lion POS is a powerful Bootstrap based Inventory Management Admin Template designed for businesses, offering seamless invoicing, project tracking, and estimates.">
<meta name="keywords"
   content="inventory management, admin dashboard, bootstrap template, invoicing, estimates, business management, responsive admin, POS system">
<meta name="author" content="Dreams Technologies">
<meta name="robots" content="index, follow">
<title>Brand - Lion POS</title>
@endpush
@section('content')
<div class="content">
   <div class="page-header">
      <div class="add-item d-flex">
         <div class="page-title">
            <h4 class="fw-bold">Brand</h4>
            <h6>Manage your brands</h6>
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
         <h4 class="fw-bold mb-0">Add Brand</h4>
      </div>
      <form action="{{ route('admin.brand.store') }}" method="POST" enctype="multipart/form-data">
         @csrf
         <div class="card-body">
            <!-- Image Upload -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Brand Logo</label>
                <div class="row align-items-center">
                    <!-- Preview Column -->
                    <div class="col-md-4 col-12 text-center mb-3 mb-md-0">
                        <div id="previewBox"
                            class="border rounded-3 d-flex align-items-center justify-content-center"
                            style="height: 150px; width: 150px; margin:auto; background:#f8f9fc; cursor:pointer; overflow:hidden;">
                            <img id="previewImage"
                                src="{{ asset('default.png') }}"
                                style="display:none; width:100%; height:100%; object-fit:cover;">
                            <div id="uploadPlaceholder" class="text-muted">
                                <i class="ti ti-upload fs-1"></i>
                                <p class="small mb-0">Upload Image</p>
                            </div>
                        </div>
                    </div>
                    <!-- File Input Column -->
                    <div class="col-md-6 col-12">
                        <input type="file" name="image" id="imageInput" class="form-control mb-2" accept="image/*">
                        <p class="text-muted small">Allowed: JPG, PNG • Max 2MB</p>
                    </div>
                </div>
            </div>

           <div class="row mb-3">

                <!-- Brand Name -->
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="brand_name" class="form-control" required>
                </div>

                <!-- Status Toggle -->
                <div class="col-md-6 col-12 d-flex align-items-center">
                    <div class="w-100">
                        
                        <div class="row mb-3 mt-4">
                            <div class="col-md-6 col-12 mb-3 mb-md-0 mt-3">
                                 <label class="form-label d-block">Status <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                    <label class="form-check-label" for="status"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

         </div>
         <!-- Footer Buttons -->
         <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('admin.brand.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Add Brand</button>
         </div>
      </form>
   </div>
</div>
@endsection
@push('scripts')
<script>
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    const previewBox = document.getElementById('previewBox');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = "block";
                uploadPlaceholder.style.display = "none";
            }

            reader.readAsDataURL(file);
        }
    });

    // Click preview box → trigger file input
    previewBox.addEventListener('click', () => {
        imageInput.click();
    });
</script>
@endpush
