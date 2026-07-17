@extends('dashboard.student.trainingsindex')
@section('title')
{{ config('app.name') .' CRM Desk' }}
@endsection
@section('content')
@php
    $totalComplains = $complains->count();
    $resolvedCount = $resolvedComplains ?? 0;
    $inProgressCount = $InProgressComplains ?? 0;
    $pendingCount = $pendingComplains ?? 0;
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">My CRM Desk</span>
                            <h1 class="h3 fw-bold mb-2">{{ strtoupper($program->p_name ?? 'CRM') }}</h1>
                            <p class="text-muted mb-0">Track the cases you have raised, what is pending, and what has already been resolved.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('participant.complains.create', ['p_id' => $program->id]) }}" class="btn btn-primary">
                                <i class="fa fa-plus me-1"></i> Add new case
                            </a>
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
                    <div class="display-6 fw-bold mb-0">{{ $totalComplains }}</div>
                    <div class="text-muted small">Logged by you</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Resolved</div>
                    <div class="display-6 fw-bold mb-0">{{ $resolvedCount }}</div>
                    <div class="text-muted small">Closed cases</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">In progress</div>
                    <div class="display-6 fw-bold mb-0">{{ $inProgressCount }}</div>
                    <div class="text-muted small">Being handled now</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Pending</div>
                    <div class="display-6 fw-bold mb-0">{{ $pendingCount }}</div>
                    <div class="text-muted small">Waiting for action</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h2 class="h5 mb-1">Your cases</h2>
                    <p class="text-muted small mb-0">Open a case to review updates, feedback, or supervisor notes.</p>
                </div>
                <a href="{{ route('participant.complains.create', ['p_id' => $program->id]) }}" class="btn btn-outline-primary">
                    <i class="fa fa-plus me-1"></i> Add case
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Date created</th>
                            <th>Date updated</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>SLA</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complains as $complain)
                            @php
                                $status = $complain->status ?? 'Pending';
                                $statusBadge = match ($status) {
                                    'Resolved' => 'success',
                                    'In Progress' => 'warning',
                                    'Pending' => 'secondary',
                                    default => 'dark',
                                };
                                $headline = $complain->subject ?: $complain->issues ?: 'Case note';
                            @endphp
                            <tr>
                                <td data-label="Ticket">
                                    <div class="fw-semibold">EMPL000{{ $complain->id }}</div>
                                </td>
                                <td data-label="Date created">{{ optional($complain->created_at)->format('d M Y') }}</td>
                                <td data-label="Date updated">{{ optional($complain->updated_at)->format('d M Y') }}</td>
                                <td data-label="Subject">
                                    <div class="fw-semibold">{{ $headline }}</div>
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit(trim(strip_tags($complain->content ?? '')), 90) }}</div>
                                </td>
                                <td data-label="Status">
                                    <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }} rounded-pill px-3 py-2">{{ $status }}</span>
                                </td>
                                <td data-label="SLA">{{ $complain->sla }} {{ $complain->sla ? 'hours' : '' }}</td>
                                <td class="text-end" data-label="Action">
                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('participant.complains.edit', ['complain' => $complain->id, 'p_id' => $program]) }}" data-bs-toggle="tooltip" title="Open case">
                                        <i class="fa fa-eye"></i>
                                    </a>
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
