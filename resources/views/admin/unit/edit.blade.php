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
                <h4 class="fw-bold">Units</h4>
                <h6>Manage your units</h6>
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
            <a href="{{ route('admin.unit.index') }}" class="btn btn-primary"><i
                    class="ti ti-arrow-left me-1"></i>Back</a>
        </div>
    </div>

    <div class="card">

            <form action="{{ route('admin.unit.update',$unit->id) }}" method="POST">
                @csrf
                @method('put')
                <div class="card-body">
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
                        @if (session()->has('fail'))
                            <div class="alert alert-solid-danger rounded-pill alert-dismissible fade show">
                                {{ session()->get('fail') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="fas fa-xmark"></i></button>
                            </div>
                        @endif
                        
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit<span class="text-danger ms-1">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$unit->name) }}" required>
                         <div class="valid-feedback">Looks good!</div>
                         @if ($errors->has('name'))
                            <div class="invalid-feedback d-block">
                                {{ $errors->first('name') }}
                            </div>
                         @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Name<span class="text-danger ms-1">*</span></label>
                        <input type="text" name="shortname" id="shortname" class="form-control @error('shortname') is-invalid @enderror" value="{{ old('shortname',$unit->shortname) }}" required>
                        @if ($errors->has('shortname'))
                            <div class="invalid-feedback d-block">
                                {{ $errors->first('shortname') }}
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No of Product<span class="text-danger ms-1">*</span></label>
                        <input type="text" name="no_of_product" id="no_of_product" class="form-control @error('no_of_product') is-invalid @enderror" value="{{ old('no_of_product',$unit->no_of_product) }}" required>
                        @if ($errors->has('no_of_product'))
                            <div class="invalid-feedback d-block">
                                {{ $errors->first('no_of_product') }}
                            </div>
                        @endif
                    </div>
                    <div class="mb-0">
                        <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                            <span class="status-label">Status</span>
                            <input type="checkbox" name="status" id="status" name="status" class="check @error('status') is-invalid @enderror" value="active"
                            @if($unit->status == 'active')
                                checked
                            @endif
                            required>
                            @if($errors->has('status'))
                            <div class="invalid-feedback d-block">
                                {{ $errors->first('status') }}
                            </div>
                            @endif
                            <label for="user2" class="checktoggle"></label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn me-2 btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="add_unit_btn" class="btn btn-primary">Update Unit</button>
                </div>
            </form>
    </div>
    <!-- /product list -->
    </div>
@endsection
