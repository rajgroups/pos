@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">admin Show</h4>
                <h6>Manage your barcodes</h6>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <ul class="table-top-head">
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Pdf" data-bs-original-title="Pdf"><img
                            src="{{ asset('resource/admin/assets/img/icons/pdf.svg') }}" alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Excel"
                        data-bs-original-title="Excel">
                        <img rc="{{ asset('resource/admin/assets/img/icons/excel.svg') }}" alt="img"></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Refresh"
                        data-bs-original-title="Refresh"><i class="ti ti-refresh"></i></a>
                </li>
                <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" id="collapse-header" aria-label="Collapse"
                        data-bs-original-title="Collapse"><i class="ti ti-chevron-up"></i></a>
                </li>
                 <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" id="collapse-header" aria-label="Collapse"
                        data-bs-original-title="Collapse" href="{{ route('admin.admin.create') }}"><i class="ti ti-plus"></i></a>
                </li>
                 <li>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" id="collapse-header" aria-label="Collapse"
                        data-bs-original-title="Collapse" href="{{ route('admin.admin.edit',1) }}"><i class="ti ti-pencil"></i></a>
                </li>
            </ul>
        </div>
    </div>
@endsection
