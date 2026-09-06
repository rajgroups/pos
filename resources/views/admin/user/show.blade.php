@extends('layouts.admin.app')
@section('title', 'User Profile')

@section('content')
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body p-0">
        <div class="profile-banner position-relative" style="height: 140px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-top-left-radius: 8px; border-top-right-radius: 8px;">
        </div>
        <div class="d-flex align-items-end px-4 pb-4 position-relative" style="margin-top: -60px;">
            <div class="avatar avatar-xxl me-4 border border-4 border-white shadow-sm rounded-circle bg-white" style="width: 120px; height: 120px;">
                <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('resource/admin/assets/img/profiles/avatar-02.jpg') }}" alt="User" class="rounded-circle w-100 h-100 object-fit-cover">
            </div>
            <div class="pb-2">
                <h3 class="mb-1 text-dark fw-bold">{{ trim($user->first_name . ' ' . $user->last_name) ?: $user->name }}</h3>
                <p class="mb-0 text-muted fs-15">User ID: #{{ $user->id }} &bull; Customer</p>
            </div>
            <div class="ms-auto pb-2">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><i class="ti ti-edit me-2"></i>Edit Profile</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- User Details -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Personal Details</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-phone me-2"></i>Phone</span>
                        <span class="fw-medium text-dark">{{ $user->mobile ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-mail me-2"></i>Email</span>
                        <span class="fw-medium text-dark">{{ $user->email ?? 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-calendar me-2"></i>Date of Birth</span>
                        <span class="fw-medium text-dark">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-user me-2"></i>Gender</span>
                        <span class="fw-medium text-dark">{{ $user->gender ? ucfirst($user->gender) : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-wallet me-2"></i>Wallet Balance</span>
                        <span class="fw-medium text-success">₹{{ $user->wallet_balance ?? '0.00' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                        <span class="text-muted"><i class="ti ti-calendar-event me-2"></i>Joined Date</span>
                        <span class="fw-medium text-dark">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</span>
                    </li>
                    <li class="d-flex justify-content-between mb-0">
                        <span class="text-muted"><i class="ti ti-map-pin me-2"></i>Address</span>
                        <span class="fw-medium text-dark text-end" style="max-width: 60%;">
                            {{ $user->address ? $user->address . ',' : '' }}
                            {{ $user->city ? $user->city . ',' : '' }}
                            {{ $user->state ? $user->state . ' -' : '' }}
                            {{ $user->postal_code ?? 'N/A' }}
                            @if(!$user->address && !$user->city && !$user->state && !$user->postal_code) N/A @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Emergency Contact -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card bg-white h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Emergency Contact</h5>
            </div>
            <div class="card-body">
                @if($user->emergency_contact_name || $user->emergency_contact_mobile)
                    <div class="d-flex align-items-center mb-4 border-bottom pb-4">
                        <div class="avatar avatar-lg me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="ti ti-heart-rate-monitor fs-24 text-danger"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $user->emergency_contact_name ?? 'N/A' }}</h6>
                            <span class="badge bg-light text-dark border">{{ $user->emergency_contact_relation ?? 'Emergency Contact' }}</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center justify-content-between">
                            <span class="text-muted"><i class="ti ti-phone me-2"></i>Contact Number</span>
                            <span class="fw-bold text-danger fs-16">{{ $user->emergency_contact_mobile ?? 'N/A' }}</span>
                        </li>
                    </ul>
                @else
                    <div class="text-center text-muted py-5 mt-4">
                        <i class="ti ti-heart-broken fs-48 mb-2"></i>
                        <p class="mb-0">No Emergency Contact Added</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-white">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Recent Rides</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead class="thead-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Date & Time</th>
                                <th>Driver & Vehicle</th>
                                <th>Pickup / Drop</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td><a href="#" class="fw-bold text-primary">#{{ $booking->booking_no }}</a></td>
                                <td>{{ $booking->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if($booking->driver)
                                        <div class="fw-medium">{{ $booking->driver->name }}</div>
                                        <div class="text-muted fs-12">{{ $booking->category?->name ?? 'N/A' }}</div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $booking->pickupLocation?->address }}"><i class="ti ti-map-pin text-success me-1"></i> {{ $booking->pickupLocation?->address ?? 'N/A' }}</div>
                                    <div class="text-truncate" title="{{ $booking->dropLocation?->address }}"><i class="ti ti-map-pin text-danger me-1"></i> {{ $booking->dropLocation?->address ?? 'N/A' }}</div>
                                </td>
                                <td>₹{{ $booking->final_amount ?? $booking->estimated_amount ?? '0.00' }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        if (in_array($booking->status, ['completed'])) $badgeClass = 'bg-success';
                                        if (in_array($booking->status, ['cancelled', 'expired', 'timeout', 'no_driver_available'])) $badgeClass = 'bg-danger';
                                        if (in_array($booking->status, ['pending', 'requested'])) $badgeClass = 'bg-warning text-dark';
                                        if (in_array($booking->status, ['assigned', 'dispatched', 'arrived', 'started', 'in_progress', 'accepted'])) $badgeClass = 'bg-info';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No rides found for this user.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
