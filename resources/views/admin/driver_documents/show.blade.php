@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Driver Documents</h4>
                <h6>Documents uploaded by {{ $driver->name }}</h6>
            </div>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.driver-documents.index') }}" class="btn btn-outline-primary shadow-sm">
                <i class="ti ti-arrow-left me-1"></i>Back to Drivers
            </a>
            <a href="{{ route('admin.driver-documents.create') }}" class="btn btn-primary ms-2 shadow-sm">
                <i class="ti ti-circle-plus me-1"></i>Add New Document
            </a>
        </div>
    </div>

    <!-- Driver Info Banner -->
    <div class="profile-banner position-relative rounded-3 mb-4 shadow-sm" style="background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);">
        <div class="d-flex align-items-center p-4 text-white">
            @if($driver->profile_image)
                <img src="{{ asset('storage/'.$driver->profile_image) }}" class="avatar avatar-xl me-3 rounded-circle border border-3 border-white shadow-sm" alt="img">
            @else
                <div class="avatar avatar-xl me-3 rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-sm">
                    <span class="fs-2 fw-bold">{{ substr($driver->name, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h4 class="mb-1 fw-bold text-white">{{ $driver->name }}</h4>
                <div class="d-flex gap-3 text-white-50">
                    <span><i class="ti ti-phone me-1"></i>{{ $driver->phone }}</span>
                    <span><i class="ti ti-mail me-1"></i>{{ $driver->email ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="ms-auto">
                <span class="badge bg-white text-primary rounded-pill px-3 py-2 fs-14 fw-semibold shadow-sm">
                    {{ $driver->documents->count() }} Document(s)
                </span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
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
                            <th>Document Type</th>
                            <th>Document Number</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($driver->documents as $document)
                        <tr>
                            <td>
                                <span class="fw-medium text-dark">{{ $document->documentType->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $document->document_number ?? 'N/A' }}</td>
                            <td>
                                @if($document->expiry_date)
                                    {{ $document->expiry_date->format('d M Y') }}
                                    @if($document->expiry_date->isPast())
                                        <span class="badge bg-danger-transparent text-danger ms-2"><i class="ti ti-alert-circle me-1"></i>Expired</span>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($document->status === 'approved')
                                    <span class="badge bg-success-transparent text-success"><i class="ti ti-check me-1"></i>Approved</span>
                                @elseif($document->status === 'rejected')
                                    <span class="badge bg-danger-transparent text-danger"><i class="ti ti-x me-1"></i>Rejected</span>
                                @else
                                    <span class="badge bg-warning-transparent text-warning"><i class="ti ti-clock me-1"></i>Pending</span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2 text-primary bg-primary-transparent rounded" href="{{route('admin.driver-documents.edit',$document->id)}}" data-bs-toggle="tooltip" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    @if($document->file_path)
                                        <a class="me-2 p-2 text-info bg-info-transparent rounded" href="{{ asset($document->file_path) }}" target="_blank" data-bs-toggle="tooltip" title="View File">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.driver-documents.destroy', $document->id) }}" method="POST" id="delete_frm_{{ $document->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger bg-danger-transparent rounded" data-bs-toggle="tooltip" title="Delete"
                                       onclick="if(confirm('Are you sure you want to delete this document?')) { document.getElementById('delete_frm_{{ $document->id }}').submit(); }">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="ti ti-file-dashed fs-3"></i></div>
                                <h6>No documents uploaded by this driver yet.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
