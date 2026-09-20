@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Admin Details</h4>
                <h6>View administrator information</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.admin.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left me-1"></i>Back to List</a>
            <a href="{{ route('admin.admin.edit', $admin->id) }}" class="btn btn-primary"><i class="ti ti-pencil me-1"></i>Edit</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-1">Name</h6>
                    <p class="fw-bold mb-0 fs-16">{{ $admin->name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-1">Email</h6>
                    <p class="fw-bold mb-0 fs-16">{{ $admin->email }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-1">Phone</h6>
                    <p class="fw-bold mb-0 fs-16">{{ $admin->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-1">Status</h6>
                    <p class="mb-0">
                        @if($admin->status == 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-1">Created At</h6>
                    <p class="fw-bold mb-0 fs-16">{{ $admin->created_at ? $admin->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted mb-1">Last Updated</h6>
                    <p class="fw-bold mb-0 fs-16">{{ $admin->updated_at ? $admin->updated_at->format('d M Y, h:i A') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
