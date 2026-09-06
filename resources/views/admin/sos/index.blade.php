@extends('layouts.admin.app')
@section('title', 'SOS Emergencies')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h4 class="fw-bold text-danger"><i class="ti ti-alert-triangle me-2"></i>Emergency (SOS) Alerts</h4>
            <h6 class="text-muted">Monitor and respond to critical SOS alerts triggered by customers and drivers</h6>
        </div>
        <div class="page-btn">
            <a href="{{ route('admin.sos.index', ['status' => 'active']) }}" class="btn btn-outline-danger me-2">
                <i class="ti ti-filter me-1"></i> Active Only
            </a>
            <a href="{{ route('admin.sos.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-list me-1"></i> All Alerts
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-solid-success alert-dismissible fade show mb-4">
            {{ session()->get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table datatable mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Alert ID</th>
                            <th>Triggered By (User)</th>
                            <th>Driver Assigned</th>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                            <tr class="{{ $alert->status == 'active' ? 'bg-danger-transparent border-start border-danger border-4' : '' }}">
                                <td class="fw-bold">#{{ $alert->id }}</td>
                                <td>
                                    @if($alert->user)
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark">{{ $alert->user->name }}</span>
                                            <span class="text-muted fs-12">{{ $alert->user->mobile }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($alert->booking && $alert->booking->driver)
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark">{{ $alert->booking->driver->name }}</span>
                                            <span class="text-muted fs-12">{{ $alert->booking->driver->phone }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeClass = 'bg-secondary';
                                        if($alert->type == 'police') $typeClass = 'bg-primary';
                                        if($alert->type == 'ambulance') $typeClass = 'bg-danger';
                                        if($alert->type == 'emergency_contact') $typeClass = 'bg-warning text-dark';
                                        if($alert->type == 'safety_team') $typeClass = 'bg-info';
                                    @endphp
                                    <span class="badge {{ $typeClass }} px-2 py-1">{{ ucfirst(str_replace('_', ' ', $alert->type)) }}</span>
                                </td>
                                <td>
                                    <div class="fs-14">{{ $alert->created_at->format('d M Y') }}</div>
                                    <div class="text-muted fs-12">{{ $alert->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    @if($alert->status == 'active')
                                        <span class="badge bg-danger pulse-danger"><i class="ti ti-point-filled"></i> Active</span>
                                    @else
                                        <span class="badge bg-success"><i class="ti ti-check"></i> Resolved</span>
                                        <div class="fs-10 text-muted mt-1">{{ $alert->resolved_at ? $alert->resolved_at->format('d M, h:i A') : '' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.sos.show', $alert->id) }}" class="btn btn-sm btn-{{ $alert->status == 'active' ? 'danger' : 'primary' }}">
                                        View Emergency
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ti ti-shield-check fs-48 d-block mb-2 text-success"></i>
                                    No SOS alerts found. Everything is secure.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 px-4 pb-4">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .pulse-danger {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .bg-danger-transparent {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }
</style>
@endpush
