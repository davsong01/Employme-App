@extends('dashboard.admin.index')
@section('css')
<style>
    .participant-filter-card .form-control,
    .participant-filter-card .form-select {
        border-radius: 0.85rem;
    }

    .bulk-action-bar {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
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
        @if(checkRoleHas(['Admin']))
            <div class="bulk-action-bar p-3">
                <form id="bulk-attach-form" action="{{ route('users.bulk.attach.programs') }}" method="POST">
                    @csrf
                    <div id="bulk-user-inputs"></div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="select-all-users">
                            <label class="form-check-label" for="select-all-users">Select all on page</label>
                        </div>
                        <select id="bulk-action" class="form-select form-select-sm" style="max-width: 180px;" disabled>
                            <option value="">Bulk actions</option>
                            <option value="add-program">Add program</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-secondary" id="bulk-apply" disabled>Apply</button>
                        <span id="selected-users-count" class="text-muted small">0 selected</span>
                    </div>
                </form>
            </div>
        @endif
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            @if(checkRoleHas(['Admin']))
                                <th><span class="visually-hidden">Select</span></th>
                            @endif
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
                                @if(checkRoleHas(['Admin']))
                                    <td data-label="Select">
                                        <input class="form-check-input user-select-checkbox" type="checkbox" value="{{ $user->id }}" aria-label="Select {{ $user->name }}">
                                    </td>
                                @endif
                                <td data-label="#">
                                    {{ $i++ }}
                                </td>
                                <td data-label="Details">
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="text-muted small">Email: {{ $user->email }}</div>
                                    <div class="text-muted small">Staff ID: {{ $user->staffID }}</div>
                                    <div class="text-muted small">Phone: {{ $user->phone }}</div>
                                    <div class="text-muted small">Status: <span class="badge {{ $user->status === 'suspended' ? 'bg-danger' : ($user->status === 'inactive' ? 'bg-secondary' : 'bg-success') }}">{{ ucfirst($user->status ?: 'active') }}</span></div>
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
                                        @if(checkRoleHas(['Admin']))
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Participant evidence" class="btn btn-outline-info btn-sm" href="{{ route('users.evidence', $user->id) }}">
                                                <i class="fa fa-file-alt"></i>
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
                                            @if(checkRoleHas(['Admin']))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('users.evidence', $user->id) }}">
                                                        <i class="fa fa-file-alt me-2"></i>Participant evidence
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

@if(checkRoleHas(['Admin']))
    <div class="modal fade" id="bulkProgramModal" tabindex="-1" aria-labelledby="bulkProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkProgramModalLabel">Add programs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Choose one or more trainings to attach to the selected participants.</p>
                    <label for="bulk-programs" class="form-label">Trainings</label>
                    <select id="bulk-programs" class="form-select" multiple size="8">
                        @foreach($allPrograms as $program)
                            <option value="{{ $program->id }}">{{ $program->p_name }}</option>
                        @endforeach
                    </select>
                    <div id="bulk-program-error" class="text-danger small mt-2 d-none">Select at least one training.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-bulk-programs">Add</button>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
@section('extra-scripts')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });

        const $userCheckboxes = $('.user-select-checkbox');
        const $selectAll = $('#select-all-users');
        const $bulkAction = $('#bulk-action');
        const $bulkApply = $('#bulk-apply');
        const $selectedCount = $('#selected-users-count');
        const bulkModalElement = document.getElementById('bulkProgramModal');
        const bulkModal = bulkModalElement ? new bootstrap.Modal(bulkModalElement) : null;

        function selectedUserIds() {
            return $userCheckboxes.filter(':checked').map(function () {
                return this.value;
            }).get();
        }

        function updateBulkControls() {
            const selected = selectedUserIds();
            $selectedCount.text(`${selected.length} selected`);
            $bulkAction.prop('disabled', selected.length === 0);
            $bulkApply.prop('disabled', selected.length === 0);
            $selectAll.prop('checked', $userCheckboxes.length > 0 && selected.length === $userCheckboxes.length);
        }

        $userCheckboxes.on('change', updateBulkControls);
        $selectAll.on('change', function () {
            $userCheckboxes.prop('checked', this.checked);
            updateBulkControls();
        });

        $bulkApply.on('click', function () {
            if ($bulkAction.val() === 'add-program') {
                $('#bulk-programs').val([]);
                $('#bulk-program-error').addClass('d-none');
                bulkModal.show();
            }
        });

        $('#confirm-bulk-programs').on('click', function () {
            const programIds = $('#bulk-programs').val() || [];
            const userIds = selectedUserIds();

            if (!programIds.length) {
                $('#bulk-program-error').removeClass('d-none');
                return;
            }

            if (!window.confirm(`Attach ${programIds.length} training(s) to ${userIds.length} participant(s)?`)) {
                return;
            }

            const $form = $('#bulk-attach-form');
            const $inputs = $('#bulk-user-inputs').empty();
            userIds.forEach(function (userId) {
                $('<input>', { type: 'hidden', name: 'user_ids[]', value: userId }).appendTo($inputs);
            });
            programIds.forEach(function (programId) {
                $('<input>', { type: 'hidden', name: 'program_ids[]', value: programId }).appendTo($inputs);
            });
            $form.submit();
        });
    });
</script>
@endsection
