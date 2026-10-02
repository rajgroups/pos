@extends('layouts.admin.app')
@section('title', 'Transaction Detail — ' . $transaction->transaction_ref)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-receipt me-2 text-primary"></i>Transaction Detail</h4>
        <h6 class="text-muted">{{ $transaction->transaction_ref }}</h6>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Back to Transactions
        </a>
        @if($transaction->booking)
        <a href="{{ route('admin.ride.show', $transaction->booking->id) }}" class="btn btn-outline-primary btn-sm">
            <i class="ti ti-car me-1"></i>View Ride
        </a>
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- LEFT COLUMN --}}
    <div class="col-xl-8">

        {{-- Ride Information --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 fw-bold"><i class="ti ti-info-circle me-2"></i>Ride Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Transaction ID</label>
                        <div class="fw-bold text-primary fs-14">{{ $transaction->transaction_ref }}</div>
                        <div class="text-muted fs-12">{{ $transaction->transaction_uuid }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Booking ID</label>
                        @if($transaction->booking)
                            <div class="fw-bold fs-14">
                                <a href="{{ route('admin.ride.show', $transaction->booking->id) }}" class="text-dark">
                                    #{{ $transaction->booking->booking_no }}
                                </a>
                            </div>
                        @else
                            <div class="text-muted">—</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Date & Time</label>
                        <div class="fw-medium fs-14">{{ $transaction->created_at->format('d M Y, h:i A') }}</div>
                        @if($transaction->settled_at)
                            <div class="text-muted fs-12">Settled: {{ $transaction->settled_at->format('d M Y, h:i A') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Vehicle Category</label>
                        <div class="fw-medium fs-14">{{ $transaction->vehicleCategory?->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Passenger (User)</label>
                        @if($transaction->user)
                            <div class="fw-medium fs-14">{{ $transaction->user->name }}</div>
                            <div class="text-muted fs-12">{{ $transaction->user->mobile }}</div>
                        @else
                            <div class="text-muted">—</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Driver</label>
                        @if($transaction->driver)
                            <div class="fw-medium fs-14">{{ $transaction->driver->name }}</div>
                            <div class="text-muted fs-12">{{ $transaction->driver->phone }}</div>
                        @else
                            <div class="text-muted">—</div>
                        @endif
                    </div>
                    @if($transaction->booking?->pickupLocation)
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Pickup</label>
                        <div class="fs-13"><i class="ti ti-circle-filled text-success me-1 fs-10"></i>{{ $transaction->booking->pickupLocation->address }}</div>
                    </div>
                    @endif
                    @if($transaction->booking?->dropLocation)
                    <div class="col-md-6">
                        <label class="text-muted fs-12 fw-medium">Drop</label>
                        <div class="fs-13"><i class="ti ti-map-pin-filled text-danger me-1 fs-10"></i>{{ $transaction->booking->dropLocation->address }}</div>
                    </div>
                    @endif
                    @if($transaction->distance_km)
                    <div class="col-md-4">
                        <label class="text-muted fs-12 fw-medium">Distance</label>
                        <div class="fw-medium fs-14">{{ $transaction->distance_km }} km</div>
                    </div>
                    @endif
                    @if($transaction->duration_minutes)
                    <div class="col-md-4">
                        <label class="text-muted fs-12 fw-medium">Duration</label>
                        <div class="fw-medium fs-14">{{ $transaction->duration_minutes }} min</div>
                    </div>
                    @endif
                    @if($transaction->waiting_minutes)
                    <div class="col-md-4">
                        <label class="text-muted fs-12 fw-medium">Waiting</label>
                        <div class="fw-medium fs-14">{{ $transaction->waiting_minutes }} min</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Fare Breakdown --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 fw-bold"><i class="ti ti-calculator me-2"></i>Fare Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fs-13">Base Fare</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->base_fare, 2) }}</td>
                        </tr>
                        @if($transaction->distance_fare > 0)
                        <tr>
                            <td class="text-muted fs-13">Distance Fare</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->distance_fare, 2) }}</td>
                        </tr>
                        @endif
                        @if($transaction->time_fare > 0)
                        <tr>
                            <td class="text-muted fs-13">Time Fare</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->time_fare, 2) }}</td>
                        </tr>
                        @endif
                        @if($transaction->waiting_fare > 0)
                        <tr>
                            <td class="text-muted fs-13">Waiting Charge</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->waiting_fare, 2) }}</td>
                        </tr>
                        @endif
                        @if($transaction->extra_charges > 0)
                        <tr>
                            <td class="text-muted fs-13">Extra Charges</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->extra_charges, 2) }}</td>
                        </tr>
                        @endif
                        @if($transaction->surge_amount > 0)
                        <tr>
                            <td class="text-muted fs-13">Surge / Night Charge</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->surge_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($transaction->discount_amount > 0)
                        <tr>
                            <td class="text-muted fs-13 text-success">Discount</td>
                            <td class="text-end fw-medium text-success">−₹{{ number_format($transaction->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-light fw-bold">
                            <td class="fs-14">Subtotal (Gross Fare)</td>
                            <td class="text-end fs-14">₹{{ number_format($transaction->subtotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tax --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0 fw-bold text-dark"><i class="ti ti-receipt-tax me-2"></i>Tax</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fs-13">Tax Type</td>
                            <td class="text-end fw-medium">{{ $transaction->tax_type ?? 'GST' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fs-13">Tax Rate <span class="badge bg-warning-transparent text-warning ms-1">Snapshot at settlement</span></td>
                            <td class="text-end fw-medium">{{ $transaction->tax_rate }}%</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td class="fs-14">Tax Amount</td>
                            <td class="text-end fs-14 text-warning">₹{{ number_format($transaction->tax_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Platform Commission --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold"><i class="ti ti-percentage me-2"></i>Platform Commission</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fs-13">Commission Type <span class="badge bg-info-transparent text-info ms-1">Snapshot at settlement</span></td>
                            <td class="text-end fw-medium">{{ ucfirst($transaction->commission_type ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fs-13">Commission Rate</td>
                            <td class="text-end fw-medium">
                                @if($transaction->commission_type === 'percentage')
                                    {{ $transaction->commission_rate }}%
                                @else
                                    ₹{{ number_format($transaction->commission_rate, 2) }} (fixed)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fs-13">Applied on</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->subtotal, 2) }} (gross fare)</td>
                        </tr>
                        <tr class="table-light fw-bold">
                            <td class="fs-14">Commission Amount</td>
                            <td class="text-end fs-14 text-info">₹{{ number_format($transaction->commission_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-xl-4">

        {{-- Settlement Summary --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header" style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
                <h5 class="mb-0 fw-bold text-white"><i class="ti ti-building-bank me-2"></i>Settlement Summary</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fs-13">Gross Fare</td>
                            <td class="text-end fw-medium">₹{{ number_format($transaction->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fs-13">Tax Collected</td>
                            <td class="text-end fw-medium text-warning">₹{{ number_format($transaction->tax_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fs-13">Commission</td>
                            <td class="text-end fw-medium text-info">₹{{ number_format($transaction->commission_amount, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="fs-13 fw-bold text-success">Driver Earnings</td>
                            <td class="text-end fw-bold text-success fs-14">₹{{ number_format($transaction->driver_earning, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fs-13 fw-bold" style="color:#6f42c1;">Platform Earnings</td>
                            <td class="text-end fw-bold fs-14" style="color:#6f42c1;">₹{{ number_format($transaction->platform_earning, 2) }}</td>
                        </tr>
                        <tr class="table-dark fw-bold">
                            <td class="fs-14">Final Amount (User Pays)</td>
                            <td class="text-end fs-15">₹{{ number_format($transaction->final_amount, 2) }}</td>
                        </tr>
                        @if($transaction->paid_amount > 0)
                        <tr>
                            <td class="text-muted fs-13">Amount Paid</td>
                            <td class="text-end fw-medium text-success">₹{{ number_format($transaction->paid_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($transaction->refund_amount > 0)
                        <tr>
                            <td class="text-muted fs-13 text-danger">Refunded</td>
                            <td class="text-end fw-medium text-danger">₹{{ number_format($transaction->refund_amount, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold"><i class="ti ti-credit-card me-2 text-primary"></i>Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted fs-12 fw-medium">Payment Method</label>
                    <div class="fw-bold fs-14 mt-1">{{ ucfirst($transaction->payment_method ?? '—') }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted fs-12 fw-medium">Payment Status</label>
                    <div class="mt-1">
                        @php
                            $pBadge = match($transaction->payment_status) {
                                'paid'     => 'bg-success',
                                'pending'  => 'bg-warning text-dark',
                                'failed'   => 'bg-danger',
                                'refunded' => 'bg-info',
                                default    => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $pBadge }} fs-13 px-3 py-2">{{ ucfirst($transaction->payment_status) }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted fs-12 fw-medium">Transaction Status</label>
                    <div class="mt-1">
                        @php
                            $sBadge = match($transaction->transaction_status) {
                                'completed' => 'bg-success',
                                'pending'   => 'bg-warning text-dark',
                                'failed'    => 'bg-danger',
                                'refunded'  => 'bg-info',
                                'cancelled' => 'bg-secondary',
                                default     => 'bg-light text-muted',
                            };
                        @endphp
                        <span class="badge {{ $sBadge }} fs-13 px-3 py-2">{{ ucfirst($transaction->transaction_status) }}</span>
                    </div>
                </div>
                <div>
                    <label class="text-muted fs-12 fw-medium">Currency</label>
                    <div class="fw-bold fs-14 mt-1">{{ $transaction->currency }}</div>
                </div>
            </div>
        </div>

        {{-- Audit Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold"><i class="ti ti-clock me-2 text-muted"></i>Audit Trail</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="text-muted fs-12 fw-medium">Created At</label>
                    <div class="fs-13 mt-1">{{ $transaction->created_at->format('d M Y, h:i:s A') }}</div>
                </div>
                @if($transaction->settled_at)
                <div class="mb-2">
                    <label class="text-muted fs-12 fw-medium">Settled At</label>
                    <div class="fs-13 mt-1">{{ $transaction->settled_at->format('d M Y, h:i:s A') }}</div>
                </div>
                @endif
                <div class="mb-2">
                    <label class="text-muted fs-12 fw-medium">Transaction UUID</label>
                    <div class="fs-12 text-muted mt-1 text-break">{{ $transaction->transaction_uuid }}</div>
                </div>
                <div class="alert alert-info p-2 mt-3 mb-0">
                    <i class="ti ti-lock me-1"></i>
                    <small>This transaction is an immutable financial snapshot. Commission and tax rates reflect values at time of settlement.</small>
                </div>
            </div>
        </div>

        {{-- Related Transactions --}}
        @if($transaction->childTransactions->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold"><i class="ti ti-arrows-exchange me-2 text-muted"></i>Related Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th class="fs-12">Ref</th>
                                <th class="fs-12">Type</th>
                                <th class="fs-12">Amount</th>
                                <th class="fs-12">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->childTransactions as $child)
                            <tr>
                                <td><a href="{{ route('admin.transactions.show', $child->id) }}" class="fs-12 text-primary">{{ $child->transaction_ref }}</a></td>
                                <td><span class="badge bg-secondary fs-11">{{ $child->transaction_type }}</span></td>
                                <td class="fs-12">₹{{ number_format($child->final_amount, 2) }}</td>
                                <td><span class="badge bg-info fs-11">{{ $child->transaction_status }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
