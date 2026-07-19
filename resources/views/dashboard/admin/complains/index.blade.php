@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') . ' CRM Desk' }}
@endsection
@section('content')
@php
    $totalComplains = $complains->count();
    $resolvedCount = $complains->where('status', 'Resolved')->count();
    $inProgressCount = $complains->where('status', 'In Progress')->count();
    $pendingCount = $complains->where('status', 'Pending')->count();
    $overdueCount = $complains->filter(function ($complain) {
        return $complain->follow_up_at && optional($complain->follow_up_at)->isPast() && $complain->status !== 'Resolved';
    })->count();
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">CRM Desk</span>
                            <h1 class="h3 fw-bold mb-2">{{ $training->p_name ?? 'CRM' }}</h1>
                            <p class="text-muted mb-0">Track customer cases, ownership, follow-up dates, and resolution status in one place.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if($training->hascrm == 1)
                                <a href="{{ route('complains.create', ['p_id' => $training->id]) }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-1"></i> Log new case
                                </a>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">CRM disabled for new cases</span>
                            @endif
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalComplains }} total</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Total cases</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ $totalComplains }}</div>
                            <div class="text-muted small">Logged for this training</div>
                        </div>
                        <div class="crm-metric-icon bg-primary-subtle text-primary">
                            <i class="fa fa-list-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Resolved</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ $resolvedCount }}</div>
                            <div class="text-muted small">Closed and completed</div>
                        </div>
                        <div class="crm-metric-icon bg-success-subtle text-success">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">In progress</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ $inProgressCount }}</div>
                            <div class="text-muted small">Being worked on now</div>
                        </div>
                        <div class="crm-metric-icon bg-warning-subtle text-warning">
                            <i class="fa fa-spinner"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Follow-up due</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ $overdueCount }}</div>
                            <div class="text-muted small">Needs attention</div>
                        </div>
                        <div class="crm-metric-icon bg-danger-subtle text-danger">
                            <i class="fa fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h5 mb-1">All cases</h2>
                    <p class="text-muted small mb-0">Review, resolve, or delete cases from the training CRM.</p>
                </div>
                <div class="d-flex gap-2">
                    @if($training->hascrm == 1)
                        <a href="{{ route('complains.create', ['p_id' => $training->id]) }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus me-1"></i> Add case
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3 crm-filters">
                <div class="col-lg-5">
                    <label for="crm-search" class="form-label small text-uppercase fw-semibold text-muted">Search</label>
                    <input type="search" id="crm-search" class="form-control" placeholder="Search by ticket, customer, subject, or note">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label for="crm-status-filter" class="form-label small text-uppercase fw-semibold text-muted">Status</label>
                    <select id="crm-status-filter" class="form-select">
                        <option value="">All</option>
                        <option value="Resolved">Resolved</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label for="crm-priority-filter" class="form-label small text-uppercase fw-semibold text-muted">Priority</label>
                    <select id="crm-priority-filter" class="form-select">
                        <option value="">All</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label for="crm-followup-filter" class="form-label small text-uppercase fw-semibold text-muted">Follow-up</label>
                    <select id="crm-followup-filter" class="form-select">
                        <option value="">All</option>
                        <option value="overdue">Overdue</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="empty">Not set</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Customer</th>
                            <th>Case</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Follow-up</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complains as $complain)
                            @php
                                $status = $complain->status ?? 'Pending';
                                $priority = $complain->priority ?? 'Medium';
                                $statusBadge = match ($status) {
                                    'Resolved' => 'success',
                                    'In Progress' => 'warning',
                                    'Pending' => 'secondary',
                                    default => 'dark',
                                };
                                $priorityBadge = match ($priority) {
                                    'High' => 'danger',
                                    'Medium' => 'warning',
                                    'Low' => 'success',
                                    default => 'secondary',
                                };
                                $headline = $complain->subject ?: $complain->issues ?: 'Case note';
                                $snippet = \Illuminate\Support\Str::limit(trim(strip_tags($complain->content ?? '')), 95);
                            @endphp
                            <tr>
                                <td data-label="Ticket">
                                    <div class="fw-semibold">EMPL000{{ $complain->id }}</div>
                                    <div class="text-muted small">{{ optional($complain->created_at)->format('d M Y') }}</div>
                                </td>
                                <td data-label="Customer">
                                    <div class="fw-semibold">{{ $complain->name ?? 'Not set' }}</div>
                                    <div class="text-muted small">{{ $complain->email ?? '' }}</div>
                                    <div class="text-muted small">{{ $complain->phone ?? '' }}</div>
                                </td>
                                <td data-label="Case">
                                    <div class="fw-semibold">{{ $headline }}</div>
                                    <div class="text-muted small">{{ $snippet ?: 'No case description yet.' }}</div>
                                    @if($complain->teamlead)
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark rounded-pill">Owner: {{ $complain->teamlead }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }} rounded-pill px-3 py-2">{{ $status }}</span>
                                </td>
                                <td data-label="Priority">
                                    <span class="badge bg-{{ $priorityBadge }}-subtle text-{{ $priorityBadge }} rounded-pill px-3 py-2">{{ $priority }}</span>
                                </td>
                                <td data-label="Follow-up">
                                    @if($complain->follow_up_at)
                                        <div class="fw-semibold">{{ optional($complain->follow_up_at)->format('d M Y') }}</div>
                                        <div class="text-muted small">
                                            {{ optional($complain->follow_up_at)->isPast() && $status !== 'Resolved' ? 'Due now' : 'Scheduled follow-up' }}
                                        </div>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td class="text-end" data-label="Actions">
                                    <div class="d-none d-md-inline-flex flex-wrap justify-content-end gap-1">
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('complains.edit', $complain->id) }}" data-bs-toggle="tooltip" title="Open case">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if($complain->status !== 'Resolved')
                                            <a class="btn btn-outline-success btn-sm" href="{{ route('crm.resolved', $complain->id) }}" data-bs-toggle="tooltip" title="Mark resolved">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        @endif
                                        @if(checkRoleHas(['Admin']))
                                            <form class="m-0" action="{{ route('complains.destroy', $complain->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Delete case">
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
                                            <li>
                                                <a class="dropdown-item" href="{{ route('complains.edit', $complain->id) }}">
                                                    <i class="fa fa-eye me-2"></i>Open case
                                                </a>
                                            </li>
                                            @if($complain->status !== 'Resolved')
                                                <li>
                                                    <a class="dropdown-item text-success" href="{{ route('crm.resolved', $complain->id) }}">
                                                        <i class="fa fa-check me-2"></i>Mark resolved
                                                    </a>
                                                </li>
                                            @endif
                                            @if(checkRoleHas(['Admin']))
                                                <li>
                                                    <form class="m-0" action="{{ route('complains.destroy', $complain->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                                        {{ csrf_field() }}
                                                        {{ method_field('DELETE') }}
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash me-2"></i>Delete case
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
@section('extra-scripts')
<script>
    $(document).ready(function () {
        const crmTable = $('#zero_config').DataTable();

        $.fn.dataTable.ext.search.push(function (settings, data) {
            if (settings.nTable.id !== 'zero_config') {
                return true;
            }

            const statusFilter = $('#crm-status-filter').val();
            const priorityFilter = $('#crm-priority-filter').val();
            const followUpFilter = $('#crm-followup-filter').val();

            const statusText = (data[3] || '').trim();
            const priorityText = (data[4] || '').trim();
            const followUpText = (data[5] || '').trim();

            if (statusFilter && statusText.indexOf(statusFilter) === -1) {
                return false;
            }

            if (priorityFilter && priorityText.indexOf(priorityFilter) === -1) {
                return false;
            }

            if (followUpFilter === 'overdue' && followUpText.indexOf('Due now') === -1) {
                return false;
            }

            if (followUpFilter === 'scheduled' && followUpText.indexOf('Scheduled follow-up') === -1) {
                return false;
            }

            if (followUpFilter === 'empty' && followUpText.indexOf('Not set') === -1) {
                return false;
            }

            return true;
        });

        $('#crm-search').on('keyup change', function () {
            crmTable.search(this.value).draw();
        });

        $('#crm-status-filter, #crm-priority-filter, #crm-followup-filter').on('change', function () {
            crmTable.draw();
        });
    });
</script>
@endsection
@endsection
