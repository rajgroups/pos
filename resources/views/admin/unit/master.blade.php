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
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img src="{{ asset('resource/admin/assets/img/icons/excel.svg') }}
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
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-units"><i
                    class="ti ti-circle-plus me-1"></i>Add Unit</a>
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
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Kilograms</td>
                            <td>kg</td>
                            <td>25</td>
                            <td>24 Dec 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Liters</td>
                            <td>L</td>
                            <td>18</td>
                            <td>10 Dec 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal" data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Dozen</td>
                            <td>dz</td>
                            <td>30</td>
                            <td>27 Nov 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Pieces</td>
                            <td>pcs</td>
                            <td>42</td>
                            <td>18 Nov 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Boxes</td>
                            <td>bx</td>
                            <td>60</td>
                            <td>06 Nov 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Tons</td>
                            <td>t</td>
                            <td>10</td>
                            <td>25 Oct 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Grams</td>
                            <td>g</td>
                            <td>70</td>
                            <td>03 Oct 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Meters</td>
                            <td>m</td>
                            <td>80</td>
                            <td>20 Sep 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkboxs">
                                    <input type="checkbox">
                                    <span class="checkmarks"></span>
                                </label>
                            </td>
                            <td class="text-gray-9">Centimeters</td>
                            <td>cm</td>
                            <td>120</td>
                            <td>10 Sep 2024</td>
                            <td><span class="badge table-badge bg-success fw-medium fs-10">Active</span></td>
                            <td class="action-table-data">
                                <div class="edit-delete-action">
                                    <a class="me-2 p-2" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit-units">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <a data-bs-toggle="modal" data-bs-target="#delete-modal" class="p-2"
                                        href="javascript:void(0);">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /product list -->
    <!-- Add Unit -->
    <div class="modal fade" id="add-units">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="page-title">
                        <h4>Add Unit</h4>
                    </div>
                    <button type="button" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
    <!-- /Add Unit -->

    <!-- Edit Unit -->
    <div class="modal fade" id="edit-units">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="page-title">
                        <h4>Edit Unit</h4>
                    </div>
                    <button type="button" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="https://dreamspos.dreamstechnologies.com/html/template/units.html">
                    <div class="modal-body">		
                        <div class="mb-3">
                            <label class="form-label">Unit<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" value="Kilograms">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Short Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" value="kg">
                        </div>
                        <div class="mb-0">
                            <div class="status-toggle modal-status d-flex justify-content-between align-items-center">
                                <span class="status-label">Status</span>
                                <input type="checkbox" id="user3" class="check" checked="">
                                <label for="user3" class="checktoggle"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-2 btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit Unit -->

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
<script>
$('document').ready(function(){
    $('#add_unit_btn').on('click', function(){    
        // e.preventDefault();

        window.alert('e');
        // Get the Input Values
        var name = $('#name').val(); // Properly select input by name
        var shortname = $('#shortname').val(); // Properly select input by name
        var status = $('#status').val(); // Properly select input by name
        var no_of_product = $('#no_of_product').val();

        // Clear previous validation messages
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        var isValid = true;

        // Validate Name (Required)
        if (name === '') {
            $('#name').addClass('is-invalid').after('<div class="invalid-feedback">Name is required.</div>');
            isValid = false;
        }

        // Validate Short Name (Required)
        if (shortname === '') {
            $('#shortname').addClass('is-invalid').after('<div class="invalid-feedback">Short Name is required.</div>');
            isValid = false;
        }

        // Validate Status (Required)
        if (no_of_product === '') {
            $('#no_of_product').addClass('is-invalid').after('<div class="invalid-feedback">Please select a status.</div>');
            isValid = false;
        }

                // Validate Status (Required)
        if (status === '') {
            $('#status').addClass('is-invalid').after('<div class="invalid-feedback">Please select a status.</div>');
            isValid = false;
        }

        // If validation fails, stop here
        if (!isValid) return;

        $.ajax({
            url:"/admin/unit",
            type:'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF Token
            },
            data: {
                name: name,
                shortname: shortname,
                status: status,
                no_of_product:no_of_product,
            },
            success: function (response) {
                alert()
                if (response.success) {
                    alert("Unit added successfully!");
                    location.reload(); // Reload page after success
                } else {
                    alert("Error: " + response.error);
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function (key, value) {
                        $('#' + key).addClass('is-invalid').after('<div class="invalid-feedback">' + value[0] + '</div>');
                    });
                }
            }
        });
    });

    $('#unitTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.unit.index') }}", // Fetch data from controller
            columns: [
                { data: 'id', render: function (data, type, row) {
                    return '<label class="checkboxs"><input type="checkbox" name="unit_ids[]" value="'+ data +'"><span class="checkmarks"></span></label>';
                }},
                { data: 'name' },
                { data: 'shortname' },
                { data: 'no_of_product' },
                { data: 'created_at', render: function(data) {
                    return new Date(data).toLocaleDateString(); // Format date
                }},
                { data: 'status', render: function (data) {
                    return data === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }},
                { data: 'id', render: function (data, type, row) {
                    return '<a href="unit/edit/' + data + '" class="btn btn-sm btn-primary">Edit</a> ' +
                           '<button class="btn btn-sm btn-danger delete-btn" data-id="' + data + '">Delete</button>';
                }}
            ]
        });

        // Delete unit using AJAX
        $(document).on('click', '.delete-btn', function () {
            var unitId = $(this).data('id');
            if (confirm('Are you sure you want to delete this unit?')) {
                $.ajax({
                    url: "unit/" + unitId,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        alert(response.message);
                        $('#unitTable').DataTable().ajax.reload();
                    }
                });
            }
        });
});
</script>
@endpush
