@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Vehicle Category Pricing</h4>
                <h6>Manage pricing rules, commission & tax for each vehicle category</h6>
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
            <a href="{{ route('admin.vehicle-pricing.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Pricing</a>
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
                            <th>Category</th>
                            <th>Search Radius</th>
                            <th>Pricing Type</th>
                            <th>Base Fare</th>
                            <th>Min Fare</th>
                            <th>Per KM</th>
                            <th>Commission</th>
                            <th>GST</th>
                            <th>Status</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pricings as $pricing)
                        <tr>
                            <td>{{ $pricing->vehicleCategory->name ?? 'N/A' }}</td>
                            <td>
                                @if($pricing->vehicleCategory && $pricing->vehicleCategory->driver_search_radius_km)
                                    <span class="badge bg-info text-white">{{ $pricing->vehicleCategory->driver_search_radius_km }} KM</span>
                                @else
                                    <span class="badge bg-secondary">5 KM</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $pricing->pricing_type }}</span></td>
                            <td>₹{{ number_format($pricing->base_fare, 2) }}</td>
                            <td>₹{{ number_format($pricing->minimum_fare, 2) }}</td>
                            <td>₹{{ number_format($pricing->per_km_rate, 2) }}</td>
                            <td>
                                @if($pricing->commission_type === 'fixed')
                                    <span class="badge bg-warning text-dark">₹{{ number_format($pricing->commission_value ?? 0, 2) }} fixed</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $pricing->commission_value ?? 0 }}%</span>
                                @endif
                            </td>
                            <td>{{ $pricing->tax_percentage ?? 0 }}%</td>
                            <td>
                                @if($pricing->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="{{route('admin.vehicle-pricing.edit',$pricing->id)}}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.vehicle-pricing.destroy', $pricing->id) }}" method="POST" id="delete_frm_{{ $pricing->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this pricing rule?')) { document.getElementById('delete_frm_{{ $pricing->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($pricings->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $pricings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
