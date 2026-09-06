@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">CMS Pages</h4>
                <h6>Manage Content Pages (Privacy Policy, Terms, etc.)</h6>
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
            <a href="{{ route('admin.cms-pages.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Page</a>
        </div>
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

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th class="no-sort text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                        <tr>
                            <td class="fw-bold">{{ $page->title }}</td>
                            <td><code>{{ $page->slug }}</code></td>
                            <td>
                                @if($page->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $page->updated_at->format('d M Y') }}</td>
                            <td class="action-table-data text-end">
                                <div class="edit-delete-action justify-content-end">
                                    <a class="me-2 p-2" href="{{ route('admin.cms-pages.edit', $page->id) }}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.cms-pages.destroy', $page->id) }}" method="POST" id="delete_frm_{{ $page->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this page?')) { document.getElementById('delete_frm_{{ $page->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($pages->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
