@extends('dashboard.admin.index')
@section('css')
<style>
    .participant-filter-card .form-control,
    .participant-filter-card .form-select {
        border-radius: 0.85rem;
    }
</style>
@endsection
@section('title', 'All Participants')
@section('content')
@php
    $currentStatus = request('status');
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">All Participants</h1>
                            <p class="text-muted mb-0">Search participants, review their trainings, and manage access from one page.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $records }} records</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $allPrograms->count() }} trainings</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm participant-filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}">
                <input type="hidden" name="status" value="{{ $currentStatus }}">
                <div class="row g-3">
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="program_id">Training</label>
                        <select name="program_id" id="program_id" class="form-select select2">
                            <option value="">All trainings</option>
                            @foreach($allPrograms as $training)
                                <option value="{{ $training->id }}" {{ request('program_id') == $training->id ? 'selected' : '' }}>{{ $training->p_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="staffID">Staff ID</label>
                        <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Staff ID" value="{{ request('staffID') }}">
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Participant name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email address" value="{{ request('email') }}">
                    </div>
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone number" value="{{ request('phone') }}">
                    </div>
                    <div class="col-md-4 col-lg-1">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="is_blacklisted">Blacklisted</label>
                        <select name="is_blacklisted" id="is_blacklisted" class="form-select">
                            <option value="">Any</option>
                            <option value="1" {{ request('is_blacklisted') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-4">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Last Login</th>
                            <th>Trainings</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php $count = 1; @endphp
                            <tr>
                                <td data-label="#">
                                    {{ $i++ }}
                                </td>
                                <td data-label="Details">
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted small">Email: {{ $user->email }}</div>
                                    <div class="text-muted small">Staff ID: {{ $user->staffID }}</div>
                                    <div class="text-muted small">Phone: {{ $user->phone }}</div>
                                    <div class="text-muted small">Balance: {{ number_format($user->account_balance) }}</div>
                                    <div class="text-muted small">Joined: {{ optional($user->created_at)->format('d M Y') }}</div>
                                </td>
                                <td data-label="Last Login">
                                    @if (App\Services\BlacklistService::check($user))
                                        <div class="mb-2"><span class="badge bg-danger">Blacklisted</span></div>
                                    @endif
                                    <span class="text-muted">{{ $user->last_login ? date('M jS, Y H:i', strtotime($user->last_login)) : 'Never' }}</span>
                                </td>
                                <td data-label="Trainings">
                                    @forelse($user->programs as $program)
                                        @if (in_array($program->id, $allPrograms->pluck('id')->toArray()))
                                            <div class="mb-1">
                                                <span class="badge bg-light text-dark rounded-pill">{{ $count++ }}. {{ $program->p_name }}</span>
                                            </div>
                                        @endif
                                    @empty
                                        <span class="text-muted">No trainings</span>
                                    @endforelse
                                </td>
                                <td class="text-end" data-label="Manage">
                                    <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                        @if(canUserAccessPermission(['users.edit'])['users.edit'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Edit User" class="btn btn-outline-primary btn-sm" href="{{ route('users.edit', $user->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(canUserAccessPermission(['impersonate'])['impersonate'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Impersonate User" class="btn btn-outline-warning btn-sm" href="{{ route('impersonate', $user->id) }}">
                                                <i class="fa fa-unlock"></i>
                                            </a>
                                        @endif
                                        @if(canUserAccessPermission(['users.destroy'])['users.destroy'])
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete user">
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
                                            @if(canUserAccessPermission(['users.edit'])['users.edit'])
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">
                                                        <i class="fa fa-edit me-2"></i>Edit User
                                                    </a>
                                                </li>
                                            @endif
                                            @if(canUserAccessPermission(['impersonate'])['impersonate'])
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('impersonate', $user->id) }}">
                                                        <i class="fa fa-unlock me-2"></i>Impersonate
                                                    </a>
                                                </li>
                                            @endif
                                            @if(canUserAccessPermission(['users.destroy'])['users.destroy'])
                                                <li>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash me-2"></i>Delete User
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
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>
@endsection
