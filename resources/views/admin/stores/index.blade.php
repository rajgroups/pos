@extends('layouts.admin.app')

@push('meta')
<title>Store List - Lion POS</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">
        <h4 class="fw-bold">Store List</h4>
        <h6 class="text-muted">Manage all stores in your multistore system</h6>
    </div>

    <div class="page-btn">
        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Add Store
        </a>
    </div>
</div>

<div class="card">

    {{-- SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-solid-success rounded-pill alert-dismissible fade show m-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- ERROR --}}
    @if (session('error'))
        <div class="alert alert-solid-danger rounded-pill alert-dismissible fade show m-3">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">

        <!-- Search -->
        <div class="search-set">
            <div class="search-input">
                <span class="btn-searchset"><i class="ti ti-search fs-14"></i></span>
            </div>
        </div>

        <!-- Filter Dropdown -->
        <div class="table-dropdown">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-white btn-md" data-bs-toggle="dropdown">
                    Filter
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-3">
                    <li><a class="dropdown-item rounded-1" href="?status=1">Active</a></li>
                    <li><a class="dropdown-item rounded-1" href="?status=0">Inactive</a></li>
                    <li><a class="dropdown-item rounded-1" href="?trashed=1">Trash</a></li>
                </ul>
            </div>
        </div>

    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table datatable">
                <thead class="thead-light">
                    <tr>
                        <th class="no-sort">
                            <label class="checkboxs">
                                <input type="checkbox" id="select-all">
                                <span class="checkmarks"></span>
                            </label>
                        </th>

                        <th>Name</th>
                        <th>Owner</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Control</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($stores as $store)
                        <tr>

                            <!-- Checkbox -->
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>

                            <!-- Store Name + Slug -->
                            <td class="fw-semibold">
                                {{ $store->store_name }}
                                <div class="text-muted small">
                                    Slug: {{ $store->slug ?? '—' }}
                                </div>
                            </td>

                            <!-- Owner -->
                            <td>{{ $store->owner_name ?? '—' }}</td>

                            <!-- Email -->
                            <td>{{ $store->email ?? '—' }}</td>

                            <!-- Phone -->
                            <td>{{ $store->phone ?? '—' }}</td>

                            <!-- Status -->
                            <td>
                                @if($store->status)
                                    <span class="badge bg-success table-badge fs-10">Active</span>
                                @else
                                    <span class="badge bg-danger table-badge fs-10">Inactive</span>
                                @endif
                            </td>

                            <!-- Created Date -->
                            <td>{{ $store->created_at->format('d M Y') }}</td>

                            <!-- Action Buttons -->
                            <td class="action-table-data">
                                <div class="edit-delete-action">

                                    <!-- Edit -->
                                    <a href="{{ route('admin.stores.edit', $store->id) }}"
                                       class="p-2 me-1">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="{{ route('admin.stores.destroy', $store->id) }}"
                                          method="POST"
                                          id="del_form_{{ $store->id }}"
                                          style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <!-- Delete Button -->
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this store?')) {
                                             document.getElementById('del_form_{{ $store->id }}').submit();
                                       }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                No Stores Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
