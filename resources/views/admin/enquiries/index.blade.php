@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Driver Register Requests (Enquiries)</h4>
                <h6>Manage partner enquiries submitted from the Driver App</h6>
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
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Contact Note</th>
                            <th>Date Submitted</th>
                            <th class="no-sort text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enquiries as $enquiry)
                        <tr>
                            <td class="fw-bold">{{ $enquiry->name }}</td>
                            <td>{{ $enquiry->mobile }}</td>
                            <td>
                                @if($enquiry->contact_note)
                                    <span title="{{ $enquiry->contact_note }}">{{ Str::limit($enquiry->contact_note, 40) }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                            <td class="action-table-data text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <a href="{{ route('admin.drivers.create', ['name' => $enquiry->name, 'phone' => $enquiry->mobile]) }}" class="btn btn-sm btn-outline-primary me-2" title="Convert to Driver">
                                        <i class="ti ti-user-plus me-1"></i> Convert to Driver
                                    </a>
                                    
                                    <form action="{{ route('admin.enquiries.destroy', $enquiry->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($enquiries->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $enquiries->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
