@extends('layouts.admin.app')
@section('title', 'Transaction History')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <h4 class="fw-bold"><i class="ti ti-receipt me-2 text-primary"></i>Transaction History</h4>
        <h6 class="text-muted">Financial ledger for all completed Indicab rides</h6>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.transactions.export', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="ti ti-download me-1"></i>Export CSV
        </a>
        <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-icon btn-sm" data-bs-toggle="tooltip" title="Reset Filters">
            <i class="ti ti-refresh"></i>
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-xl-2 col-sm-6 col-12 d-flex">
        <div class="card bg-primary sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-primary"><i class="ti ti-car fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1 fs-12">Total Rides</p>
                    <h4 class="text-white">{{ number_format($summary->total_rides ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-sm-6 col-12 d-flex">
        <div class="card bg-success sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-success"><i class="ti ti-currency-rupee fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1 fs-12">Gross Revenue</p>
                    <h5 class="text-white">₹{{ number_format($summary->total_gross ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-sm-6 col-12 d-flex">
        <div class="card bg-warning sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-warning"><i class="ti ti-receipt-tax fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1 fs-12">Tax Collected</p>
                    <h5 class="text-white">₹{{ number_format($summary->total_tax ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-sm-6 col-12 d-flex">
        <div class="card bg-info sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-info"><i class="ti ti-percentage fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1 fs-12">Commission</p>
                    <h5 class="text-white">₹{{ number_format($summary->total_commission ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-sm-6 col-12 d-flex">
        <div class="card bg-secondary sale-widget flex-fill">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white text-secondary"><i class="ti ti-wallet fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1 fs-12">Driver Earnings</p>
                    <h5 class="text-white">₹{{ number_format($summary->total_driver_earning ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-sm-6 col-12 d-flex">
        <div class="card sale-widget flex-fill" style="background:linear-gradient(135deg,#6f42c1,#8b5cf6);">
            <div class="card-body d-flex align-items-center">
                <span class="sale-icon bg-white" style="color:#6f42c1;"><i class="ti ti-building-bank fs-24"></i></span>
                <div class="ms-2">
                    <p class="text-white mb-1 fs-12">Platform Earnings</p>
                    <h5 class="text-white">₹{{ number_format($summary->total_platform_earning ?? 0, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label text-muted fs-13">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Txn ID, Booking, User, Driver..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-muted fs-13">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-muted fs-13">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-muted fs-13">Txn Status</label>
                <select name="transaction_status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','completed','failed','refunded','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('transaction_status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-muted fs-13">Payment Method</label>
                <select name="payment_method" class="form-select">
                    <option value="">All Methods</option>
                    @foreach(['cash','wallet','online','upi','card','other'] as $m)
                        <option value="{{ $m }}" {{ request('payment_method') == $m ? 'selected' : '' }}>{{ ucfirst($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-muted fs-13">Vehicle Category</label>
                <select name="vehicle_category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($vehicleCategories as $cat)
                        <option value="{{ $cat->id }}" {{ request('vehicle_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-muted fs-13">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="newest" {{ request('sort','newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="highest_amount" {{ request('sort') == 'highest_amount' ? 'selected' : '' }}>Highest Amount</option>
                    <option value="highest_commission" {{ request('sort') == 'highest_commission' ? 'selected' : '' }}>Highest Commission</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i>Filter</button>
                @if(request()->hasAny(['search','from_date','to_date','transaction_status','payment_method','vehicle_category_id','sort']))
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-light border w-100">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Data Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Transaction ID</th>
                            <th>Booking</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Driver</th>
                            <th>Gross Fare</th>
                            <th>Tax</th>
                            <th>Commission</th>
                            <th>Driver Earning</th>
                            <th>Final Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $txn)
                        <tr>
                            <td>
                                <a href="{{ route('admin.transactions.show', $txn->id) }}" class="fw-bold text-primary fs-13">
                                    {{ $txn->transaction_ref }}
                                </a>
                            </td>
                            <td>
                                @if($txn->booking)
                                    <a href="{{ route('admin.ride.show', $txn->booking->id) }}" class="text-dark fw-medium fs-13">
                                        #{{ $txn->booking->booking_no }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fs-13">{{ $txn->created_at->format('d M Y') }}</div>
                                <div class="text-muted fs-12">{{ $txn->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($txn->user)
                                    <div class="fw-medium fs-13">{{ $txn->user->name }}</div>
                                    <div class="text-muted fs-12">{{ $txn->user->mobile }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($txn->driver)
                                    <div class="fw-medium fs-13">{{ $txn->driver->name }}</div>
                                    <div class="text-muted fs-12">{{ $txn->driver->phone }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="fw-medium">₹{{ number_format($txn->subtotal, 2) }}</td>
                            <td>
                                <span class="text-warning">₹{{ number_format($txn->tax_amount, 2) }}</span>
                                @if($txn->tax_rate > 0)
                                    <div class="text-muted fs-11">{{ $txn->tax_rate }}%</div>
                                @endif
                            </td>
                            <td>
                                <span class="text-info">₹{{ number_format($txn->commission_amount, 2) }}</span>
                                @if($txn->commission_type)
                                    <div class="text-muted fs-11">{{ $txn->commission_type == 'percentage' ? $txn->commission_rate . '%' : '₹' . $txn->commission_rate . ' fixed' }}</div>
                                @endif
                            </td>
                            <td class="text-success fw-medium">₹{{ number_format($txn->driver_earning, 2) }}</td>
                            <td class="fw-bold text-dark">₹{{ number_format($txn->final_amount, 2) }}</td>
                            <td>
                                <div class="fw-medium fs-13">{{ ucfirst($txn->payment_method ?? '—') }}</div>
                                @php
                                    $pBadge = match($txn->payment_status) {
                                        'paid'     => 'bg-success',
                                        'pending'  => 'bg-warning text-dark',
                                        'failed'   => 'bg-danger',
                                        'refunded' => 'bg-info',
                                        default    => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $pBadge }} fs-11">{{ ucfirst($txn->payment_status) }}</span>
                            </td>
                            <td>
                                @php
                                    $sBadge = match($txn->transaction_status) {
                                        'completed' => 'bg-success',
                                        'pending'   => 'bg-warning text-dark',
                                        'failed'    => 'bg-danger',
                                        'refunded'  => 'bg-info',
                                        'cancelled' => 'bg-secondary',
                                        default     => 'bg-light text-muted',
                                    };
                                @endphp
                                <span class="badge {{ $sBadge }}">{{ ucfirst($txn->transaction_status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $txn->id) }}"
                                   class="btn btn-sm btn-light border btn-icon" data-bs-toggle="tooltip" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @else
            <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="min-height:400px;">
                <div class="empty-state-icon bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                    <i class="ti ti-receipt-off text-muted opacity-50" style="font-size:48px;"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">No Transactions Found</h4>
                <p class="text-muted mb-4" style="max-width:400px;">
                    @if(request()->hasAny(['search','from_date','to_date','transaction_status','payment_method']))
                        No transactions match your filters. Try adjusting your search criteria.
                    @else
                        Transactions will appear here automatically when rides are completed and financially settled.
                    @endif
                </p>
                @if(request()->hasAny(['search','from_date','to_date','transaction_status','payment_method']))
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-primary"><i class="ti ti-filter-off me-2"></i>Clear Filters</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
