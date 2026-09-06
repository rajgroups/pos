@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Wallet Recharge Requests</h4>
                <h6>Approve or reject driver wallet recharge requests</h6>
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
        @if (session()->has('error'))
            <div class="alert alert-solid-danger rounded-pill alert-dismissible fade show m-3">
                {{ session()->get('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        @endif
        @if (session()->has('info'))
            <div class="alert alert-solid-info rounded-pill alert-dismissible fade show m-3">
                {{ session()->get('info') }}
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
                            <th>Driver</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Transaction ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                        <tr>
                            <td>
                                @if($request->driver)
                                    <a href="{{ route('admin.drivers.edit', $request->driver->id) }}">{{ $request->driver->name }}</a>
                                    <br><small class="text-muted">{{ $request->driver->phone }}</small>
                                @else
                                    <span class="text-danger">Driver Not Found</span>
                                @endif
                            </td>
                            <td class="fw-bold">₹{{ number_format($request->amount, 2) }}</td>
                            <td>{{ strtoupper($request->payment_method ?? 'N/A') }}</td>
                            <td>{{ $request->transaction_id ?? 'N/A' }}</td>
                            <td>
                                @if($request->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($request->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $request->created_at->format('d M Y, h:i A') }}</td>
                            <td class="action-table-data">
                                @if($request->status === 'pending')
                                    <div class="d-flex align-items-center">
                                        <form action="{{ route('admin.recharge-requests.approve', $request->id) }}" method="POST" class="me-2" onsubmit="return confirm('Approve this recharge and add ₹{{ $request->amount }} to the driver\'s wallet?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"><i class="ti ti-check me-1"></i> Approve</button>
                                        </form>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                            <i class="ti ti-x me-1"></i> Reject
                                        </button>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Recharge Request</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.recharge-requests.reject', $request->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="form-label">Reason for rejection (Admin Remarks)</label>
                                                            <textarea name="admin_remarks" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject Request</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted"><i class="ti ti-check-all"></i> Handled</span>
                                    @if($request->admin_remarks)
                                        <br><small class="text-muted" title="{{ $request->admin_remarks }}">Note: {{ Str::limit($request->admin_remarks, 20) }}</small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($requests->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
