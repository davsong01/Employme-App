@extends('dashboard.admin.index')

@section('title', 'Participant Evidence - '.$user->name)

@php
    $evidenceSetting = \App\Models\Settings::first();
    $evidenceLogo = $evidenceSetting?->logo ?: 'assets/images/logo.png';
    $evidenceLogoUrl = asset(ltrim($evidenceLogo, '/'));
@endphp

@section('css')
<style>
    .evidence-page { color: #273444; }
    .evidence-header { background: linear-gradient(135deg, #e9e7ff, #eefaff); border-left: 5px solid #6558d3 !important; }
    .evidence-label { color: #6c757d; font-size: .72rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
    .evidence-value { font-weight: 600; }
    .evidence-section { break-inside: avoid; }
    .evidence-section:nth-of-type(2) { border-top: 4px solid #0d9488 !important; }
    .evidence-section:nth-of-type(3n) { border-top: 4px solid #f59e0b !important; }
    .evidence-section:nth-of-type(4n) { border-top: 4px solid #e11d48 !important; }
    .evidence-section .card-header { background: linear-gradient(90deg, #f8fafc, #fff) !important; }
    .evidence-section .card-header h2 { color: #334155; }
    .evidence-table th { white-space: nowrap; }
    .evidence-table td, .evidence-table th { vertical-align: top; }
    .evidence-table thead th { background: #f1f5f9; color: #475569; border-bottom: 2px solid #cbd5e1; }
    .evidence-table tbody tr:nth-child(even) { background: #fbfdff; }
    .evidence-table tbody tr:hover { background: #f0fdfa; }
    .evidence-page .border.rounded { background: linear-gradient(135deg, #f8f7ff, #f0fdfa); border-color: #c7d2fe !important; }
    .evidence-page .h4 { color: #6558d3; }
    .evidence-page .btn-primary { background-color: #6558d3; border-color: #6558d3; }
    .evidence-page .btn-primary:hover { background-color: #5146b8; border-color: #5146b8; }
    .empty-state { color: #6c757d; font-style: italic; }
    @media print {
        @page { size: A4; margin: 12mm; }
        html, body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
        #main-wrapper, .page-wrapper { margin: 0 !important; padding: 0 !important; }
        .topbar, nav, aside, .left-sidebar, .sidebar, .actions, .no-print, footer { display: none !important; }
        .evidence-page { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
        .evidence-header { background: #eef4ff !important; }
        .evidence-table { font-size: 9px; }
        .evidence-table th, .evidence-table td { padding: 4px !important; }
        .table-responsive { overflow: visible !important; }
        .evidence-section { page-break-inside: avoid; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid evidence-page">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back to participants</a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa fa-print me-1"></i> Print / Save PDF</button>
    </div>

    <div class="card border-0 shadow-sm mb-4 evidence-header">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start gap-3">
                <div>
                    <div class="evidence-label mb-1">Participant evidence record</div>
                    <h1 class="h3 mb-1">{{ $user->name }}</h1>
                    <div class="text-muted">Generated {{ now()->format('d M Y, h:i A') }}</div>
                </div>
                <img src="{{ $evidenceLogoUrl }}" alt="{{ config('app.name') }}" style="max-width: 170px; max-height: 60px; object-fit: contain;">
            </div>
            <hr>
            <div class="row g-3">
                <div class="col-md-2"><div class="evidence-label">Email</div><div class="evidence-value">{{ $user->email ?: 'N/A' }}</div></div>
                <div class="col-md-2"><div class="evidence-label">Phone</div><div class="evidence-value">{{ $user->phone ?: 'N/A' }}</div></div>
                <div class="col-md-2"><div class="evidence-label">Staff ID</div><div class="evidence-value">{{ $user->staffID ?: 'N/A' }}</div></div>
                <div class="col-md-2"><div class="evidence-label">Status</div><div class="evidence-value">{{ ucfirst($user->status ?: 'active') }}</div></div>
                <div class="col-md-2"><div class="evidence-label">Last login</div><div class="evidence-value">{{ $user->last_login ? date('d M Y, h:i A', strtotime($user->last_login)) : 'Never' }}</div></div>
                <div class="col-md-2"><div class="evidence-label">Registered</div><div class="evidence-value">{{ optional($user->created_at)->format('d M Y') ?: 'N/A' }}</div></div>
            </div>
            @if(!empty($user->metadata))
                <div class="mt-3"><div class="evidence-label">Additional participant metadata</div><div>{{ is_array($user->metadata) ? json_encode($user->metadata, JSON_PRETTY_PRINT) : $user->metadata }}</div></div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 evidence-section">
        <div class="card-header bg-white"><h2 class="h5 mb-0">Programs and registration history</h2></div>
        <div class="card-body p-0">
            @forelse($programs as $program)
                @php
                    $programTransactions = $transactions->where('program_id', $program->id);
                    $registration = $programTransactions->sortBy('created_at')->first();
                @endphp
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between gap-3">
                        <div><h3 class="h6 mb-1">{{ $program->p_name }}</h3></div>
                        <div class="text-end"><div class="evidence-label">Registration date</div><strong>{{ optional($registration?->created_at)->format('d M Y, h:i A') ?: 'Attached date unavailable' }}</strong></div>
                    </div>
                    @if($programTransactions->isNotEmpty())
                        <div class="table-responsive mt-3">
                            <table class="table table-sm evidence-table mb-0">
                                <thead><tr><th>Transaction ID</th><th>Invoice ID</th><th>Amount</th><th>Balance</th><th>Date</th></tr></thead>
                                <tbody>
                                @foreach($programTransactions as $transaction)
                                    <tr>
                                        <td>
                                            @if($transaction->transid && $transaction->paymentLog)
                                                <a href="{{ route('payments.receipt.download', $transaction->paymentLog->id) }}" title="Download receipt">
                                                    {{ $transaction->transid }}
                                                </a>
                                            @elseif($transaction->transid)
                                                <span title="No receipt is linked to this participant">{{ $transaction->transid }}</span>
                                            @else
                                                {{ $transaction->transid ?: 'N/A' }}
                                            @endif
                                        </td>
                                        <td>{{ $transaction->invoice_id ?: 'N/A' }}</td>
                                        <td>{{ $transaction->currency_symbol ?: '₦' }}{{ number_format((float) ($transaction->amount ?? 0), 2) }}</td>
                                        <td>{{ $transaction->currency_symbol ?: '₦' }}{{ number_format((float) ($transaction->balance ?? 0), 2) }}</td>
                                        <td>{{ optional($transaction->created_at)->format('d M Y, h:i A') ?: 'N/A' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state small mt-2">Program is attached, but no registration transaction record is available.</div>
                    @endif
                </div>
            @empty
                <div class="p-4 empty-state">No programs are attached to this participant.</div>
            @endforelse
        </div>
    </div>

    @foreach($programs as $program)
        @php
            $programResults = $results->where('program_id', $program->id);
            $programCertificates = $certificates->where('program_id', $program->id);
            $programTransaction = $transactions->where('program_id', $program->id)->sortByDesc('created_at')->first();
            $trainingResult = $programTransaction?->training_result;
            $certificationStatus = $certificationStatuses[$program->id] ?? 'NOT TAKEN';
        @endphp
        <div class="card border-0 shadow-sm mb-4 evidence-section">
            <div class="card-header bg-white d-flex justify-content-between align-items-center gap-3">
                <h2 class="h5 mb-0">Assessments and certification — {{ $program->p_name }}</h2>
                <span class="badge {{ $certificationStatus === 'CERTIFIED' ? 'bg-success' : ($certificationStatus === 'NOT TAKEN' ? 'bg-secondary' : 'bg-danger') }}">{{ $certificationStatus }}</span>
            </div>
            <div class="card-body">
                @if($trainingResult)
                    <h3 class="h6">Recorded final training result</h3>
                    <div class="row g-2 mb-4">
                        @foreach(['class_test_score' => 'Class test', 'email_test_score' => 'Email test', 'roleplay_test_score' => 'Role play', 'crm_test_score' => 'CRM test', 'certification_test_score' => 'Certification', 'total_score' => 'Total'] as $key => $label)
                            @if(isset($trainingResult->{$key}))
                                <div class="col-6 col-md-2"><div class="border rounded p-2"><div class="evidence-label">{{ $label }}</div><strong>{{ $trainingResult->{$key} }}</strong></div></div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <h3 class="h6">Post-test / module results</h3>
                @if($programResults->isNotEmpty())
                    <div class="table-responsive mb-4">
                        <table class="table table-sm evidence-table">
                            <thead><tr><th>Module / test</th><th>Type</th><th>Class</th><th>Email</th><th>Role play</th><th>CRM</th><th>Certification</th><th>Recorded</th></tr></thead>
                            <tbody>
                            @foreach($programResults as $result)
                                <tr>
                                    <td>{{ $result->module?->title ?: 'Test result #'.$result->id }}</td>
                                    <td>{{ $result->module?->type ?: 'N/A' }}</td>
                                    <td>{{ $result->class_test_score ?? '—' }}</td>
                                    <td>{{ $result->email_test_score ?? '—' }}</td>
                                    <td>{{ $result->role_play_score ?? '—' }}</td>
                                    <td>{{ $result->crm_test_score ?? '—' }}</td>
                                    <td>{{ $result->certification_test_score ?? '—' }}</td>
                                    <td>{{ optional($result->created_at)->format('d M Y') ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state mb-4">No post-test/module results recorded.</div>
                @endif

                <h3 class="h6">Certificates</h3>
                @if($programCertificates->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm evidence-table mb-0">
                            <thead><tr><th>Certificate number</th><th>Date issued</th><th>Status</th><th>Recorded</th></tr></thead>
                            <tbody>
                            @foreach($programCertificates as $certificate)
                                @php
                                    $verificationBaseUrl = env('WAACSP_CERTIFICATE_VERIFICATION_LINK') ?: route('verify.certificate');
                                    $verificationUrl = rtrim($verificationBaseUrl, '?&') . '?certificate_number=' . urlencode($certificate->certificate_number);
                                @endphp
                                <tr>
                                    <td>
                                        @if($certificate->certificate_number)
                                            <a href="{{ $verificationUrl }}" target="_blank" rel="noopener noreferrer"><strong>{{ $certificate->certificate_number }}</strong></a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $certificate->date_issued ?: 'N/A' }}</td>
                                    <td>{{ $certificate->status ?? 'Issued' }}</td>
                                    <td>{{ optional($certificate->created_at)->format('d M Y') ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">No certificate record found for this program.</div>
                @endif
            </div>
        </div>
    @endforeach

    <div class="card border-0 shadow-sm mb-4 evidence-section">
        <div class="card-header bg-white"><h2 class="h5 mb-0">Record summary</h2></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3"><div class="evidence-label">Programs</div><div class="h4 mb-0">{{ $programs->count() }}</div></div>
                <div class="col-md-3"><div class="evidence-label">Registration records</div><div class="h4 mb-0">{{ $transactions->count() }}</div></div>
                <div class="col-md-3"><div class="evidence-label">Test result rows</div><div class="h4 mb-0">{{ $results->count() }}</div></div>
                <div class="col-md-3"><div class="evidence-label">Certificates</div><div class="h4 mb-0">{{ $certificates->count() }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
