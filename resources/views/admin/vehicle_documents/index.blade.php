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
                            <th>Vehicle</th>
                            <th>Document Type</th>
                            <th>Document Number</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                        <tr>
                            <td>{{ $document->vehicle->vehicle_number ?? 'N/A' }}</td>
                            <td>{{ $document->documentType->name ?? 'N/A' }}</td>
                            <td>{{ $document->document_number ?? 'N/A' }}</td>
                            <td>
                                @if($document->expiry_date)
                                    {{ $document->expiry_date->format('d M Y') }}
                                    @if($document->expiry_date->isPast())
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($document->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($document->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="{{route('admin.vehicle-documents.edit',$document->id)}}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    @if($document->file_path)
                                        <a class="me-2 p-2 text-primary" href="{{ asset($document->file_path) }}" target="_blank" title="View Document">
                                            <i data-feather="eye" class="feather-eye"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.vehicle-documents.destroy', $document->id) }}" method="POST" id="delete_frm_{{ $document->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this document?')) { document.getElementById('delete_frm_{{ $document->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($documents->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
