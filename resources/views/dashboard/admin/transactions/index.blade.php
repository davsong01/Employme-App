@extends('dashboard.admin.index')
@section('title', 'All Users')
@section('content')
@php
    $totalUsers = method_exists($users, 'total') ? $users->total() : $users->count();
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">All Students</h1>
                            <p class="text-muted mb-0">Review participant records, export the roster, and manage each account from one place.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalUsers }} students</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ method_exists($users, 'lastPage') ? $users->lastPage() : 1 }} pages</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-3">
                <div class="text-muted small">Use the table below to review student records and manage access.</div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('users.create') }}" class="btn btn-outline-primary">Add New Student</a>
                    <button class="btn btn-success" id="csv">Export Students</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Trainings</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Date">
                                {{ optional($user->created_at)->format('d/m/Y') }}
                            </td>
                            <td data-label="Name">
                                <div class="fw-semibold">{{ $user->name }}</div>
                            </td>
                            <td data-label="Phone">{{ $user->phone }}</td>
                            <td data-label="Email">{{ $user->email }}</td>
                            <td data-label="Trainings">
                                {{ $user->programs()->count() }}
                            </td>
                            <td class="text-end" data-label="Manage">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit User" class="btn btn-outline-primary btn-sm" href="{{ route('users.edit', $user->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Impersonate User" class="btn btn-outline-warning btn-sm" href="{{ route('impersonate', $user->id) }}">
                                        <i class="fa fa-unlock"></i>
                                    </a>
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Send E-receipt" class="btn btn-outline-info btn-sm" href="{{ route('users.show', $user->id) }}">
                                        <i class="far fa-envelope"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete user">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('users.edit', $user->id) }}"><i class="fa fa-edit me-2"></i>Edit User</a></li>
                                        <li><a class="dropdown-item" href="{{ route('impersonate', $user->id) }}"><i class="fa fa-unlock me-2"></i>Impersonate</a></li>
                                        <li><a class="dropdown-item" href="{{ route('users.show', $user->id) }}"><i class="far fa-envelope me-2"></i>Send E-receipt</a></li>
                                        <li>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash me-2"></i>Delete User
                                                </button>
                                            </form>
                                        </li>
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
@section('extra-scripts')
<script type="text/javascript" src="{{ asset('src/jspdf.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('src/jspdf.plugin.autotable.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('src/tableHTMLExport.js') }}"></script>
<script type="text/javascript">
    $("#csv").on("click", function () {
        $("#zero_config").tableHTMLExport({
            type: 'csv',
            filename: 'Participants.csv'
        });
    });
</script>
@endsection
