@extends('layouts.admin.app')

@push('meta')
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Store - Lion POS</title>
@endpush

@section('content')
<div class="content">

    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div class="page-title">
            <h4 class="fw-bold">Add Store</h4>
            <h6>Create store details</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.stores.index') }}" class="btn btn-primary">
                <i class="ti ti-list me-1"></i> Store List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">

        <div class="card-header">
            <h4 class="fw-bold mb-0">Add Store</h4>
        </div>

        <form action="{{ route('admin.stores.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row">

                    <!-- Store Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Store Name <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control" required>
                    </div>

                    <!-- User Name -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">User Name <span class="text-danger">*</span></label>
                        <input type="text" name="user_name" class="form-control" required>
                    </div>

                    <!-- Password -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="pass-group">
                            <input type="password" name="password" class="form-control pass-input" required>
                            <span class="fas toggle-password fa-eye-slash"></span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="status-toggle d-flex justify-content-between align-items-center w-100">
                            <span class="status-label">Status</span>
                            <input type="checkbox" id="store_status" name="status" class="check" checked>
                            <label for="store_status" class="checktoggle mb-0"></label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Store</button>
            </div>

        </form>

    </div>

</div>
@endsection
