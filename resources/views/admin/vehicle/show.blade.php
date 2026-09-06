@extends('layouts.admin.app')
@section('title', 'Vehicle Details')

@section('content')
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="profile-banner position-relative" style="height: 140px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-top-left-radius: 8px; border-top-right-radius: 8px;">
        </div>
        <div class="d-flex align-items-end px-4 pb-4 position-relative" style="margin-top: -60px;">
            <div class="avatar avatar-xxl me-4 border border-4 border-white shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                <i class="ti ti-car text-primary" style="font-size: 56px;"></i>
            </div>
            <div class="pb-2">
                <h3 class="mb-1 text-dark fw-bold">{{ $vehicle->vehicle_number }}</h3>
                <p class="mb-0 text-muted fs-15">{{ $vehicle->brand }} {{ $vehicle->model }} &bull; {{ $vehicle->vehicleType?->name ?? 'N/A' }}</p>
            </div>
            <div class="ms-auto pb-2">
                <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-primary"><i class="ti ti-edit me-2"></i>Edit Vehicle</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Driver Info Card -->
    <div class="col-xl-4 col-md-6">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Assigned Driver</h5>
            </div>
            <div class="card-body">
                @if($vehicle->driver)
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-lg me-3">
                            <img src="{{ $vehicle->driver->profile_image ? asset('storage/'.$vehicle->driver->profile_image) : asset('resource/admin/assets/img/profiles/avatar-02.jpg') }}" alt="Driver Image" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $vehicle->driver->name }}</h6>
                            <span class="badge bg-{{ $vehicle->driver->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($vehicle->driver->status) }}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3"><i class="ti ti-phone me-2 text-primary fs-18"></i>{{ $vehicle->driver->phone }}</li>
                        <li class="mb-3"><i class="ti ti-mail me-2 text-primary fs-18"></i>{{ $vehicle->driver->email ?? 'No email provided' }}</li>
                        <li class="mb-3"><i class="ti ti-id me-2 text-primary fs-18"></i>Driver ID: #{{ $vehicle->driver->id }}</li>
                    </ul>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-user-x fs-48 mb-2"></i>
                        <p class="mb-0">No Driver Assigned</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Vehicle Specs Card -->
    <div class="col-xl-4 col-md-6 mt-4 mt-md-0">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Vehicle Specifications</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-tag me-2"></i>Brand & Model</span>
                        <span class="fw-medium text-dark">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-category me-2"></i>Category</span>
                        <span class="fw-medium text-dark">{{ $vehicle->vehicleType?->name ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-color-swatch me-2"></i>Color</span>
                        <span class="fw-medium text-dark">{{ $vehicle->color }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-calendar-event me-2"></i>Manufacture Year</span>
                        <span class="fw-medium text-dark">{{ $vehicle->manufacture_year }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-users me-2"></i>Capacity</span>
                        <span class="fw-medium text-dark">{{ $vehicle->seating_capacity }} Seats</span>
                    </li>
                    <li class="d-flex justify-content-between mb-0">
                        <span class="text-muted"><i class="ti ti-shield-check me-2"></i>Status</span>
                        <span class="badge bg-{{ $vehicle->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($vehicle->status) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Live Tracking Map -->
    <div class="col-xl-4 col-md-12 mt-4 mt-xl-0">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="ti ti-map-pin me-2 text-primary"></i>Live Location</h5>
                <span id="map-status" class="badge bg-secondary">Connecting...</span>
            </div>
            <div class="card-body p-0">
                <div id="vehicle-map" style="height: 100%; min-height: 320px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Rides -->
    <div class="col-xl-8 col-md-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead class="thead-light">
                            <tr>
                                <th>Booking No</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td><a href="#" class="text-primary">#{{ $booking->booking_no }}</a></td>
                                    <td>
                                        <h6 class="mb-0">{{ $booking->user?->name ?? 'N/A' }}</h6>
                                        <span class="text-muted fs-12">{{ $booking->user?->phone }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                                    <td>{{ $booking->final_amount ? '₹'.$booking->final_amount : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No recent bookings found for this vehicle.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="col-xl-4 col-md-12 mt-4 mt-xl-0">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Documents</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @forelse($vehicle->documents as $doc)
                        <li class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="avatar avatar-md me-3 flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded"><i class="ti ti-file-text fs-20"></i></span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $doc->documentType?->name ?? 'Document' }}</h6>
                                <p class="text-muted mb-0 fs-13">Exp: {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="ms-2">
                                <span class="badge bg-{{ $doc->status == 'approved' ? 'success' : ($doc->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($doc->status) }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-muted py-4">No documents uploaded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'AIzaSyDaf37zM3dVX_Th1HwkfQXPdJmam7Epn4c') }}&callback=initMap" async defer></script>
<script>
    let map;
    let marker;
    let currentLat = {{ $vehicle->location?->latitude ?? 0 }};
    let currentLng = {{ $vehicle->location?->longitude ?? 0 }};
    let isOnline = {{ $vehicle->location?->is_online ? 'true' : 'false' }};
    
    function initMap() {
        if (currentLat === 0 && currentLng === 0) {
            currentLat = 20.5937; // Default to India center
            currentLng = 78.9629;
            document.getElementById('map-status').innerText = 'No Location Data';
            document.getElementById('map-status').className = 'badge bg-warning';
        } else {
            updateStatusBadge(isOnline);
        }

        const vehiclePos = { lat: parseFloat(currentLat), lng: parseFloat(currentLng) };
        
        map = new google.maps.Map(document.getElementById("vehicle-map"), {
            zoom: 15,
            center: vehiclePos,
            mapTypeId: 'roadmap',
            disableDefaultUI: true,
            zoomControl: true,
            styles: [
                { featureType: "poi", elementType: "labels", stylers: [{ visibility: "off" }] }
            ]
        });

        marker = new google.maps.Marker({
            position: vehiclePos,
            map: map,
            title: "{{ $vehicle->vehicle_number }}",
            icon: {
                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                scale: 6,
                fillColor: "#007bff",
                fillOpacity: 1,
                strokeWeight: 2,
                strokeColor: "#ffffff",
                rotation: {{ $vehicle->location?->heading ?? 0 }}
            }
        });

        startTrackingFallback();
    }

    function updateStatusBadge(onlineStatus) {
        const badge = document.getElementById('map-status');
        if (onlineStatus) {
            badge.innerText = 'Live (Online)';
            badge.className = 'badge bg-success';
        } else {
            badge.innerText = 'Last Known (Offline)';
            badge.className = 'badge bg-secondary';
        }
    }

    function startTrackingFallback() {
        setInterval(() => {
            fetch(`{{ route('admin.vehicles.location', $vehicle->id) }}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.latitude && data.longitude) {
                        const newPos = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };
                        marker.setPosition(newPos);
                        
                        if (data.heading !== null) {
                            let icon = marker.getIcon();
                            icon.rotation = parseFloat(data.heading);
                            marker.setIcon(icon);
                        }

                        updateStatusBadge(data.is_online);
                    }
                })
                .catch(err => console.error("Error fetching location", err));
        }, 5000);
    }
</script>
@endpush
