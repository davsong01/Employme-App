@php
    $selectedProgramId = request('program_id');
    $selectedProgramName = $selectedProgramId
        ? optional($programs->firstWhere('id', $selectedProgramId))->p_name
        : null;
@endphp
@extends('dashboard.admin.index')

@section('title', 'Certificates')

@section('css')
<style>
    .certificates-dashboard-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, .06);
    }

    .certificates-dashboard-card .table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .08em;
        color: #64748b;
        border-bottom: 0;
    }

    .certificates-dashboard-card .table tbody td {
        vertical-align: middle;
    }

    .certificates-dashboard-card .table {
        font-size: .82rem;
    }

    .certificates-dashboard-card .table th,
    .certificates-dashboard-card .table td {
        padding: .7rem .75rem;
    }

    .certificate-micro {
        font-size: .78rem;
        line-height: 1.35;
    }

    .certificate-stack {
        display: flex;
        flex-direction: column;
        gap: .2rem;
    }

    .certificate-stack.align-start {
        align-items: flex-start;
    }

    .certificate-link-copy {
        padding: .22rem .5rem;
        font-size: .72rem;
    }

    .certificate-inline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        align-items: center;
    }

    .certificate-bulk-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    .certificate-bulk-toolbar .btn,
    .certificate-bulk-toolbar .badge {
        border-radius: 999px;
    }

    .certificate-bulk-toolbar .dropdown-menu {
        min-width: 180px;
    }

    .certificate-pill {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        border-radius: 999px;
        padding: .2rem .55rem;
        font-size: .68rem;
        font-weight: 700;
        line-height: 1;
    }

    .certificate-pill.success {
        background: #dcfce7;
        color: #166534;
    }

    .certificate-pill.danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .certificate-preview-wrap {
        position: relative;
        min-height: 60vh;
    }

    .certificate-preview-spinner {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 250, 252, .72);
        z-index: 2;
    }

    .certificate-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: 999px;
        padding: .3rem .75rem;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1;
    }

    .certificate-chip.enabled {
        background: #dcfce7;
        color: #166534;
    }

    .certificate-chip.disabled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .certificate-chip.training {
        background: #eef2ff;
        color: #4338ca;
    }

    #certificate-program-modal .select2-container {
        z-index: 1065;
    }

    @media (max-width: 767.98px) {
        .certificates-dashboard-card .table-responsive {
            overflow: visible;
        }

        .certificates-dashboard-card .table thead {
            display: none;
        }

        .certificates-dashboard-card .table,
        .certificates-dashboard-card .table tbody,
        .certificates-dashboard-card .table tr,
        .certificates-dashboard-card .table td {
            display: block;
            width: 100%;
        }

        .certificates-dashboard-card .table tr {
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
        }

        .certificates-dashboard-card .table td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
            border: 0;
            border-bottom: 1px solid #f1f5f9;
            padding: .8rem .95rem;
            text-align: left !important;
        }

        .certificates-dashboard-card .table td:last-child {
            border-bottom: 0;
        }

        .certificates-dashboard-card .table td::before {
            content: attr(data-label);
            flex: 0 0 42%;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            font-weight: 700;
        }

        .certificates-dashboard-card .table td[data-label=""]::before,
        .certificates-dashboard-card .table td[data-label="#"]::before {
            content: '';
            flex-basis: 0;
        }

        .certificates-dashboard-card .table td[data-label="Name"],
        .certificates-dashboard-card .table td[data-label="Test Details"],
        .certificates-dashboard-card .table td[data-label="ReGenCert"],
        .certificates-dashboard-card .table td[data-label="Uploaded"],
        .certificates-dashboard-card .table td[data-label="Actions"] {
            flex-direction: column;
        }

        .certificates-dashboard-card .table td[data-label="Name"]::before,
        .certificates-dashboard-card .table td[data-label="Test Details"]::before,
        .certificates-dashboard-card .table td[data-label="ReGenCert"]::before,
        .certificates-dashboard-card .table td[data-label="Uploaded"]::before,
        .certificates-dashboard-card .table td[data-label="Actions"]::before {
            margin-bottom: .35rem;
        }

        .certificates-dashboard-card .table td[data-label="Actions"] .d-inline-flex,
        .certificates-dashboard-card .table td[data-label="ReGenCert"] .certificate-stack,
        .certificates-dashboard-card .table td[data-label="Test Details"] .certificate-stack,
        .certificates-dashboard-card .table td[data-label="Uploaded"] .certificate-micro,
        .certificates-dashboard-card .table td[data-label="Name"] .certificate-stack {
            width: 100%;
        }

        .certificates-dashboard-card .table td[data-label="Test Details"] .certificate-stack.align-start {
            width: 100%;
        }

        .certificates-dashboard-card .table td[data-label="Actions"] .d-inline-flex {
            justify-content: flex-start !important;
        }

        .certificate-link-copy {
            width: 100%;
            justify-content: center;
        }

        .certificate-inline-actions {
            width: 100%;
        }

        .certificate-bulk-toolbar {
            width: 100%;
        }

        .certificate-bulk-toolbar .btn,
        .certificate-bulk-toolbar .badge,
        .certificate-bulk-toolbar .dropdown {
            width: 100%;
        }

        .certificate-bulk-toolbar .dropdown-toggle {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Certificates</h1>
                            <p class="text-muted mb-0">Review, filter, enable, disable, download, and remove certificates from one dashboard.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="btn btn-primary"
                                data-certificate-flow="generate"
                                data-bs-toggle="modal"
                                data-bs-target="#certificate-program-modal"
                            >
                                <i class="fa fa-plus me-1"></i> Add New Certificate
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-danger"
                                data-certificate-flow="duplicates"
                                data-bs-toggle="modal"
                                data-bs-target="#certificate-program-modal"
                            >
                                <i class="fa fa-clone me-1"></i> Clear Duplicates
                            </button>
                            <a href="{{ route('certificates.regeneration.requests') }}" class="btn btn-outline-info">
                                <i class="fa fa-refresh me-1"></i> Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Certificates in view</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ number_format($summary['total']) }}</div>
                            <div class="text-muted small">Matching your filters</div>
                        </div>
                        <div class="crm-metric-icon bg-primary-subtle text-primary">
                            <i class="fa fa-certificate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Enabled</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ number_format($summary['enabled']) }}</div>
                            <div class="text-muted small">Visible to participants</div>
                        </div>
                        <div class="crm-metric-icon bg-success-subtle text-success">
                            <i class="fa fa-toggle-on"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Disabled</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ number_format($summary['disabled']) }}</div>
                            <div class="text-muted small">Temporarily hidden</div>
                        </div>
                        <div class="crm-metric-icon bg-danger-subtle text-danger">
                            <i class="fa fa-toggle-off"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Pending requests</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ number_format($summary['pending_requests']) }}</div>
                            <div class="text-muted small">{{ number_format($summary['programs']) }} training(s) in scope</div>
                        </div>
                        <div class="crm-metric-icon bg-warning-subtle text-warning">
                            <i class="fa fa-clock-o"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Search</label>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, email, phone, program, or certificate number">
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Program</label>
                    <select name="program_id" class="form-select select2" data-placeholder="All trainings">
                        <option value=""></option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" @selected((string) $selectedProgramId === (string) $program->id)>
                                {{ $program->p_name }} ({{ $program->certificates_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Access</label>
                    <select name="access_status" class="form-select select2" data-placeholder="Any status">
                        <option value=""></option>
                        <option value="enabled" @selected(request('access_status') === 'enabled')>Enabled</option>
                        <option value="disabled" @selected(request('access_status') === 'disabled')>Disabled</option>
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small text-uppercase fw-semibold text-muted">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small text-uppercase fw-semibold text-muted">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <button class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm certificates-dashboard-card">
        <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
                <div>
                    <h2 class="h5 mb-1">Certificates library</h2>
                    <p class="text-muted small mb-0">Review issued certificates and manage visibility without leaving the page.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $certificates->count() }} on this page</span>
                    @if($selectedProgramName)
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $selectedProgramName }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form id="certificates-bulk-form" method="POST" action="{{ route('certificates.bulk-action') }}">
                @csrf
                <input type="hidden" name="bulk_action" id="certificates-bulk-action">
            </form>

            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 align-items-md-center mb-3">
                <div class="text-muted small">
                    Showing {{ $certificates->count() }} records on this page. Use the checkboxes for batch actions.
                </div>
                <div class="certificate-bulk-toolbar">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                        <span id="selected-certificate-count">0</span> selected
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="select-all-certificates-btn">
                        Select all on page
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-certificate-selection-btn" disabled>
                        Clear selection
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="certificates-bulk-actions" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Bulk actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="certificates-bulk-actions">
                            <li><button class="dropdown-item" type="button" data-certificate-action="enable">Enable selected</button></li>
                            <li><button class="dropdown-item" type="button" data-certificate-action="disable">Disable selected</button></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle crm-table crm-mobile-stack mb-0" id="certificates-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 24px;">
                                <input type="checkbox" id="select-all-certificates" class="form-check-input">
                            </th>
                            <th style="width: 48px;">#</th>
                            <th>Name</th>
                            <th>ReGenCert</th>
                            <th>Test Details</th>
                            <th>Access</th>
                            <th>Issued</th>
                            <th>Uploaded</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $certificate)
                            @php
                                $accessEnabled = $certificate->show_certificate() === 'Enabled';
                                $results = $certificate->scores();
                                $scoreSettings = $certificate->program?->scoresettings;
                                $certificateHistoryCount = $certificate->certificateHistory?->count() ?? 0;
                                $uploadedBy = $certificate->uploadedBy?->name ?? 'System';
                                $uploadedAt = $certificate->uploaded_at
                                    ? $certificate->uploaded_at->format('d/m/Y h:i A')
                                    : optional($certificate->created_at)->format('d/m/Y h:i A');
                                $verificationLink = $certificate->certificate_number
                                    ? env('WAACSP_CERTIFICATE_VERIFICATION_LINK') . '?certificate_number=' . $certificate->certificate_number
                                    : null;
                                $verificationLogsCount = $certificate->verification_logs_count ?? 0;
                            @endphp
                            <tr>
                                <td data-label="">
                                    <input type="checkbox" class="form-check-input certificate-row-check" name="certificate_ids[]" value="{{ $certificate->id }}" form="certificates-bulk-form">
                                </td>
                                <td data-label="#">{{ paginationIndex($certificates, $loop) }}</td>
                                <td data-label="Name">
                                    <div class="certificate-stack align-start">
                                        <div class="fw-semibold">{{ $certificate->user?->name ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $certificate->user?->email }}</div>
                                        <div class="text-muted small">{{ $certificate->user?->staffID }}</div>
                                        <div class="certificate-inline-actions">
                                            @if($certificate->certificate_number)
                                                <span class="badge bg-light text-dark rounded-pill">{{ $certificate->certificate_number }}</span>
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary btn-sm certificate-link-copy"
                                                    onclick="copyCertificateLink('verification-link-{{ $certificate->id }}', 'copy-status-{{ $certificate->id }}')"
                                                >
                                                    <i class="fa fa-copy me-1"></i> Copy verification link
                                                </button>
                                            @endif
                                            @if($certificate->file)
                                                <a class="btn btn-outline-info btn-sm certificate-link-copy" href="#" onclick="loadCertificatePreview(event, '/admin/certificate-preview/{{ $certificate->file }}')">
                                                    <i class="fa fa-eye me-1"></i> Preview
                                                </a>
                                            @endif
                                            @if($certificate->certificate_number)
                                                <a
                                                    class="btn btn-outline-secondary btn-sm certificate-link-copy"
                                                    href="{{ route('certificate.verification.logs', ['certificate_number' => $certificate->certificate_number]) }}"
                                                >
                                                    <i class="fa fa-history me-1"></i> Logs
                                                    <span class="badge bg-light text-dark rounded-pill ms-1">{{ $verificationLogsCount }}</span>
                                                </a>
                                            @endif
                                        </div>
                                        @if($certificate->certificate_number)
                                            <input type="hidden" id="verification-link-{{ $certificate->id }}" value="{{ $verificationLink }}">
                                            <small id="copy-status-{{ $certificate->id }}" class="text-success d-none"></small>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="ReGenCert">
                                    <div class="certificate-stack">
                                        <span class="badge bg-light text-dark rounded-pill">
                                            {{ $certificate->allow_new_certificate_request ? 'Regen allowed' : 'Regen locked' }}
                                        </span>
                                        @if($certificate->allow_new_certificate_request)
                                            <a class="btn btn-outline-danger btn-sm" href="{{ route('new.certificate.generation', ['certificate_id' => $certificate->id, 'status' => 0]) }}" onclick="return confirm('Disable new certificate generation for this participant?')">
                                                <i class="fa fa-toggle-on me-1"></i> Disable
                                            </a>
                                        @else
                                            <a class="btn btn-outline-success btn-sm" href="{{ route('new.certificate.generation', ['certificate_id' => $certificate->id, 'status' => 1]) }}" onclick="return confirm('Enable new certificate generation for this participant?')">
                                                <i class="fa fa-toggle-off me-1"></i> Enable
                                            </a>
                                        @endif
                                        @if($certificateHistoryCount > 0)
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#historyModal-{{ $certificate->id }}">
                                                <i class="fa fa-history me-1"></i> {{ $certificateHistoryCount }} history
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Test Details">
                                    <div class="certificate-stack">
                                        <span class="badge bg-light text-dark rounded-pill">
                                            <i class="fa fa-graduation-cap me-1"></i>{{ $certificate->program?->p_name ?? 'Program has been trashed' }}
                                        </span>
                                        @php
                                            $programCertificateEnabled = (int) ($certificate->program?->show_certificate ?? 0) === 1;
                                        @endphp
                                        <span class="certificate-pill {{ $programCertificateEnabled ? 'success' : 'danger' }}">
                                            {{ $programCertificateEnabled ? 'Program certificate enabled' : 'Program certificate disabled' }}
                                        </span>
                                    </div>
                                    @if(! empty($scoreSettings))
                                        <div class="certificate-micro mt-2">
                                            @if(isset($scoreSettings->certification) && $scoreSettings->certification > 0)
                                                <div><strong>Certification:</strong> {{ $results['certification_test_score'] ?? 0 }}%</div>
                                            @endif
                                            @if(isset($scoreSettings->class_test) && $scoreSettings->class_test > 0)
                                                <div><strong>Class Tests:</strong> {{ $results['class_test_score'] ?? 0 }}%</div>
                                            @endif
                                            @if(isset($scoreSettings->role_play) && $scoreSettings->role_play > 0)
                                                <div><strong>Role Play:</strong> {{ $results['role_play_score'] ?? 0 }}%</div>
                                            @endif
                                            @if(isset($scoreSettings->email) && $scoreSettings->email > 0)
                                                <div><strong>Email:</strong> {{ $results['email_test_score'] ?? 0 }}%</div>
                                            @endif
                                            <div class="fw-semibold mt-1" style="color:{{ ($results['total'] ?? 0) < ($scoreSettings->passmark ?? 0) ? 'red' : 'green' }}">
                                                Total: {{ $results['total'] ?? 0 }}%
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">No test settings</span>
                                    @endif
                                </td>
                                <td data-label="Access">
                                    @if(! $accessEnabled)
                                        <span class="certificate-chip disabled">
                                            <i class="fa fa-toggle-off"></i> Disabled
                                        </span>
                                    @else
                                        <span class="certificate-chip enabled">
                                            <i class="fa fa-toggle-on"></i> Enabled
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Issued">{{ !empty($certificate->date_issued) ? $certificate->date_issued : 'N/A' }}</td>
                                <td data-label="Uploaded">
                                    <div class="certificate-micro">
                                        <div class="fw-semibold">{{ $uploadedBy }}</div>
                                        <div class="text-muted">{{ $uploadedAt }}</div>
                                    </div>
                                </td>
                                <td class="text-end" data-label="Actions">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                        @if(! $accessEnabled)
                                            <a
                                                data-bs-toggle="tooltip"
                                                title="Enable certificate"
                                                class="btn btn-outline-success btn-sm"
                                                href="{{ route('certificate.status', ['program_id' => $certificate->program_id, 'user_id' => $certificate->user_id, 'status' => 1, 'certificate_id' => $certificate->id]) }}"
                                                onclick="return confirm('Enable this certificate for the participant?')"
                                            >
                                                <i class="fa fa-toggle-on"></i>
                                            </a>
                                        @else
                                            <a
                                                data-bs-toggle="tooltip"
                                                title="Disable certificate"
                                                class="btn btn-outline-warning btn-sm"
                                                href="{{ route('certificate.status', ['program_id' => $certificate->program_id, 'user_id' => $certificate->user_id, 'status' => 0, 'certificate_id' => $certificate->id]) }}"
                                                onclick="return confirm('Disable this certificate for the participant?')"
                                            >
                                                <i class="fa fa-toggle-off"></i>
                                            </a>
                                        @endif
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Download certificate" class="btn btn-outline-primary btn-sm" href="{{ url('download-certificate/' . $certificate->file) }}">
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete certificate">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted mb-2">No certificates found.</div>
                                    <div class="small text-muted">Try adjusting the filters or issue a new certificate.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($certificates, 'links'))
                <div class="mt-4">
                    {{ $certificates->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="certificatePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Certificate Preview</h5>
                    <div class="text-muted small">Inline preview for the selected certificate file.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="certificate-preview-wrap bg-light">
                    <div id="certificate-preview-spinner" class="certificate-preview-spinner">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
                            <div class="text-muted small mt-3">Loading preview...</div>
                        </div>
                    </div>
                    <iframe id="certificate-preview-frame" title="Certificate Preview" class="border-0 w-100" style="min-height:60vh;" src="about:blank"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="certificate-program-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Select a program</h5>
                    <div class="text-muted small">Choose the training to preserve program-specific certificate actions.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label fw-semibold">Program</label>
                <select id="certificate-program-select" class="form-select certificate-program-select" data-placeholder="Select a training">
                    <option value=""></option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->p_name }} ({{ $program->certificates_count }})</option>
                    @endforeach
                </select>
                {{-- <div class="alert alert-info border-0 mt-3 mb-0">
                    This will open the selected training route, such as <strong>/admin/suser/117</strong>, so certificate generation and cleanup stay program-aware.
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="certificate-program-go-btn">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.loadCertificatePreview = function (event, filePath) {
            event.preventDefault();

            const modalEl = document.getElementById('certificatePreviewModal');
            const frameEl = document.getElementById('certificate-preview-frame');
            const spinnerEl = document.getElementById('certificate-preview-spinner');

            if (!modalEl || !frameEl) {
                return;
            }

            if (spinnerEl) {
                spinnerEl.style.display = 'flex';
            }

            frameEl.onload = function () {
                if (spinnerEl) {
                    spinnerEl.style.display = 'none';
                }
            };

            const cacheBust = new Date().getTime();
            frameEl.src = filePath + '?nocache=' + cacheBust;

            if (window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                $('#certificatePreviewModal').modal('show');
            }
        };

        const previewModalEl = document.getElementById('certificatePreviewModal');
        const previewFrameEl = document.getElementById('certificate-preview-frame');
        const previewSpinnerEl = document.getElementById('certificate-preview-spinner');

        if (previewModalEl && previewFrameEl) {
            previewModalEl.addEventListener('hidden.bs.modal', function () {
                previewFrameEl.src = 'about:blank';
                if (previewSpinnerEl) {
                    previewSpinnerEl.style.display = 'flex';
                }
            });
        }

        const rowChecks = Array.from(document.querySelectorAll('.certificate-row-check'));
        const selectAll = document.getElementById('select-all-certificates');
        const selectAllBtn = document.getElementById('select-all-certificates-btn');
        const clearBtn = document.getElementById('clear-certificate-selection-btn');
        const bulkButton = document.getElementById('certificates-bulk-actions');
        const selectedCountLabel = document.getElementById('selected-certificate-count');
        const bulkActionInput = document.getElementById('certificates-bulk-action');
        const bulkForm = document.getElementById('certificates-bulk-form');
        const bulkActionItems = document.querySelectorAll('[data-certificate-action]');
        const flowButtons = document.querySelectorAll('[data-certificate-flow]');
        const programSelect = document.getElementById('certificate-program-select');
        const programGoBtn = document.getElementById('certificate-program-go-btn');
        const certificateProgramModal = document.getElementById('certificate-program-modal');
        const programRouteBase = @json(url('admin/suser'));
        const duplicatesRouteBase = @json(url('admin/certificate-clear-duplicate'));
        let activeCertificateFlow = 'generate';

        window.copyCertificateLink = function (inputId, statusId) {
            const input = document.getElementById(inputId);
            const status = document.getElementById(statusId);

            if (!input) {
                return;
            }

            const copyText = input.value || '';

            const done = function () {
                if (status) {
                    status.textContent = 'Verification link copied';
                    status.classList.remove('d-none');
                    setTimeout(function () {
                        status.classList.add('d-none');
                    }, 1800);
                }
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(copyText).then(done).catch(function () {
                    input.type = 'text';
                    input.select();
                    document.execCommand('copy');
                    input.type = 'hidden';
                    done();
                });
                return;
            }

            input.type = 'text';
            input.select();
            document.execCommand('copy');
            input.type = 'hidden';
            done();
        };

        function selectedIds() {
            return rowChecks.filter(cb => cb.checked).map(cb => cb.value);
        }

        function updateSelectionState() {
            const count = selectedIds().length;
            if (selectedCountLabel) {
                selectedCountLabel.textContent = count;
            }
            if (clearBtn) {
                clearBtn.disabled = count === 0;
            }
            if (bulkButton) {
                bulkButton.disabled = count === 0;
            }
            if (selectAll) {
                selectAll.checked = rowChecks.length > 0 && count === rowChecks.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowChecks.forEach(cb => cb.checked = selectAll.checked);
                updateSelectionState();
            });
        }

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function () {
                rowChecks.forEach(cb => cb.checked = true);
                updateSelectionState();
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                rowChecks.forEach(cb => cb.checked = false);
                updateSelectionState();
            });
        }

        rowChecks.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSelectionState);
        });

        bulkActionItems.forEach(function (item) {
            item.addEventListener('click', function () {
                const action = item.getAttribute('data-certificate-action');
                const ids = selectedIds();

                if (!ids.length) {
                    return;
                }

                if (!confirm((action === 'enable' ? 'Enable' : 'Disable') + ' the selected certificates?')) {
                    return;
                }

                if (bulkActionInput) {
                    bulkActionInput.value = action;
                }

                if (bulkForm) {
                    bulkForm.querySelectorAll('input[name="certificate_ids[]"]').forEach(function (input) {
                        input.remove();
                    });

                    ids.forEach(function (id) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'certificate_ids[]';
                        input.value = id;
                        bulkForm.appendChild(input);
                    });

                    bulkForm.submit();
                }
            });
        });

        flowButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activeCertificateFlow = button.getAttribute('data-certificate-flow') || 'generate';
            });
        });

        if (programSelect && window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            const initProgramSelect = function () {
                const $select = window.jQuery(programSelect);

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
                    dropdownParent: window.jQuery('#certificate-program-modal'),
                    placeholder: $select.data('placeholder') || 'Select a training',
                    allowClear: true
                });
            };

            if (certificateProgramModal) {
                certificateProgramModal.addEventListener('shown.bs.modal', initProgramSelect);
                certificateProgramModal.addEventListener('hidden.bs.modal', function () {
                    const $select = window.jQuery(programSelect);
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }
                });
            } else {
                initProgramSelect();
            }
        }

        if (programGoBtn) {
            programGoBtn.addEventListener('click', function () {
                const programId = programSelect ? programSelect.value : '';

                if (!programId) {
                    alert('Please select a program first.');
                    return;
                }

                const targetBase = activeCertificateFlow === 'duplicates'
                    ? duplicatesRouteBase
                    : programRouteBase;

                window.location.href = targetBase + '/' + programId;
            });
        }

        updateSelectionState();
    });
</script>
@endsection
