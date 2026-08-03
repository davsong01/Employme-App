@extends('dashboard.admin.index')

@section('title', 'Add Certificate')

@section('css')
<style>
    .certificate-hero {
        border: 0;
        border-radius: 24px;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, .18), transparent 28%),
            linear-gradient(135deg, #0f172a 0%, #111827 55%, #1e293b 100%);
        color: #fff;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, .18);
    }

    .certificate-hero .text-muted,
    .certificate-hero .small {
        color: rgba(255, 255, 255, .72) !important;
    }

    .certificate-shell {
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        background: #fff;
    }

    .certificate-meta-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
    }

    .certificate-kpi {
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 1rem 1.1rem;
        background: #fff;
    }

    .certificate-kpi .value {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1;
    }

    .certificate-kpi .label {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .certificate-form-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 34px rgba(15, 23, 42, .05);
    }

    .certificate-form-card .form-label {
        font-weight: 700;
        color: #334155;
    }

    .certificate-form-card .form-control,
    .certificate-form-card .form-select {
        min-height: 48px;
        border-radius: 14px;
        border-color: #dbe3ea;
    }

    .certificate-form-card .form-control:focus,
    .certificate-form-card .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .12);
    }

    .certificate-hint {
        color: #64748b;
        font-size: .82rem;
    }

    .certificate-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 700;
        line-height: 1;
    }

    .certificate-pill.info {
        background: #e0f2fe;
        color: #0369a1;
    }

    .certificate-pill.success {
        background: #dcfce7;
        color: #166534;
    }

    .certificate-pill.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .certificate-pill.danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .certificate-list {
        margin: 0;
        padding-left: 1rem;
        color: #475569;
    }

    .certificate-list li + li {
        margin-top: .45rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @include('layouts.partials.alerts')

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card certificate-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-4">
                        <div class="pe-xl-4">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="certificate-pill info">
                                    @php($generatedCertificatesCount = $generatedCertificatesCount ?? 0)
                                    {{ number_format($generatedCertificatesCount) }} {{ \Illuminate\Support\Str::plural('certificate', $generatedCertificatesCount) }} generated
                                </span>
                                @if(!empty($certificate_settings['auto_certificate_status']) && $certificate_settings['auto_certificate_status'] == 'yes')
                                    <span class="certificate-pill success">Auto generation enabled</span>
                                @else
                                    <span class="certificate-pill warning">Manual generation only</span>
                                @endif
                            </div>
                            <h1 class="display-6 fw-bold mb-2">{{ $p_name }} Certificate Management</h1>
                            <p class="mb-0 text-muted">
                                Generate and manage certificates for this training without leaving the page.
                                The certificate index now handles the library view, previews, verification links, and regeneration history.
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @if(isset($certificate_settings['auto_certificate_status']) && $certificate_settings['auto_certificate_status'] == 'yes')
                                <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#batchModal">
                                    <i class="fa fa-magic me-1"></i> Auto Generate
                                </button>
                                <a href="{{ route('certificate.clear.duplicates', $p_id) }}" class="btn btn-outline-danger btn-lg" onclick="return confirm('Clear duplicate certificates for this program?');">
                                    <i class="fa fa-clone me-1"></i> Clear Duplicates
                                </a>
                            @endif
                            <a href="{{ route('certificates.index', ['program_id' => $p_id]) }}" class="btn btn-outline-light btn-lg">
                                <i class="fa fa-list me-1"></i> Open Index
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card certificate-form-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-start mb-4">
                        <div>
                            <h2 class="h4 fw-bold mb-2">Generate a certificate</h2>
                            <p class="text-muted mb-0">Pick a learner, upload the certificate file, and submit. The record will be routed back to the main certificate index.</p>
                        </div>
                    </div>

                    <form action="{{ route('certificates.save') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                        @csrf
                        <input type="hidden" value="{{ $p_id }}" name="p_id">

                        <div class="col-12">
                            <label for="user_id" class="form-label">Select User *</label>
                            <select name="user_id" id="user_id" class="form-select select2" required data-placeholder="Search and select a learner">
                                <option value=""></option>
                                @foreach ($users->sortBy('name') as $user)
                                    @if($user->certificates_count <= 0)
                                        <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="certificate-hint mt-2">Only learners without an existing certificate for this training are shown here.</div>
                            <div class="text-danger small mt-1">{{ $errors->first('user_id') }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="certificate" class="form-label">Choose Certificate *</label>
                            <input type="file" id="certificate" name="certificate" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <div class="certificate-hint mt-2">Upload the issued certificate file in PDF, DOC, DOCX, JPG, JPEG, or PNG format.</div>
                            <div class="text-danger small mt-1">{{ $errors->first('certificate') }}</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="date_issued" class="form-label">Date Issued</label>
                            <input type="date" class="form-control" name="date_issued" id="date_issued" value="{{ now()->format('Y-m-d') }}">
                            <div class="certificate-hint mt-2">Defaults to today if you leave it as-is.</div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fa fa-check me-1"></i> Save Certificate
                                </button>
                                <a href="{{ route('certificates.index', ['program_id' => $p_id]) }}" class="btn btn-outline-secondary btn-lg px-4">
                                    Back to Index
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="batchModal" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="batchModalLabel">Auto Certificate Options</h5>
                    <div class="text-muted small">Generate certificates in batches for this program.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('certificates.generate', $p_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="batch-size" class="form-label">Batch Size</label>
                        <input type="number" class="form-control" id="batch-size" name="pick" min="1" value="50" required>
                    </div>
                    <div class="mb-3">
                        <label for="show_certificate" class="form-label">Enable Generated Certificates</label>
                        <select name="show_certificate" class="form-select">
                            <option value="">Select</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="use_cron" class="form-label">Use Cron</label>
                        <select name="use_cron" class="form-select">
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label for="date_issued" class="form-label">Date Issued</label>
                        <input type="date" class="form-control" name="date_issued" id="batch-date_issued" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="generate-button">
                        <span id="generate-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    $(document).ready(function() {
        $('#user_id').select2();

        $('#generate-button').on('click', function() {
            $('#generate-spinner').removeClass('d-none');
        });
    });
</script>
@endsection
