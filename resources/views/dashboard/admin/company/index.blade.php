@php
    $check = [
        'companyuser.create',
        'companyuser.edit',
        'companyuser.destroy'
    ];

    $permissions = canUserAccessPermission($check);
@endphp

@extends('dashboard.admin.index')
@section('title', 'All Company Users')
@section('content')
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">All Company Users</h1>
                            <p class="text-muted mb-0">Manage company admins and their linked trainings in one streamlined view.</p>
                        </div>
                        @if($permissions['companyuser.create'])
                            <a href="{{ route('companyuser.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus me-1"></i> Add New
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Trainings</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php $index = 1; @endphp
                            <tr>
                                <td data-label="#">
                                    {{ $i++ }}
                                </td>
                                <td data-label="Details">
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted small">Email: {{ $user->email }}</div>
                                    <div class="text-muted small">Phone: {{ $user->phone }}</div>
                                </td>
                                <td data-label="Trainings">
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($user->p_names as $names)
                                            <span class="badge bg-light text-dark rounded-pill">{{ $index++ }}. {{ $names->p_name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td data-label="Status">
                                    @if($user->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td data-label="Date Added">{{ optional($user->created_at)->format('d M Y') }}</td>
                                <td class="text-end" data-label="Manage">
                                    <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                        @if($permissions['companyuser.edit'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-outline-primary btn-sm" href="{{ route('companyuser.edit', $user->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($permissions['companyuser.destroy'])
                                            <form action="{{ route('companyuser.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="dropdown d-inline-flex d-md-none">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($permissions['companyuser.edit'])
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('companyuser.edit', $user->id) }}">
                                                        <i class="fa fa-edit me-2"></i>Edit
                                                    </a>
                                                </li>
                                            @endif
                                            @if($permissions['companyuser.destroy'])
                                                <li>
                                                    <form action="{{ route('companyuser.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
