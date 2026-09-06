@extends('layouts.admin.app')
@section('title', 'Ride Details')

@section('content')

@php
    // Determine header theme based on status
    $headerBg = 'bg-primary text-white';
    if(in_array($booking->status, ['completed'])) $headerBg = 'bg-success text-white';
    if(in_array($booking->status, ['cancelled', 'expired', 'timeout', 'no_driver_available'])) $headerBg = 'bg-danger text-white';
    if(in_array($booking->status, ['pending', 'requested'])) $headerBg = 'bg-warning';
    
    // Determine Map coordinates
    $hasRoute = $booking->pickupLocation && $booking->pickupLocation->latitude && $booking->dropLocation && $booking->dropLocation->latitude;
@endphp

<!-- Header Banner -->
<div class="card mb-4 border-0 {{ $headerBg }} shadow-sm">
    <div class="card-body d-flex justify-content-between align-items-center p-4">
        <div>
            <h3 class="fw-bold mb-1 {{ in_array($booking->status, ['pending', 'requested']) ? 'text-dark' : 'text-white' }}">
                <i class="ti ti-steering-wheel fs-28 me-2"></i> 
                Booking #{{ $booking->booking_no }}
            </h3>
            <p class="mb-0 fs-15 {{ in_array($booking->status, ['pending', 'requested']) ? 'text-dark-50' : 'text-white-50' }}">
                Created on {{ $booking->created_at->format('l, d M Y at h:i A') }}
            </p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            @if(in_array($booking->status, ['pending', 'requested', 'scheduled']))
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                    <i class="ti ti-user-plus me-1"></i> Assign Driver
                </button>
            @endif

            @if(in_array($booking->status, ['assigned', 'accepted', 'arrived', 'started']))
                <form action="{{ route('admin.ride.action.complete', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to forcefully mark this ride as completed? This will finalize the estimated amount.');">
                    @csrf
                    <button type="submit" class="btn btn-success shadow-sm">
                        <i class="ti ti-check me-1"></i> Complete Ride
                    </button>
                </form>
            @endif

            @if(!in_array($booking->status, ['completed', 'cancelled', 'expired', 'timeout', 'no_driver_available']))
                <form action="{{ route('admin.ride.action.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this ride? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-danger shadow-sm">
                        <i class="ti ti-x me-1"></i> Cancel Ride
                    </button>
                </form>
            @endif

            <span class="badge bg-white fs-16 px-4 py-2 shadow-sm ms-2 text-dark">
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Map and Timeline -->
    <div class="col-xl-8 col-lg-7 mb-4">
        
        <!-- Live Route Map -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold"><i class="ti ti-map text-primary me-2"></i> Trip Route</h5>
                <span class="badge bg-light text-dark border">{{ $booking->category?->name ?? 'Standard' }}</span>
            </div>
            <div class="card-body p-0">
                @if($hasRoute)
                    <iframe
                        width="100%"
                        height="400"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps/embed/v1/directions?key=AIzaSyDaf37zM3dVX_Th1HwkfQXPdJmam7Epn4c&origin={{ $booking->pickupLocation->latitude }},{{ $booking->pickupLocation->longitude }}&destination={{ $booking->dropLocation->latitude }},{{ $booking->dropLocation->longitude }}">
                    </iframe>
                @elseif($booking->pickupLocation && $booking->pickupLocation->latitude)
                    <iframe
                        width="100%"
                        height="400"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDaf37zM3dVX_Th1HwkfQXPdJmam7Epn4c&q={{ $booking->pickupLocation->latitude }},{{ $booking->pickupLocation->longitude }}&zoom=15">
                    </iframe>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center bg-light" style="height: 400px;">
                        <i class="ti ti-map-off fs-48 text-muted mb-3"></i>
                        <h5 class="text-muted">Map not available</h5>
                        <p class="text-muted">No valid GPS coordinates provided for this booking.</p>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white pt-4 pb-3">
                <div class="position-relative ps-4">
                    <div class="position-absolute start-0 top-0 bottom-0 border-start border-2 ms-2 my-2 border-dashed border-primary"></div>
                    
                    <div class="position-relative mb-4">
                        <i class="ti ti-circle-filled text-success position-absolute bg-white" style="left: -23px; top: 2px;"></i>
                        <h6 class="fs-12 text-muted mb-1 text-uppercase fw-bold">Pickup Location</h6>
                        <p class="mb-0 fs-15 fw-medium text-dark">{{ $booking->pickupLocation?->address ?? 'Not Specified' }}</p>
                    </div>
                    
                    <div class="position-relative">
                        <i class="ti ti-map-pin-filled text-danger position-absolute bg-white" style="left: -24px; top: 2px;"></i>
                        <h6 class="fs-12 text-muted mb-1 text-uppercase fw-bold">Drop Location</h6>
                        <p class="mb-0 fs-15 fw-medium text-dark">{{ $booking->dropLocation?->address ?? 'Not Specified' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trip Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="ti ti-history text-info me-2"></i> Trip Timeline</h5>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    <ul class="list-unstyled mb-0 ms-3">
                        <li class="position-relative pb-4 ms-3 border-start border-2 border-light">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-primary text-white rounded-circle"><i class="ti ti-calendar fs-12"></i></span>
                            <div class="ms-3">
                                <h6 class="mb-1 fw-bold">Booking Created</h6>
                                <p class="text-muted fs-13 mb-0">{{ $booking->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                        
                        @if($booking->accepted_at)
                        <li class="position-relative pb-4 ms-3 border-start border-2 border-light">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-info text-white rounded-circle"><i class="ti ti-thumb-up fs-12"></i></span>
                            <div class="ms-3">
                                <h6 class="mb-1 fw-bold">Driver Accepted</h6>
                                <p class="text-muted fs-13 mb-0">{{ $booking->accepted_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                        @endif
                        
                        @if($booking->arrived_at)
                        <li class="position-relative pb-4 ms-3 border-start border-2 border-light">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-warning text-white rounded-circle"><i class="ti ti-map-pin fs-12"></i></span>
                            <div class="ms-3">
                                <h6 class="mb-1 fw-bold">Driver Arrived</h6>
                                <p class="text-muted fs-13 mb-0">{{ $booking->arrived_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                        @endif
                        
                        @if($booking->started_at)
                        <li class="position-relative pb-4 ms-3 border-start border-2 border-light">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-secondary text-white rounded-circle"><i class="ti ti-steering-wheel fs-12"></i></span>
                            <div class="ms-3">
                                <h6 class="mb-1 fw-bold">Trip Started</h6>
                                <p class="text-muted fs-13 mb-0">{{ $booking->started_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                        @endif

                        @if($booking->completed_at)
                        <li class="position-relative ms-3">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-success text-white rounded-circle"><i class="ti ti-check fs-12"></i></span>
                            <div class="ms-3">
                                <h6 class="mb-1 fw-bold text-success">Trip Completed</h6>
                                <p class="text-muted fs-13 mb-0">{{ $booking->completed_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                        @endif

                        @if($booking->cancelled_at)
                        <li class="position-relative ms-3">
                            <span class="position-absolute top-0 start-0 translate-middle p-2 bg-danger text-white rounded-circle"><i class="ti ti-x fs-12"></i></span>
                            <div class="ms-3">
                                <h6 class="mb-1 fw-bold text-danger">Trip Cancelled</h6>
                                <p class="text-muted fs-13 mb-0">{{ $booking->cancelled_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Info -->
    <div class="col-xl-4 col-lg-5 mb-4">
        
        <!-- Fare Breakdown Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h5 class="fw-bold mb-0"><i class="ti ti-receipt text-primary me-2"></i>Fare Calculation</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Estimated Fare</span>
                    <span class="fw-bold">₹{{ $booking->estimated_amount ?? '0.00' }}</span>
                </div>
                
                    @if($booking->fare && $booking->status == 'completed')
                        <div class="border-top pt-3 mb-3">
                            <h6 class="fs-13 fw-bold text-muted text-uppercase mb-3">Calculation Breakdown</h6>
                            
                            <!-- Base Fare -->
                            <div class="d-flex justify-content-between align-items-center mb-2 fs-14">
                                <div>
                                    <span class="text-dark">Base Fare</span>
                                    <div class="text-muted fs-12">
                                        Includes {{ $booking->category->pricing->base_distance ?? 0 }} km & {{ $booking->category->pricing->base_time ?? 0 }} mins
                                    </div>
                                </div>
                                <span>₹{{ $booking->fare->base_fare ?? '0.00' }}</span>
                            </div>

                            @if(($booking->usage->total_distance ?? 0) > 0 || ($booking->usage->total_time ?? 0) > 0)
                                <!-- Distance Charge -->
                                @php
                                    $totalDistance = $booking->usage->total_distance ?? 0;
                                    $baseDistance = $booking->category->pricing->base_distance ?? 0;
                                    $extraDistance = max(0, $totalDistance - $baseDistance);
                                    $perKmRate = $booking->category->pricing->price_per_km ?? 0;
                                    $distanceCharge = $extraDistance * $perKmRate;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2 fs-14">
                                    <div>
                                        <span class="text-dark">Distance Charge</span>
                                        <div class="text-muted fs-12">
                                            {{ $extraDistance }} extra km × ₹{{ $perKmRate }}/km
                                        </div>
                                    </div>
                                    <span>₹{{ number_format($distanceCharge, 2) }}</span>
                                </div>

                                <!-- Time Charge -->
                                @php
                                    $totalTime = $booking->usage->total_time ?? 0;
                                    $baseTime = $booking->category->pricing->base_time ?? 0;
                                    $extraTime = max(0, $totalTime - $baseTime);
                                    $perMinRate = $booking->category->pricing->price_per_minute ?? 0;
                                    $timeCharge = $extraTime * $perMinRate;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2 fs-14">
                                    <div>
                                        <span class="text-dark">Time Charge</span>
                                        <div class="text-muted fs-12">
                                            {{ $extraTime }} extra mins × ₹{{ $perMinRate }}/min
                                        </div>
                                    </div>
                                    <span>₹{{ number_format($timeCharge, 2) }}</span>
                                </div>
                            @else
                                <div class="alert alert-warning border-warning-transparent bg-warning-transparent p-2 text-dark fs-12 mt-2 mb-3">
                                    <i class="ti ti-alert-circle me-1"></i> Actual GPS distance/time tracking data is missing (likely due to manual dispatch/completion). The final fare relies on the estimated formula.
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 fs-14">
                                    <span class="text-dark">Estimated Usage Charge</span>
                                    <span>₹{{ number_format(max(0, ($booking->estimated_amount ?? 0) - ($booking->fare->base_fare ?? 0)), 2) }}</span>
                                </div>
                            @endif
                            
                            <!-- Extra Charges / Taxes -->
                            @if(($booking->fare->extra_charge ?? 0) > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2 fs-14">
                                <span class="text-dark">Extra Charges & Taxes</span>
                                <span>₹{{ $booking->fare->extra_charge ?? '0.00' }}</span>
                            </div>
                            @endif

                            @if(($booking->fare->discount ?? 0) > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2 fs-14 text-success">
                                <span class="fw-medium">Discount Applied</span>
                                <span>-₹{{ $booking->fare->discount }}</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-light border text-muted fs-13 mt-3">
                            <i class="ti ti-info-circle me-1"></i> Detailed calculation breakdown will be available once the ride is officially completed by the driver.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <span class="fs-16 fw-bold text-dark">Final Fare</span>
                        <div class="text-end">
                            <span class="fs-20 fw-bold text-success d-block mb-1">₹{{ ($booking->final_amount > 0) ? number_format($booking->final_amount, 2) : number_format($booking->estimated_amount, 2) }}</span>
                            @if($booking->payment_status == 'paid')
                                <span class="badge bg-success-transparent text-success border border-success-transparent">Paid</span>
                            @else
                                <span class="badge bg-warning-transparent text-warning border border-warning-transparent">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <!-- SOS Alerts -->
        @if($booking->sosAlerts->count() > 0)
        <div class="card border-0 shadow-sm border-danger border-2">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="card-title mb-0 fw-bold text-white"><i class="ti ti-alert-triangle me-2"></i> SOS Alerts Triggered</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($booking->sosAlerts as $alert)
                    <li class="list-group-item p-3 {{ $alert->status == 'active' ? 'bg-danger-transparent' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">#{{ $alert->id }} - {{ ucfirst(str_replace('_', ' ', $alert->type)) }}</span>
                            @if($alert->status == 'active')
                                <span class="badge bg-danger pulse-danger">Active</span>
                            @else
                                <span class="badge bg-success">Resolved</span>
                            @endif
                        </div>
                        <div class="text-muted fs-12 mb-2">{{ $alert->created_at->format('d M, h:i A') }}</div>
                        <a href="{{ route('admin.sos.show', $alert->id) }}" class="btn btn-sm btn-outline-danger w-100">View Emergency</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

    </div>
</div>

<!-- Full Width Customer & Driver Cards Row -->
<div class="row">
    <!-- Customer Details -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="ti ti-user text-primary me-2"></i> Customer</h5>
                @if($booking->user)
                    <a href="{{ route('admin.users.show', $booking->user->id) }}" class="btn btn-sm btn-outline-primary">View Profile</a>
                @endif
            </div>
            <div class="card-body">
                @if($booking->user)
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl me-4">
                            <img src="{{ $booking->user->profile_image ? asset('storage/'.$booking->user->profile_image) : asset('resource/admin/assets/img/profiles/avatar-02.jpg') }}" class="rounded-circle object-fit-cover w-100 h-100" alt="User">
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $booking->user->name }}</h5>
                            <div class="text-muted fs-15 mb-1"><i class="ti ti-phone me-1"></i>{{ $booking->user->mobile }}</div>
                            <div class="text-muted fs-14"><i class="ti ti-star-filled text-warning me-1"></i>{{ $booking->user->rating ?? '4.8' }}</div>
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="avatar avatar-xl bg-light text-muted mb-3 d-flex align-items-center justify-content-center rounded-circle">
                            <i class="ti ti-user-x fs-24"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Unavailable</h6>
                        <p class="text-muted fs-13 text-center mb-0">Customer details not found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Driver Details -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="ti ti-steering-wheel text-info me-2"></i> Driver</h5>
                @if($booking->driver)
                    <a href="{{ route('admin.drivers.show', $booking->driver->id) }}" class="btn btn-sm btn-outline-info">View Profile</a>
                @endif
            </div>
            <div class="card-body">
                @if($booking->driver)
                    <div class="row align-items-center">
                        <div class="col-sm-7 d-flex align-items-center mb-3 mb-sm-0">
                            <div class="avatar avatar-xl me-4">
                                <img src="{{ $booking->driver->profile_image ? asset('storage/'.$booking->driver->profile_image) : asset('resource/admin/assets/img/profiles/avatar-03.jpg') }}" class="rounded-circle object-fit-cover w-100 h-100" alt="Driver">
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold">{{ $booking->driver->name }}</h5>
                                <div class="text-muted fs-15 mb-1"><i class="ti ti-phone me-1"></i>{{ $booking->driver->phone }}</div>
                                <div class="text-muted fs-14"><i class="ti ti-star-filled text-warning me-1"></i>{{ $booking->driver->rating ?? '4.9' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-5 border-start-sm ps-sm-4">
                            @if($booking->vehicle)
                                <span class="text-muted fs-13 d-block mb-2">Assigned Vehicle</span>
                                <span class="badge bg-dark fs-14 mb-2 px-3 py-2">{{ $booking->vehicle->plate_number }}</span>
                                <div class="fw-medium text-dark fs-14">{{ $booking->vehicle->make }} {{ $booking->vehicle->model }}</div>
                                <div class="text-muted fs-13">{{ $booking->vehicle->color }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="avatar avatar-xl bg-light text-muted mb-3 d-flex align-items-center justify-content-center rounded-circle">
                            <i class="ti ti-steering-wheel fs-24"></i>
                        </div>
                        <h6 class="fw-bold mb-1">Unassigned</h6>
                        <p class="text-muted fs-13 text-center mb-0">No driver has been assigned to this trip yet.</p>
                        @if(in_array($booking->status, ['pending', 'requested', 'scheduled']))
                            <button type="button" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                                Assign Driver Now
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Driver Modal -->
<div class="modal fade" id="assignDriverModal" tabindex="-1" aria-labelledby="assignDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold" id="assignDriverModalLabel"><i class="ti ti-user-plus text-primary me-2"></i>Assign Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.ride.action.assign', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="driver_id" class="form-label fw-medium text-muted">Select Online Driver</label>
                        <select class="form-select select2" id="driver_id" name="driver_id" required>
                            <option value="" disabled selected>-- Select a Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">
                                    {{ $driver->name }} ({{ $driver->phone }}) 
                                    - {{ $driver->calculated_distance ? number_format($driver->calculated_distance, 1) . ' km away' : 'Distance Unknown' }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Only active and online drivers are shown.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .border-dashed {
        border-style: dashed !important;
    }
    .bg-danger-transparent {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .pulse-danger {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>
@endpush

@push('scripts')
<script type="48a874d177e4539849dc73e2-text/javascript">
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({
                dropdownParent: $('#assignDriverModal'),
                width: '100%',
                placeholder: "-- Select a Driver --"
            });
        }
    });
</script>
@endpush
