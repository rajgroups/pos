@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Vehicles</h4>
                <h6>Manage fleet vehicles</h6>
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
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Vehicle</a>
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
                            <th>Registration No.</th>
                            <th>Brand / Model</th>
                            <th>Color</th>
                            <th>Year</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $vehicle)
                        <tr>
                            <td class="fw-bold">{{ $vehicle->vehicle_number }}</td>
                            <td>{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                            <td>{{ $vehicle->color }}</td>
                            <td>{{ $vehicle->manufacture_year }}</td>
                            <td>{{ $vehicle->vehicleType->name ?? 'N/A' }}</td>
                            <td>
                                @if($vehicle->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($vehicle->status === 'inactive')
                                    <span class="badge bg-danger">Inactive</span>
                                @elseif($vehicle->status === 'maintenance')
                                    <span class="badge bg-warning">Maintenance</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($vehicle->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($vehicle->is_verified)
                                    <span class="badge bg-success"><i class="ti ti-check"></i></span>
                                @else
                                    <span class="badge bg-light text-dark"><i class="ti ti-x"></i></span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="{{route('admin.vehicles.show', $vehicle->id)}}" title="View Profile">
                                        <i data-feather="eye" class="feather-eye"></i>
                                    </a>
                                    <a class="me-2 p-2" href="{{route('admin.vehicles.edit',$vehicle->id)}}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" id="delete_frm_{{ $vehicle->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this vehicle?')) { document.getElementById('delete_frm_{{ $vehicle->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
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
