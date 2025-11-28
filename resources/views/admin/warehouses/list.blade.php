@extends('layouts.admin.app')
@push('meta')
<!-- Meta Tags -->
<meta charset="utf-8">
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
{{-- 
<div class="page-wrapper">
   --}}
<div class="content">
					<div class="page-header">
						<div class="add-item d-flex">
							<div class="page-title">
								<h4>Warehouses</h4>
								<h6>Manage your warehouses</h6>
							</div>
						</div>
						<ul class="table-top-head">							
							<li class="me-2">
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Pdf"><img src="{{ asset('resource/admin/assets/img/icons/pdf.svg')}}" alt="img"></a>
							</li>
							<li class="me-2">
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Excel"><img src="{{ asset('resource/admin/assets/img/icons/excel.svg')}}" alt="img"></a>
							</li>
							<li class="me-2">
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
							</li>
							<li class="me-2">
								<a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a>
							</li>
						</ul>
						<div class="page-btn">
							<a href="{{route('admin.warehouses.create')}}" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-warehouse"><i class="ti ti-circle-plus me-1"></i>Add Warehouse</a>
						</div>
					</div>
					<!-- /product list -->
					<div class="card">
						<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
							<div class="search-set">
								<div class="search-input">
									<span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
								</div>
							</div>
							<div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
								<div class="dropdown">
									<a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
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
											<th>Warehouse</th>
											<th>Contact Person</th>
											<th>Phone</th>
											<th>Total Products</th>
											<th>Stock</th>
											<th>Qty</th>
											<th>Created On</th>
											<th>status</th>
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
											<td class="text-gray-9">Lavish Warehouse </td>
											<td>
												<div class="d-flex align-items-center">
													<a href="#" class="avatar avatar-md"><img src="assets/img/warehouse/avatar-01.png" class="img-fluid rounded-2" alt="img"></a>
													<div class="ms-2">
														<p class="mb-0"><a href="#" class="text-default">Chad Taylor</a></p>
													</div>
												</div>
											</td>
											<td>
												+12498345785						
											</td>
											<td>10</td>
											<td>
												600
											</td>
											<td>
												80
											</td>
											<td>
												24 Dec 2024
											</td>
											<td>
												<span class="badge badge-success d-inline-flex align-items-center badge-xs">
													<i class="ti ti-point-filled me-1"></i>Active
												</span>
											</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 p-2" href="#">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
													<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-warehouse">
														<i data-feather="edit" class="feather-edit"></i>
													</a>
													<a class="p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete-modal">
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
											<td class="text-gray-9">Quaint Warehouse</td>
											<td>
												<div class="d-flex align-items-center">
													<a href="#" class="avatar avatar-md"><img src="assets/img/warehouse/avatar-02.png" class="img-fluid rounded-2" alt="img"></a>
													<div class="ms-2">
														<p class="mb-0"><a href="#" class="text-default">Jenny Ellis</a></p>
													</div>
												</div>
											</td>
											<td>
												+13178964582								
											</td>
											<td>15</td>
											<td>
												300
											</td>
											<td>
												85
											</td>
											<td>
												10 Dec 2024
											</td>
											<td>
												<span class="badge badge-success d-inline-flex align-items-center badge-xs">
													<i class="ti ti-point-filled me-1"></i>Active
												</span>
											</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 p-2" href="#">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
													<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-warehouse">
														<i data-feather="edit" class="feather-edit"></i>
													</a>
													<a class="p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete-modal">
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
											<td class="text-gray-9">Traditional Warehouse</td>
											<td>
												<div class="d-flex align-items-center">
													<a href="#" class="avatar avatar-md"><img src="assets/img/warehouse/avatar-03.png" class="img-fluid rounded-2" alt="img"></a>
													<div class="ms-2">
														<p class="mb-0"><a href="#" class="text-default">Leon Baxter</a></p>
													</div>
												</div>
											</td>											
											<td>
												+12796183487								
											</td>
											<td>12</td>
											<td>400</td>
											<td>70</td>
											<td>
												27 Nov 2024
											</td>
											<td>
												<span class="badge badge-success d-inline-flex align-items-center badge-xs">
													<i class="ti ti-point-filled me-1"></i>Active
												</span>
											</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 p-2" href="#">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
													<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-warehouse">
														<i data-feather="edit" class="feather-edit"></i>
													</a>
													<a class="p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete-modal">
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
											<td class="text-gray-9">Fulfillment Hub</td>
											<td>
												<div class="d-flex align-items-center">
													<a href="#" class="avatar avatar-md"><img src="assets/img/warehouse/avatar-10.png" class="img-fluid rounded-2" alt="img"></a>
													<div class="ms-2">
														<p class="mb-0"><a href="#" class="text-default">Jada Robinson</a></p>
													</div>
												</div>
											</td>
											<td>
												+12678934561					
											</td>
											<td>14</td>
											<td>300 </td>
											<td>45</td>
											<td>
												10 Sep 2024
											</td>
											<td>
												<span class="badge badge-success d-inline-flex align-items-center badge-xs">
													<i class="ti ti-point-filled me-1"></i>Active
												</span>
											</td>
											<td class="action-table-data">
												<div class="edit-delete-action">
													<a class="me-2 p-2" href="#">
														<i data-feather="eye" class="feather-eye"></i>
													</a>
													<a class="me-2 p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit-warehouse">
														<i data-feather="edit" class="feather-edit"></i>
													</a>
													<a class="p-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete-modal">
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
					
				</div>
   {{-- 
</div>
--}}
@endsection