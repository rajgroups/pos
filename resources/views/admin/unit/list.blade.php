@extends('layouts.admin.app')
@push('meta')
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Lion POS is a powerful Bootstrap based Inventory Management Admin Template designed for businesses, offering seamless invoicing, project tracking, and estimates.">
    <meta name="keywords"
        content="inventory management, admin dashboard, bootstrap template, invoicing, estimates, business management, responsive admin, POS system">
    <meta name="author" content="Dreams Technologies">
    <meta name="robots" content="index, follow">
    <title>Lion POS - Inventory Management & Admin Dashboard</title>
@endpush
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Units</h4>
                <h6>Manage your units</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="{{ asset('resource/admin/assets/img/icons/pdf.svg') }}"
                        alt="img"></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img src="{{ asset('resource/admin/assets/img/icons/excel.svg') }}"
                        alt="img"></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i
                        class="ti ti-chevron-up"></i></a>
            </li>
        </ul>
        <div class="page-btn">
            <a href="{{ route('admin.unit.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Unit</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
            <div class="table-dropdown my-xl-auto right-content">
                <div class="dropdown">
                    <a href="javascript:void(0);"
                        class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center"
                        data-bs-toggle="dropdown">
                        Status
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Active</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Inactive</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @if (session()->has('success'))
            <div class="alert alert-solid-success rounded-pill alert-dismissible fade show">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="alert alert-solid-danger rounded-pill alert-dismissible fade show">
                {{ session()->get('error') }}
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
                            <th class="no-sort">
                                <label class="checkboxs">
                                    <input type="checkbox" id="select-all">
                                    <span class="checkmarks"></span>
                                </label>
                            </th>
                            <th>Unit</th>
                            <th>Short name</th>
                            <th>No of Products</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th class="no-sort"></th>
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
                            <td class="text-gray-9">{{ $unit->name }}</td>
                            <td>{{ $unit->shortname }}</td>
                            <td>{{ $unit->no_of_product }}</td>
                            <td>{{ \Carbon\Carbon::parse($unit->created_at)->format('d M Y') }}</td>
                            <td>
                                @if($unit->status == 'active')
                                    <span class="badge table-badge bg-success fw-medium fs-10">Active</span>
                                @else
                                    <span class="badge table-badge bg-danger fw-medium fs-10">Inactive</span>
                                @endif
                            </td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="{{route('admin.unit.edit',$unit->id)}}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.unit.destroy', $unit->id) }}" method="POST" id="delete_unit_frm_{{ $unit->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="unit_ids[]" value="{{ $unit->id }}">
                                    </form>
                                    
                                    <a href="javascript:void(0);" class="p-2" id="delete_unit_single" 
                                       onclick="if(confirm('Are you sure you want to delete this unit?')) { document.getElementById('delete_unit_frm_{{ $unit->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        @empty
                            
                        @endforelse
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- delete modal -->
    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="page-wrapper-new p-0">
                    <div class="p-5 px-3 text-center">
                            <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
                            <h4 class="fs-20 fw-bold mb-2 mt-1">Delete Unit</h4>
                            <p class="mb-0 fs-16">Are you sure you want to delete unit?</p>
                            <div class="modal-footer-btn mt-3 d-flex justify-content-center">
                                <button type="button" class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary fs-13 fw-medium p-2 px-3">Yes Delete</button>
                            </div>						
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection