@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Driver Documents</h4>
                <h6>Manage uploaded documents for drivers</h6>
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
            <a href="{{ route('admin.driver-documents.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Upload Document</a>
        </div>
    </div>

    <div class="card">
        @if (session()->has('success'))
            <div class="alert alert-solid-success rounded-pill alert-dismissible fade show m-3">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Driver Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Uploaded Documents</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $driver)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($driver->profile_image)
                                        <img src="{{ asset('storage/'.$driver->profile_image) }}" class="avatar avatar-sm me-2 rounded-circle" alt="img">
                                    @else
                                        <div class="avatar avatar-sm me-2 rounded-circle bg-primary-transparent d-flex align-items-center justify-content-center">
                                            <span class="text-primary fw-bold">{{ substr($driver->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <span class="fw-medium">{{ $driver->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>{{ $driver->phone ?? 'N/A' }}</td>
                            <td>{{ $driver->email ?? 'N/A' }}</td>
                            <td>
                                @if($driver->status === 'active')
                                    <span class="badge bg-success-transparent text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill px-3">{{ $driver->documents_count }} Document(s)</span>
                            </td>
                            <td class="action-table-data">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.driver-documents.show', $driver->id) }}">
                                    <i class="ti ti-eye me-1"></i> View Documents
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No drivers with uploaded documents found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($drivers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $drivers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
