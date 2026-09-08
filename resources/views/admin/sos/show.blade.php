@extends('layouts.admin.app')
@section('title', 'Emergency Control Room')

@section('content')
    @php($googleMapsApiKey = config('services.google_maps.api_key'))
    <!-- Top Emergency Banner -->
    <div class="card mb-4 border-0 {{ $alert->status == 'active' ? 'bg-danger text-white pulse-border' : 'bg-success text-white' }}">
        <div class="card-body d-flex justify-content-between align-items-center p-4">
            <div>
                <h3 class="fw-bold mb-1 {{ $alert->status == 'active' ? 'text-white' : 'text-white' }}">
                    <i class="ti ti-alert-triangle fs-28 me-2"></i> 
                    SOS Alert #{{ $alert->id }} ({{ ucfirst(str_replace('_', ' ', $alert->type)) }})
                </h3>
                <p class="mb-0 fs-15 {{ $alert->status == 'active' ? 'text-white-50' : 'text-white-50' }}">
                    Triggered on {{ $alert->created_at->format('l, d M Y at h:i A') }}
                    @if($alert->status == 'resolved')
                        &bull; Resolved on {{ $alert->resolved_at->format('d M Y, h:i A') }}
                    @endif
                </p>
            </div>
            
            <div>
                @if($alert->status == 'active')
                    <form action="{{ route('admin.sos.resolve', $alert->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this emergency as resolved?');">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-light text-danger fw-bold shadow-sm">
                            <i class="ti ti-shield-check me-2"></i> Mark as Resolved
                        </button>
                    </form>
                @else
                    <span class="badge bg-light text-success fs-16 px-4 py-2 shadow-sm">
                        <i class="ti ti-check me-2"></i> Emergency Resolved
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Live Location Map -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="ti ti-map-pin text-danger me-2"></i> Emergency Location</h5>
                </div>
                <div class="card-body p-0">
                    @if($alert->latitude && $alert->longitude)
                        <iframe
                        width="100%"
                        height="500"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps/embed/v1/place?key={{ $googleMapsApiKey }}&q={{ $alert->latitude }},{{ $alert->longitude }}&zoom=16">
                    </iframe>
                @else
                        <div class="d-flex flex-column align-items-center justify-content-center bg-light" style="height: 500px;">
                            <i class="ti ti-map-pin-off fs-48 text-muted mb-3"></i>
                            <h5 class="text-muted">No exact GPS coordinates provided.</h5>
                            <p class="text-muted">Relying on driver/user contact for location.</p>
                        </div>
                    @endif
                </div>
                @if($alert->message)
                <div class="card-footer bg-light">
                    <p class="mb-0 text-dark fw-medium"><strong>SOS Message:</strong> "{{ $alert->message }}"</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Sidebar Info -->
        <div class="col-xl-4 col-lg-5 mb-4">
            
            <!-- User Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ti ti-user text-primary me-2"></i> Customer Details</h5>
                </div>
                <div class="card-body">
                    @if($alert->user)
                        <div class="d-flex align-items-center mb-3 border-bottom pb-3">
                            <div class="avatar avatar-md me-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                {{ substr($alert->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $alert->user->name }}</h6>
                                <a href="tel:{{ $alert->user->mobile }}" class="text-primary fs-14"><i class="ti ti-phone me-1"></i>{{ $alert->user->mobile }}</a>
                            </div>
                        </div>
                        
                        <h6 class="fs-13 fw-bold text-muted text-uppercase mb-2">Emergency Contact</h6>
                        <div class="bg-danger-transparent p-3 rounded border border-danger-transparent">
                            @if($alert->user->emergency_contact_name || $alert->user->emergency_contact_mobile)
                                <div class="fw-bold text-dark">{{ $alert->user->emergency_contact_name ?? 'Unknown' }} <span class="badge bg-light text-dark ms-2">{{ $alert->user->emergency_contact_relation ?? 'Contact' }}</span></div>
                                <div class="mt-1">
                                    <a href="tel:{{ $alert->user->emergency_contact_mobile }}" class="fw-bold text-danger fs-18"><i class="ti ti-phone-call me-2"></i>{{ $alert->user->emergency_contact_mobile }}</a>
                                </div>
                            @else
                                <div class="text-muted"><i class="ti ti-alert-circle me-1"></i> No emergency contact saved.</div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted mb-0">Customer information unavailable.</p>
                    @endif
                </div>
            </div>

            <!-- Driver Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ti ti-steering-wheel text-success me-2"></i> Driver & Vehicle</h5>
                </div>
                <div class="card-body">
                    @if($alert->booking && $alert->booking->driver)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="avatar avatar-md me-3 bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                                {{ substr($alert->booking->driver->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $alert->booking->driver->name }}</h6>
                                <a href="tel:{{ $alert->booking->driver->phone }}" class="text-success fs-14"><i class="ti ti-phone me-1"></i>{{ $alert->booking->driver->phone }}</a>
                            </div>
                        </div>
                        @if($alert->booking->vehicle)
                            <div class="d-flex align-items-center">
                                <i class="ti ti-car fs-24 text-dark me-3"></i>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $alert->booking->vehicle->vehicle_number }}</h6>
                                    <div class="text-muted fs-13">{{ $alert->booking->vehicle->brand }} {{ $alert->booking->vehicle->model }} ({{ $alert->booking->vehicle->color }})</div>
                                </div>
                            </div>
                        @else
                            <div class="text-muted"><i class="ti ti-car-off me-1"></i> No specific vehicle assigned.</div>
                        @endif
                    @else
                        <p class="text-muted mb-0">Driver information unavailable.</p>
                    @endif
                </div>
            </div>

            <!-- Booking Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="ti ti-route text-info me-2"></i> Trip Context</h5>
                </div>
                <div class="card-body">
                    @if($alert->booking)
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="fw-bold">Booking ID:</span>
                            <span class="text-primary">#{{ $alert->booking->booking_no }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="fw-bold">Status:</span>
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $alert->booking->status)) }}</span>
                        </div>
                        
                        <div class="position-relative ps-4 mt-3">
                            <div class="position-absolute start-0 top-0 bottom-0 border-start border-2 ms-2 my-2 border-dashed"></div>
                            
                            <div class="position-relative mb-3">
                                <i class="ti ti-circle-filled text-success position-absolute bg-white" style="left: -23px; top: 2px;"></i>
                                <h6 class="fs-12 text-muted mb-1 text-uppercase">Pickup Location</h6>
                                <p class="mb-0 fs-14">{{ $alert->booking->pickupLocation?->address ?? 'N/A' }}</p>
                            </div>
                            
                            <div class="position-relative">
                                <i class="ti ti-map-pin-filled text-danger position-absolute bg-white" style="left: -24px; top: 2px;"></i>
                                <h6 class="fs-12 text-muted mb-1 text-uppercase">Drop Location</h6>
                                <p class="mb-0 fs-14">{{ $alert->booking->dropLocation?->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Trip information unavailable.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
<style>
    .pulse-border {
        animation: pulse-border 1.5s infinite;
    }
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .bg-danger-transparent {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endpush
