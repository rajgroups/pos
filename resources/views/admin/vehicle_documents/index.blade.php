@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Vehicle Documents</h4>
                <h6>Manage uploaded documents for vehicles</h6>
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
            <a href="{{ route('admin.vehicle-documents.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Upload Document</a>
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
                            <th>Vehicle Number</th>
                            <th>Brand & Model</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Uploaded Documents</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $vehicle)
                        <tr>
                            <td>
                                <span class="fw-bold text-uppercase">{{ $vehicle->vehicle_number ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                            <td>{{ $vehicle->driver->name ?? 'Unassigned' }}</td>
                            <td>
                                @if($vehicle->status === 'active')
                                    <span class="badge bg-success-transparent text-success">Active</span>
                                @elseif($vehicle->status === 'maintenance')
                                    <span class="badge bg-warning-transparent text-warning">Maintenance</span>
                                @else
                                    <span class="badge bg-danger-transparent text-danger">{{ ucfirst($vehicle->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary rounded-pill px-3">{{ $vehicle->documents_count }} Document(s)</span>
                            </td>
                            <td class="action-table-data">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.vehicle-documents.show', $vehicle->id) }}">
                                    <i class="ti ti-eye me-1"></i> View Documents
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No vehicles with uploaded documents found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($vehicles->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $vehicles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
