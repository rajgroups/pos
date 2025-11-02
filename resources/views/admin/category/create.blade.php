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
    <title>Lion POS - Inventory Management & Admin Dashboard</title>
@endpush
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Category</h4>
                <h6>Manage your Category</h6>
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
            <a href="{{ route('admin.category.index') }}" class="btn btn-primary"><i
                    class="ti ti-arrow-left me-1"></i>Back</a>
        </div>
    </div>

    <div class="card">

            <form action="{{ route('admin.category.store') }}" method="POST"  enctype="multipart/form-data">
                @csrf
                @method('post')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        There were some errors with your request.
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if (session()->has('success'))
                                    <div class="alert alert-solid-success rounded-pill alert-dismissible fade show">
                                       {{ session()->get('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-solid-danger rounded-pill alert-dismissible fade show">
                                        {{ session()->get('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
                                    </div>
                                @endif
                                
                            </div>
                            <div class="mb-3">
                                <label class="form-label">name<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                 <div class="valid-feedback">Looks good!</div>
                                 @if ($errors->has('name'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('name') }}
                                    </div>
                                 @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label">slug<span class="text-danger ms-1">*</span></label>
                                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                                @if ($errors->has('slug'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('slug') }}
                                    </div>
                                @endif
                            </div>
        
                            <div class="mb-0">
                                <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                                    <span class="status-label">Status</span>
                                    <input type="checkbox" name="status" id="status" name="status" class="check @error('status') is-invalid @enderror" value="active" checked required>
                                    @if($errors->has('status'))
                                    <div class="invalid-feedback d-block">
                                        {{ $errors->first('status') }}
                                    </div>
                                    @endif
                                    <label for="user2" class="checktoggle"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-file-container" data-upload-id="myFirstImage">
                                <label>Upload (Single File) <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a></label>
                                <label class="custom-file-container__custom-file" >
                                    <input type="file" name="image" class="custom-file-container__custom-file__custom-file-input">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                    <span class="custom-file-container__custom-file__custom-file-control"></span>
                                </label>
                                <div class="custom-file-container__image-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn me-2 btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="add_unit_btn" class="btn btn-primary">Add Category</button>
                </div>
            </form>
    </div>
    <!-- /product list -->
    </div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $('#name').on('keyup', function () {
            let name = $(this).val();
            let cleanedName = name.replace(/[0-9]/g, ''); // Remove numbers from name
            $(this).val(cleanedName); // Update name field

            let slug = cleanedName.toLowerCase()
                .replace(/\s+/g, '-')  // Replace spaces with hyphens
                .replace(/[^\w\-]+/g, '') // Remove special characters except hyphens
                .replace(/\-\-+/g, '-') // Replace multiple hyphens with single hyphen
                .trim('-'); // Trim hyphens from start and end

            $('#slug').val(slug); // Update slug field
        });

        let fileInput = $(".custom-file-container__custom-file__custom-file-input");
        let imagePreview = $(".custom-file-container__image-preview");
        let clearButton = $(".custom-file-container__image-clear");

        fileInput.on("change", function (event) {
            let file = event.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.css({
                        "background-image": `url(${e.target.result})`,
                        "display": "block"
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        clearButton.on("click", function () {
            fileInput.val(""); // Clear file input
            imagePreview.css({
                "background-image": "none",
                "display": "none"
            });
        });        
    });
</script>
@endpush
@push('css')
<style>
    .custom-file-container__image-preview {
    width: 200px;
    height: 200px;
    background-color: #f3f3f3;
    background-size: cover;
    background-position: center;
    display: none;
    border: 1px solid #ddd;
    margin-top: 10px;
}
</style>
@endpush