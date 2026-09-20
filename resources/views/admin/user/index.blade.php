@extends('layouts.admin.app')
@section('content')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">User List</h4>
                <h6>Manage riders and their accounts</h6>
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
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add User</a>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Joined At</th>
                            <th class="no-sort text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td class="fw-bold">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $user->profile_image ? asset('storage/'.$user->profile_image) : asset('resource/admin/assets/img/profiles/avatar-02.jpg') }}" alt="Profile" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                    {{ trim($user->first_name . ' ' . $user->last_name) ?: $user->name }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile ?? '-' }}</td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="action-table-data text-end">
                                <div class="edit-delete-action justify-content-end">
                                    <a class="me-2 p-2" href="{{ route('admin.users.show', $user->id) }}">
                                        <i data-feather="eye" class="feather-eye"></i>
                                    </a>
                                    <a class="me-2 p-2" href="{{ route('admin.users.edit', $user->id) }}">
                                        <i data-feather="edit" class="feather-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" id="delete_frm_{{ $user->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="javascript:void(0);" class="p-2 text-danger"
                                       onclick="if(confirm('Are you sure you want to delete this user?')) { document.getElementById('delete_frm_{{ $user->id }}').submit(); }">
                                        <i data-feather="trash-2" class="feather-trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
