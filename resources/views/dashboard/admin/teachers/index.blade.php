@php
    $check = [
        'teachers.create',
        'teachers.edit',
        'admin-impersonate',
        'teachers.destroy',
    ];

    $permissions = canUserAccessPermission($check);
    $totalTeachers = method_exists($users, 'total') ? $users->total() : $users->count();
@endphp
@extends('dashboard.admin.index')
@section('title', 'All Facilitators')
@section('content')
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Facilitators &amp; Graders</h1>
                            <p class="text-muted mb-0">Review staff profiles, assigned trainings, and account-level actions from one dashboard.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalTeachers }} staff</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ count($check) }} permissions</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-3">
                <div class="text-muted small">Use this list to manage facilitators, graders, and impersonation access.</div>
                @if($permissions['teachers.create'])
                    <a href="{{ route('teachers.create') }}" class="btn btn-outline-primary">Add New</a>
                @endif
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Details</th>
                            <th>Role</th>
                            <th>Trainings</th>
                            <th>Direct Students</th>
                            <th>Earnings</th>
                            <th>Off Season</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Profile">
                                <img src="{{ $user->image }}" alt="avatar" class="rounded-circle" width="50" height="50">
                            </td>
                            <td data-label="Details">
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="text-muted small">Email: {{ $user->email }}</div>
                                <div class="text-muted small">Phone: {{ $user->phone }}</div>
                                <div class="text-muted small">Assigned trainings: {{ $user->trainings->count() }}</div>
                                @if($user->license)
                                    <div class="text-success small">WTN License: {{ $user->license }}</div>
                                @endif
                                @if($user->last_login)
                                    <div class="text-muted small">Last Login: {{ date("M jS, Y H:i", strtotime($user->last_login)) }}</div>
                                @endif
                            </td>
                            <td data-label="Role">
                                <div class="d-grid gap-1">
                                    @if(checkRoleHas(['Facilitator'], $user))
                                        <span class="badge bg-primary">Facilitator</span>
                                    @endif
                                    @if(checkRoleHas(['Grader'], $user))
                                        <span class="badge bg-info text-light">Grader</span>
                                    @endif
                                    @if(checkRoleHas(['Admin'], $user))
                                        <span class="badge bg-success">Admin</span>
                                    @endif
                                    <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->status == 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>
                            <td data-label="Trainings">
                                <div class="small">
                                    @foreach($user->p_names as $index => $names)
                                        <div>{{ $names }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td data-label="Direct Students">
                                <div>{{ $user->students_count }}</div>
                                <a target="_blank" href="{{ route('teachers.students', $user->id) }}" class="btn btn-outline-info btn-sm mt-2">View</a>
                            </td>
                            <td data-label="Earnings">
                                <div>{{ $user->payment_modes->currency_symbol ?? 'NGN' }}{{ $user->earnings ? number_format($user->earnings) : 0 }}</div>
                                <a target="_blank" href="{{ route('teachers.earnings', $user->id) }}" class="btn btn-outline-info btn-sm mt-2">View</a>
                            </td>
                            <td data-label="Off Season">
                                {{ $user->off_season_availability == 1 ? 'Yes' : 'No' }}
                            </td>
                            <td class="text-end" data-label="Manage">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    @if($permissions['teachers.edit'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Edit Staff" class="btn btn-outline-primary btn-sm" href="{{ route('teachers.edit', $user->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($permissions['admin-impersonate'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Impersonate User" class="btn btn-outline-warning btn-sm" href="{{ route('admin.impersonate', $user->id) }}">
                                            <i class="fa fa-unlock"></i>
                                        </a>
                                    @endif
                                    @if($permissions['teachers.destroy'])
                                        <form action="{{ route('teachers.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete facilitator">
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
                                        @if($permissions['teachers.edit'])
                                            <li><a class="dropdown-item" href="{{ route('teachers.edit', $user->id) }}"><i class="fa fa-edit me-2"></i>Edit Staff</a></li>
                                        @endif
                                        @if($permissions['admin-impersonate'])
                                            <li><a class="dropdown-item" href="{{ route('admin.impersonate', $user->id) }}"><i class="fa fa-unlock me-2"></i>Impersonate</a></li>
                                        @endif
                                        @if($permissions['teachers.destroy'])
                                            <li>
                                                <form action="{{ route('teachers.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash me-2"></i>Delete Facilitator
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
