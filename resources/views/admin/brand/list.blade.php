@extends('layouts.admin.app')
@push('meta')
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Lion POS - Manage Brands">
    <meta name="keywords"
        content="brand management, inventory management, admin dashboard">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="noindex, nofollow">
    <title>Brand Management | Lion POS</title>
@endpush
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create New Brand</h4>
                <h6 class="text-muted">Add a new product category to organize your inventory</h6>
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
        <div class="page-btn">
            <a href="{{ route('admin.category.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Back to Categories
            </a>
        </div>
    </div>

    @if(session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        <div class="d-flex align-items-center">
            <i class="ti ti-circle-check me-2"></i>
            {{ session()->get('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-circle me-2"></i>
            {{ session()->get('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- /product list -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <input type="text" id="searchInput" placeholder="Search brands..." class="form-control">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
            <div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                    Status
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1 filter-status" data-status="all">All</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1 filter-status" data-status="1">Active</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1 filter-status" data-status="0">Inactive</a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                    Sort By : Latest
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-by" data-sort="latest">Latest</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-by" data-sort="name_asc">Name (A-Z)</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-by" data-sort="name_desc">Name (Z-A)</a>
                        </li>
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
                            <th>Brand</th>
                            <th>Slug</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th class="no-sort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                        <tr data-status="{{ $brand->status }}" data-name="{{ strtolower($brand->name) }}">
                            <td>
                                <label class="checkboxs">
                                <input type="checkbox" class="brand-checkbox" value="{{ $brand->id }}">
                                <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-light-900 p-1 me-2 d-flex align-items-center justify-content-center">
                                        @if($brand->icon)
                                            <img class="object-fit-contain" src="{{ $brand->icon_url }}" alt="{{ $brand->name }}" style="max-height: 32px; max-width: 32px;">
                                        @elseif($brand->image)
                                            <img class="object-fit-contain" src="{{ $brand->image_url }}" alt="{{ $brand->name }}" style="max-height: 32px; max-width: 32px;">
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="ti ti-package text-white fs-4"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.brand.edit', $brand->id) }}" class="fw-medium">{{ $brand->name }}</a>
                                        @if($brand->image)
                                            <i class="ti ti-photo ms-1 text-muted fs-12" data-bs-toggle="tooltip" title="Has image"></i>
                                        @endif
                                        @if($brand->icon)
                                            <i class="ti ti-icons ms-1 text-muted fs-12" data-bs-toggle="tooltip" title="Has icon"></i>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $brand->slug }}</span>
                            </td>
                            <td>{{ $brand->created_at->format('d M Y') }}</td>
                            <td>
                                @if($brand->status == 1)
                                    <span class="badge table-badge bg-success fw-medium fs-10">Active</span>
                                @else
                                    <span class="badge table-badge bg-danger fw-medium fs-10">Inactive</span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="{{ route('admin.brand.edit', $brand->id) }}" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <a class="p-2 delete-brand" href="javascript:void(0);"
                                       data-id="{{ $brand->id }}"
                                       data-name="{{ $brand->name }}"
                                       title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="ti ti-package-off fs-1 text-muted mb-3"></i>
                                    <h5 class="mb-2">No Brands Found</h5>
                                    <p class="text-muted mb-4">Get started by creating your first brand.</p>
                                    <a href="{{ route('admin.brand.create') }}" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i>Add New Brand
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($brands->hasPages())
            <div class="card-footer border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $brands->firstItem() ?? 0 }} to {{ $brands->lastItem() ?? 0 }} of {{ $brands->total() }} entries
                    </div>
                    <div>
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <!-- /product list -->
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Action</label>
                    <select class="form-select" id="bulkActionSelect">
                        <option value="">Choose action...</option>
                        <option value="activate">Activate Selected</option>
                        <option value="deactivate">Deactivate Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    Selected <span id="selectedCount" class="fw-bold">0</span> brand(s)
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyBulkAction">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="ti ti-alert-triangle text-danger fs-1"></i>
                        </div>
                        <h6 class="mb-3">Are you sure you want to delete this brand?</h6>
                        <p class="text-muted">
                            Brand: <strong id="deleteBrandName"></strong><br>
                            This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Select all checkbox
    $('#select-all').change(function() {
        $('.brand-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox change
    $('.brand-checkbox').change(function() {
        if(!this.checked) {
            $('#select-all').prop('checked', false);
        } else {
            var allChecked = $('.brand-checkbox:checked').length === $('.brand-checkbox').length;
            $('#select-all').prop('checked', allChecked);
        }
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        var count = $('.brand-checkbox:checked').length;
        $('#selectedCount').text(count);

        // Show/hide bulk actions button
        if(count > 0) {
            if(!$('#bulkActionsBtn').length) {
                $('.page-btn').append('<button class="btn btn-outline-primary ms-2" id="bulkActionsBtn" data-bs-toggle="modal" data-bs-target="#bulkActionsModal"><i class="ti ti-list-check me-1"></i>Bulk Actions</button>');
            }
        } else {
            $('#bulkActionsBtn').remove();
        }
    }

    // Delete brand
    $('.delete-brand').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#deleteBrandName').text(name);
        $('#deleteForm').attr('action', '/admin/brand/' + id);

        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('tbody tr').filter(function() {
            var brandName = $(this).data('name') || '';
            $(this).toggle(brandName.indexOf(value) > -1 || value === '');
        });
    });

    // Filter by status
    $('.filter-status').click(function() {
        var status = $(this).data('status');
        if(status === 'all') {
            $('tbody tr').show();
        } else {
            $('tbody tr').each(function() {
                $(this).toggle($(this).data('status') == status);
            });
        }
        $(this).closest('.dropdown').find('.dropdown-toggle').text('Status: ' + $(this).text());
    });

    // Sort functionality
    $('.sort-by').click(function() {
        var sort = $(this).data('sort');
        var rows = $('tbody tr').get();

        rows.sort(function(a, b) {
            var aVal, bVal;

            switch(sort) {
                case 'name_asc':
                    aVal = $(a).data('name');
                    bVal = $(b).data('name');
                    return aVal.localeCompare(bVal);
                case 'name_desc':
                    aVal = $(a).data('name');
                    bVal = $(b).data('name');
                    return bVal.localeCompare(aVal);
                case 'latest':
                default:
                    // Assuming rows are already in latest order from server
                    return 0;
            }
        });

        $.each(rows, function(index, row) {
            $('tbody').append(row);
        });

        $(this).closest('.dropdown').find('.dropdown-toggle').text('Sort By: ' + $(this).text());
    });

    // Bulk actions
    $('#applyBulkAction').click(function() {
        var action = $('#bulkActionSelect').val();
        var selectedIds = [];

        $('.brand-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if(!action) {
            alert('Please select an action');
            return;
        }

        if(selectedIds.length === 0) {
            alert('Please select at least one brand');
            return;
        }

        if(action === 'delete') {
            if(!confirm('Are you sure you want to delete ' + selectedIds.length + ' brand(s)?')) {
                return;
            }
        }

        // Submit bulk action
        {{-- {{ route("admin.brand.bulk-action") }} --}}
        $.ajax({
             url: '',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: selectedIds,
                action: action
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'An error occurred');
                }
            },
            error: function() {
                alert('An error occurred while processing your request');
            }
        });
    });
});
</script>
@endpush

@push('css')
<style>
.avatar {
    border-radius: 8px;
    overflow: hidden;
}

.table-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.75rem;
}

.action-table-data .edit-delete-action {
    display: flex;
    gap: 0.5rem;
}

.action-table-data a {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.action-table-data a:hover {
    background: #e9ecef;
    color: #495057;
    transform: translateY(-2px);
}

.empty-state {
    padding: 3rem 1rem;
    text-align: center;
}

.empty-state i {
    opacity: 0.5;
}

.checkboxs {
    position: relative;
    display: inline-block;
}

.checkboxs input[type="checkbox"] {
    opacity: 0;
    position: absolute;
}

.checkboxs .checkmarks {
    position: relative;
    display: inline-block;
    width: 20px;
    height: 20px;
    background-color: #fff;
    border: 2px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
}

.checkboxs input[type="checkbox"]:checked + .checkmarks {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.checkboxs input[type="checkbox"]:checked + .checkmarks:after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.search-input {
    position: relative;
}

.search-input input {
    padding-left: 40px;
}

.search-input .btn-searchset {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    cursor: pointer;
}

.table thead th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 1rem 0.75rem;
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.pagination {
    margin-bottom: 0;
}
</style>
@endpush
