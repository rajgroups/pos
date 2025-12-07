@extends('layouts.admin.app')

@push('meta')
<title>Variant Attributes - Lion POS</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">
        <h4 class="fw-bold">Variant Attribute List</h4>
        <h6 class="text-muted">Manage product variant attributes (size, color, material)</h6>
    </div>

    <div class="page-btn">
        <a href="{{ route('admin.variant-attributes.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Add Variant Attribute
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
        <div class="search-set">
            <div class="search-input">
                <span class="btn-searchset"><i class="ti ti-search fs-14"></i></span>
            </div>
        </div>

        <div class="table-dropdown">
            <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-white btn-md" data-bs-toggle="dropdown">
                    Filter
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-3">
                    <li><a class="dropdown-item rounded-1" href="#">Active</a></li>
                    <li><a class="dropdown-item rounded-1" href="#">Inactive</a></li>
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
                    <th>Slug</th>
                    <th>Type</th>
                    <th>Values</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="no-sort"></th>
                </tr>
                </thead>

                <tbody>
                @forelse ($variants as $variant)
                    <tr>

                        {{-- Checkbox --}}
                        <td>
                            <label class="checkboxs">
                                <input type="checkbox">
                                <span class="checkmarks"></span>
                            </label>
                        </td>

                        {{-- Name --}}
                        <td class="fw-semibold">{{ $variant->name }}</td>

                        {{-- Slug --}}
                        <td>{{ $variant->slug }}</td>

                        {{-- Type --}}
                        <td class="text-capitalize">{{ $variant->type_label }}</td>

                        {{-- Values --}}
                        <td>{{ $variant->formatted_values }}</td>

                        {{-- Status --}}
                        <td>
                            @if($variant->status)
                                <span class="badge bg-success table-badge fs-10">Active</span>
                            @else
                                <span class="badge bg-danger table-badge fs-10">Inactive</span>
                            @endif
                        </td>

                        {{-- Created --}}
                        <td>{{ $variant->created_at->format('d M Y') }}</td>

                        {{-- Actions --}}
                        <td class="action-table-data">
                            <div class="edit-delete-action">

                                {{-- EDIT --}}
                                <a href="{{ route('admin.variant-attributes.edit', $variant->id) }}" class="p-2 me-1">
                                    <i data-feather="edit" class="feather-edit"></i>
                                </a>

                                {{-- DELETE FORM --}}
                                <form action="{{ route('admin.variant-attributes.destroy', $variant->id) }}"
                                      method="POST" id="del_form_{{ $variant->id }}" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                {{-- DELETE BUTTON --}}
                                <a href="javascript:void(0);" class="p-2 text-danger"
                                   onclick="if(confirm('Are you sure you want to delete this variant?')) {
                                          document.getElementById('del_form_{{ $variant->id }}').submit();
                                   }">
                                    <i data-feather="trash-2" class="feather-trash-2"></i>
                                </a>

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            No Variant Attributes Found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection
