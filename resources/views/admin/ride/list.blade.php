@extends('layouts.admin.app')
@section('title', $type . ' Rides')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h4 class="fw-bold">{{ $type }} Rides</h4>
            <h6 class="text-muted">Manage and monitor all {{ strtolower($type) }} bookings</h6>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-icon" data-bs-toggle="tooltip" title="Reset Filters"><i class="ti ti-refresh"></i></a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ request()->url() }}" method="GET" class="row align-items-end g-3">
                <div class="col-lg-{{ $type == 'All' ? '4' : '5' }} col-md-6">
                    <label class="form-label text-muted fs-13">Search Booking</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Booking No, Customer Name or Phone..." value="{{ request('search') }}">
                    </div>
                </div>
                
                @if($type == 'All')
                <div class="col-lg-3 col-md-6">
                    <label class="form-label text-muted fs-13">Filter by Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['pending', 'requested', 'assigned', 'accepted', 'arrived', 'started', 'in_progress', 'completed', 'cancelled', 'expired', 'timeout'] as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="col-lg-{{ $type == 'All' ? '2' : '3' }} col-md-4">
                    <label class="form-label text-muted fs-13">Filter by Date</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar text-muted"></i></span>
                        <input type="date" name="date" class="form-control border-start-0 ps-0" value="{{ request('date') }}">
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-2"></i>Apply Filter</button>
                    @if(request()->hasAny(['search', 'date', 'status']))
                        <a href="{{ request()->url() }}" class="btn btn-light border w-100">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($rides->count() > 0)
                <div class="table-responsive">
                    <table class="table datatable mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer</th>
                                <th>Driver Assigned</th>
                                <th>Route</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rides as $ride)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.ride.show', $ride->id) }}" class="fw-bold text-primary">#{{ $ride->booking_no }}</a>
                                    </td>
                                    <td>
                                        @if($ride->user)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold">
                                                    {{ substr($ride->user->name, 0, 1) }}
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium text-dark">{{ $ride->user->name }}</span>
                                                    <span class="text-muted fs-12">{{ $ride->user->mobile }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ride->driver)
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium text-dark">{{ $ride->driver->name }}</span>
                                                <span class="text-muted fs-12">{{ $ride->driver->phone }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border">Unassigned</span>
                                        @endif
                                    </td>
                                    <td style="max-width: 200px;">
                                        <div class="text-truncate fs-13 mb-1" title="{{ $ride->pickupLocation?->address }}">
                                            <i class="ti ti-circle-filled text-success me-1 fs-10"></i> {{ $ride->pickupLocation?->address ?? 'N/A' }}
                                        </div>
                                        <div class="text-truncate fs-13" title="{{ $ride->dropLocation?->address }}">
                                            <i class="ti ti-map-pin-filled text-danger me-1 fs-10"></i> {{ $ride->dropLocation?->address ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">₹{{ $ride->final_amount ?? $ride->estimated_amount ?? '0.00' }}</span>
                                        <div class="fs-12 text-muted">{{ strtoupper($ride->payment_method ?? 'CASH') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            if(in_array($ride->status, ['completed'])) $badgeClass = 'bg-success';
                                            if(in_array($ride->status, ['cancelled', 'expired', 'timeout'])) $badgeClass = 'bg-danger';
                                            if(in_array($ride->status, ['pending', 'requested'])) $badgeClass = 'bg-warning text-dark';
                                            if(in_array($ride->status, ['accepted', 'arrived', 'started'])) $badgeClass = 'bg-info text-white';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-2 py-1">{{ ucfirst(str_replace('_', ' ', $ride->status)) }}</span>
                                    </td>
                                    <td>
                                        <div class="fs-14">{{ $ride->created_at->format('d M Y') }}</div>
                                        <div class="text-muted fs-12">{{ $ride->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.ride.show', $ride->id) }}" class="btn btn-sm btn-light border btn-icon" data-bs-toggle="tooltip" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $rides->withQueryString()->links() }}
                </div>
            @else
                <!-- Empty State Design -->
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="min-height: 400px;">
                    <div class="empty-state-icon bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                        <i class="ti ti-car-off text-muted opacity-50" style="font-size: 48px;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">No {{ $type }} Rides Found</h4>
                    <p class="text-muted mb-4" style="max-width: 400px;">
                        @php $rideTypeLabel = $type == 'All' ? '' : strtolower($type) . ' '; @endphp
                        @if(request()->hasAny(['search', 'date', 'status']))
                            We couldn't find any {{ $rideTypeLabel }}rides matching your filter criteria. Try adjusting your search or clearing the filters.
                        @else
                            There are currently no {{ $rideTypeLabel }}rides in the system. When new rides are booked, they will appear here.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'date', 'status']))
                        <a href="{{ request()->url() }}" class="btn btn-primary"><i class="ti ti-filter-off me-2"></i>Clear Filters</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
