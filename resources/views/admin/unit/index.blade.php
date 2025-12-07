{{-- @dd($units[0]->status) --}}
@extends('layouts.admin.app')

@push('meta')
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage product units in Lion POS inventory system.">
    <meta name="keywords" content="unit, measurement, inventory, POS, product units">
    <title>Units - Lion POS</title>
@endpush

@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Unit List</h4>
                <h6>All measurement units used in your inventory</h6>
            </div>
        </div>

        <ul class="table-top-head">
            <li><a data-bs-toggle="tooltip" title="Refresh"><i class="ti ti-refresh"></i></a></li>
            <li><a data-bs-toggle="tooltip" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li>
        </ul>

        <div class="page-btn">
            <a href="{{ route('admin.unit.create') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i>Add Unit
            </a>
        </div>
    </div>

    <div class="card">

        {{-- SUCCESS MESSAGE --}}
        @if (session()->has('success'))
            <div class="alert alert-solid-success rounded-pill alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        @endif

        {{-- ERROR MESSAGE --}}
        @if (session()->has('error'))
            <div class="alert alert-solid-danger rounded-pill alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
            <div class="table-dropdown my-xl-auto right-content">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center"
                        data-bs-toggle="dropdown">Status</a>
                    <ul class="dropdown-menu dropdown-menu-end p-3">
                        <li><a class="dropdown-item rounded-1">Active</a></li>
                        <li><a class="dropdown-item rounded-1">Inactive</a></li>
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
    <th>Unit Name</th>
    <th>Short Name</th>
    <th>No. of Products</th>
    <th>Type</th>
    <th>Status</th>
    <th>Created At</th>
    <th class="no-sort">Controle</th>
</tr>
</thead>

                    <tbody>
                        @forelse ($units as $unit)
                            <tr>
                                <td>
                                    <label class="checkboxs">
                                        <input type="checkbox">
                                        <span class="checkmarks"></span>
                                    </label>
                                </td>

                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->shortname }}</td>
                                <td>{{ $unit->no_of_product ?? '-' }}</td>
                                <td class="text-capitalize">{{ $unit->type ?? '-' }}</td>

                                <td>
                                    @if ($unit->status->value == 1)
                                        <span class="badge table-badge bg-success fs-10">Active</span>
                                    @else
                                        <span class="badge table-badge bg-danger fs-10">Inactive</span>
                                    @endif
                                </td>

                                <td>{{ $unit->created_at->format('d M Y') }}</td>

                                <td class="action-table-data">
                                    <div class="edit-delete-action">
                                        <a href="{{ route('admin.unit.edit', $unit->id) }}" class="p-2 me-2">
                                            <i data-feather="edit"></i>
                                        </a>

                                        <form id="delete_unit_{{ $unit->id }}"
                                            action="{{ route('admin.unit.destroy', $unit->id) }}" method="POST"
                                            style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>

                                        <a href="javascript:void(0);" class="p-2"
                                            onclick="if(confirm('Delete this unit?')) document.getElementById('delete_unit_{{ $unit->id }}').submit();">
                                            <i data-feather="trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No units found</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>

    </div>
@endsection
