@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Drivers</h4>
                <h6>Manage driver profiles</h6>
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
            <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Driver</a>
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
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                        <tr>
                            <td>{{ $driver->name }}</td>
                            <td>{{ $driver->phone }}</td>
                            <td>{{ $driver->email ?? 'N/A' }}</td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $driver->driver_type }}</span></td>
                            <td>
                                @if($driver->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($driver->status === 'inactive')
                                    <span class="badge bg-danger">Inactive</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($driver->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($driver->is_verified)
                                    <span class="badge bg-success"><i class="ti ti-check"></i> Verified</span>
                                @else
                                    <span class="badge bg-light text-dark"><i class="ti ti-x"></i> Unverified</span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="{{route('admin.drivers.edit',$driver->id)}}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.drivers.destroy', $driver->id) }}" method="POST" id="delete_frm_{{ $driver->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this driver?')) { document.getElementById('delete_frm_{{ $driver->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
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
