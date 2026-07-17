@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .programs-dashboard .badge {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: auto !important;
        height: auto !important;
        min-width: 0;
        padding: .35rem .6rem;
        border-radius: .35rem !important;
        box-shadow: none;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    .programs-dashboard .hero-card,
    .programs-dashboard .metric-card,
    .programs-dashboard .table-card {
        border: 1px solid #e6eaf2;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    .programs-dashboard .hero-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #111827 100%);
        color: #fff;
    }

    .programs-dashboard .hero-card .text-muted,
    .programs-dashboard .hero-card .small {
        color: rgba(255, 255, 255, .75) !important;
    }

    .programs-dashboard .section-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 700;
        color: #64748b;
    }

    .programs-dashboard .hero-card .section-label {
        color: rgba(255, 255, 255, .65);
    }

    .programs-dashboard .metric-card {
        background: #fff;
        height: 100%;
        padding: 1rem;
    }

    .programs-dashboard .metric-value {
        font-size: 1.6rem;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }

    .programs-dashboard .metric-label {
        font-size: .76rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        margin-bottom: .35rem;
    }

    .programs-dashboard .table-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef2f7;
    }

    .programs-dashboard .table thead th {
        background: #f8fafc;
        border-top: 0;
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #475569;
    }

    .programs-dashboard .table tbody tr {
        transition: background .15s ease;
    }

    .programs-dashboard .table tbody tr:hover {
        background: #f8fbff;
    }

    .programs-dashboard .program-banner {
        width: 88px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #d8e0ea;
        background: #f8fafc;
    }

    .programs-dashboard .program-title {
        font-size: .98rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
    }

    .programs-dashboard .program-copy {
        font-size: .84rem;
        color: #64748b;
        line-height: 1.45;
    }

    .programs-dashboard .program-pills,
    .programs-dashboard .quick-links,
    .programs-dashboard .action-stack {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
    }

    .programs-dashboard .program-pills .badge {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #dbe3ec;
    }

    .programs-dashboard .program-pills .badge.bg-warning {
        background: #fff3cd;
        color: #856404;
        border-color: #ffe69c;
    }

    .programs-dashboard .program-pills .badge.bg-info {
        background: #e0f2fe;
        color: #075985;
        border-color: #bae6fd;
    }

    .programs-dashboard .program-pills .badge.bg-success {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .programs-dashboard .program-pills .badge.bg-primary {
        background: #dbeafe;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .programs-dashboard .program-pills .badge.bg-secondary {
        background: #e2e8f0;
        color: #334155;
        border-color: #cbd5e1;
    }

    .programs-dashboard .action-stack {
        min-width: 220px;
        justify-content: flex-start;
    }

    .programs-dashboard .action-stack .btn {
        border-radius: 10px;
        white-space: nowrap;
    }

    .programs-dashboard .dropdown-menu {
        border-radius: 12px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .12);
        border-color: #e6eaf2;
    }

    .programs-dashboard .dropdown-item {
        white-space: normal;
    }

    .programs-dashboard .simple-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .3rem .55rem;
        border-radius: 999px;
        background: #e2e8f0;
        color: #334155;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .programs-dashboard .flashing-red {
        color: #dc2626;
        font-weight: 700;
        animation: flashRed 1s infinite;
    }

    @keyframes flashRed {
        0% { opacity: 1; }
        50% { opacity: .35; }
        100% { opacity: 1; }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@php
    $permissionsToCheck = [
        'program.detailsexport',
        'programs.edit',
        'crm.hide',
        'crm.show',
        'results.disable',
        'results.enable',
        'certificates.disable',
        'certificates.enable',
        'password.reset',
        'registration.close',
        'registration.open',
        'training.clone',
        'training.import',
        'programs.destroy',
        'earlybird.close',
        'earlybird.open',
    ];

    $programCollection = collect($programs);
    $currencySymbol = \App\Models\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION');
    $canCreate = canUserAccessPermission(['programs.create'])['programs.create'] ?? false;
    $summary = [
        'total' => $programCollection->count(),
        'published' => $programCollection->where('status', 1)->count(),
        'draft' => $programCollection->where('status', '<>', 1)->count(),
        'crm_enabled' => $programCollection->where('hascrm', 1)->count(),
        'cert_enabled' => $programCollection->where('show_certificate', 1)->count(),
        'discounted' => $programCollection->where('early_bird_status', 1)->count(),
        'with_children' => $programCollection->filter(fn ($program) => $program->subPrograms?->count() > 0)->count(),
        'auto_certificate' => $programCollection->filter(fn ($program) => data_get($program, 'auto_certificate_settings.auto_certificate_status') === 'yes')->count(),
    ];
@endphp

<div class="container-fluid py-3 programs-dashboard">
    <div class="card hero-card mb-3">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="section-label mb-2">Training Management</div>
                    <h3 class="mb-2">All Trainings</h3>
                    <div class="small">Manage programme setup, certification, participant access, cloning, and import actions from one dashboard.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($canCreate)
                        <a href="{{ route('programs.create') }}" class="btn btn-light btn-sm">Add New Training</a>
                    @endif
                    <span class="badge bg-white text-dark">Published {{ $summary['published'] }}</span>
                    <span class="badge bg-white text-dark">Drafts {{ $summary['draft'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Total trainings</div>
                <div class="metric-value">{{ number_format($summary['total']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Published</div>
                <div class="metric-value">{{ number_format($summary['published']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">CRM enabled</div>
                <div class="metric-value">{{ number_format($summary['crm_enabled']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Auto certificate</div>
                <div class="metric-value">{{ number_format($summary['auto_certificate']) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="metric-card">
                <div class="metric-label">Certificate enabled</div>
                <div class="metric-value">{{ number_format($summary['cert_enabled']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="metric-card">
                <div class="metric-label">Early bird discounted</div>
                <div class="metric-value">{{ number_format($summary['discounted']) }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="metric-card">
                <div class="metric-label">With children</div>
                <div class="metric-value">{{ number_format($summary['with_children']) }}</div>
            </div>
        </div>
    </div>

    @include('layouts.partials.alerts')

    <div class="card table-card">
        <div class="card-header py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">
                <div>
                    <div class="section-label">Training Registry</div>
                    <h5 class="card-title mb-0">Current Trainings</h5>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="zero_config" class="table table-striped table-bordered align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Banner</th>
                        <th>Title</th>
                        <th>Fee</th>
                        <th>Dates</th>
                        <th>Participants</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programCollection as $program)
                        @php
                            $program->permissions = checkTrainingHasPermissions($program->id, $permissionsToCheck);
                            $hasChildren = $program->subPrograms && $program->subPrograms->count() > 0;
                            $programStatus = $program->status == 1 ? 'Published' : 'Draft';
                        @endphp
                        <tr>
                            <td class="align-top text-nowrap">{{ $i++ }}</td>
                            <td class="align-top">
                                <img src="{{ url('/').'/'.$program->image }}" alt="banner" class="program-banner mb-2">
                                <div>
                                    <span class="simple-badge">{{ $programStatus }}</span>
                                </div>
                            </td>
                            <td class="align-top">
                                <div class="program-title">{{ $program->p_name }}</div>
                                <div class="program-copy mt-1">
                                    @if($program->parent)
                                        <div class="mb-1">
                                            <span class="text-muted fw-bold">Parent:</span>
                                            <a target="_blank" href="{{ route('programs.edit', $program->parent->id) }}">{{ $program->parent->p_name }}</a>
                                        </div>
                                    @endif

                                    <div class="program-pills mt-2">
                                        <span class="badge bg-secondary">Type: {{ $program->off_season ? 'Off Season' : 'Normal' }}</span>
                                        @if($program->early_bird_status == 1)
                                            <span class="badge bg-warning">Discounted</span>
                                        @endif
                                        @if(!empty($program->auto_certificate_settings['auto_certificate_status']) && $program->auto_certificate_settings['auto_certificate_status'] === 'yes')
                                            <span class="badge bg-info">Auto Certificate</span>
                                        @endif
                                    </div>

                                    @if($hasChildren)
                                        <div class="dropdown mt-2">
                                            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                View Children ({{ $program->subPrograms->count() }})
                                            </button>
                                            <div class="dropdown-menu children-menu p-1">
                                                @foreach($program->subPrograms as $child)
                                                    <a class="dropdown-item rounded" target="_blank" href="{{ route('programs.edit', $child->id) }}">{{ $child->p_name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if($program->permissions['program.detailsexport'])
                                        <div class="mt-2">
                                            <a href="{{ route('program.detailsexport', ['p_id'=> $program->id, 'id'=> $program->id]) }}" class="text-decoration-none">
                                                <i class="fa fa-download"></i> Export Participant's details
                                            </a>
                                        </div>
                                    @endif

                                    @if($program->status == 1)
                                        <div class="quick-links mt-2">
                                            <a href="{{ url('/trainings').'/'.$program->slug }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa fa-eye"></i> Preview Training
                                            </a>
                                            @if($program->early_bird_status == 1)
                                                <a href="{{ url('/early-bird-trainings').'/'.$program->slug }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fa fa-eye"></i> Preview Earlybird Link
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="align-top">
                                <div><strong>Normal Fee:</strong> {{ $currencySymbol . number_format($program->p_amount) }}</div>
                                <div><strong>EarlyBird:</strong> {{ $currencySymbol . number_format($program->e_amount) }}</div>
                            </td>
                            <td class="align-top">
                                <div><strong>Start:</strong> {{ $program->p_start }}</div>
                                <div><strong>End:</strong> {{ $program->p_end }}</div>
                            </td>
                            <td class="align-top">
                                <div><strong>Part:</strong> {{ $program->part_paid }}</div>
                                <div><strong>Full:</strong> {{ $program->fully_paid }}</div>
                                <div><strong>Materials:</strong> {{ $program->materials->count() }}</div>
                                <div><strong>Modules:</strong> {{ $program->modules->count() }}</div>
                            </td>
                            <td class="align-top">
                                @if($program->status == 1)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="align-top">
                                <div class="action-stack">
                                    @if($program->permissions['programs.edit'])
                                        <a class="btn btn-info btn-sm" href="{{ route('programs.edit', ['p_id'=> $program->id, 'program'=> $program->id]) }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    @endif

                                    @if($program->hascrm == 0)
                                        @if($program->permissions['crm.show'])
                                            <a class="btn btn-primary btn-sm" href="{{ route('crm.show', ['p_id'=> $program->id, 'crm'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="far fa-comments"></i> Enable CRM
                                            </a>
                                        @endif
                                    @else
                                        @if($program->permissions['crm.hide'])
                                            <a class="btn btn-primary btn-sm" href="{{ route('crm.hide', ['p_id'=> $program->id, 'crm'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-ban"></i> Disable CRM
                                            </a>
                                        @endif
                                    @endif

                                    @if($program->hasresult == 0)
                                        @if($program->permissions['results.enable'])
                                            <a class="btn btn-success btn-sm" href="{{ route('results.enable', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-graduation-cap"></i> Enable Results
                                            </a>
                                        @endif
                                    @else
                                        @if($program->permissions['results.disable'])
                                            <a class="btn btn-warning btn-sm" href="{{ route('results.disable', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-ban"></i> Disable Results
                                            </a>
                                        @endif
                                    @endif

                                    @if($program->show_certificate == 0)
                                        @if($program->permissions['certificates.enable'])
                                            <a class="btn btn-success btn-sm" href="{{ route('certificates.enable', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-graduation-cap"></i> Enable Certificates
                                            </a>
                                        @endif
                                    @else
                                        @if($program->permissions['certificates.disable'])
                                            <a class="btn btn-warning btn-sm" href="{{ route('certificates.disable', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-ban"></i> Disable Certificates
                                            </a>
                                        @endif
                                    @endif

                                    @if($program->permissions['password.reset'])
                                        <a class="btn btn-dark btn-sm" href="{{ route('admin.password.reset', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                            <i class="fa fa-window-close"></i> Reset Password
                                        </a>
                                    @endif

                                    @if($program->close_registration == 0)
                                        @if($program->permissions['registration.close'])
                                            <a class="btn btn-danger btn-sm" href="{{ route('registration.close', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-window-close"></i> Close Registration
                                            </a>
                                        @endif
                                    @else
                                        @if($program->permissions['registration.open'])
                                            <a class="btn btn-success btn-sm" href="{{ route('registration.open', ['p_id'=> $program->id, 'id'=> $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-window-restore"></i> Extend Registration
                                            </a>
                                        @endif
                                    @endif

                                    @if($program->permissions['training.clone'])
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#cloneTraining{{ $program->id }}">
                                            <i class="fa fa-copy"></i> Clone Training
                                        </button>

                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importData{{ $program->id }}">
                                            <i class="fa fa-upload"></i> Import Data
                                        </button>
                                    @endif

                                    @if($program->permissions['training.import'])
                                        <a class="btn btn-dark btn-sm" href="{{ route('training.import', ['p_id'=> $program->id, 'program'=> $program->id]) }}">
                                            <i class="fa fa-upload"></i> Bulk Import
                                        </a>
                                    @endif

                                    @if($program->permissions['programs.destroy'])
                                        <form action="{{ route('programs.destroy', ['p_id'=> $program->id, 'program' => $program->id]) }}" method="POST" onsubmit="return confirm('Do you really want to trash?');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-recycle"></i> Trash
                                            </button>
                                        </form>
                                    @endif

                                    @if($program->e_amount > 0)
                                        @if($program->early_bird_status == 1)
                                            @if($program->permissions['earlybird.close'])
                                                <a class="btn btn-info btn-sm" href="{{ route('earlybird.close', ['id' => $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                    <i class="fa fa-folder-open"></i> Close Earlybird
                                                </a>
                                            @endif
                                        @else
                                            @if($program->permissions['earlybird.open'])
                                                <a class="btn btn-info btn-sm" href="{{ route('earlybird.open', ['id' => $program->id]) }}" onclick="return confirm('Are you really sure?');">
                                                    <i class="fa fa-folder"></i> Extend Earlybird
                                                </a>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach($programCollection as $program)
        @php($program->permissions = $program->permissions ?? checkTrainingHasPermissions($program->id, $permissionsToCheck))
        @if($program->permissions['training.clone'])
            <div class="modal fade" id="cloneTraining{{ $program->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Clone {{ $program->p_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form onsubmit="return confirm('This will clone training');" action="{{ route('training.clone', ['p_id'=> $program->id, 'training'=> $program->id]) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="clone_options{{ $program->id }}" class="form-label">Select Clone Options</label>
                                    <select name="clone_options[]" class="select3 form-control" multiple="multiple" required id="clone_options{{ $program->id }}" style="width: 100%;">
                                        <option value="training_materials">Training Materials</option>
                                        <option value="modules">Modules</option>
                                        <option value="score_settings">Score Settings</option>
                                        <option value="certificate_settings">Certificate Settings</option>
                                        <option value="all">All</option>
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        Certificate settings only clone for programs already using the new certificate designer. Legacy certificate settings are always stripped from cloned programs.
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Clone</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="importData{{ $program->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form onsubmit="return confirm('This will import data from another training');" action="{{ route('training.import.data', ['p_id'=> $program->id, 'training'=> $program->id]) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="import_options{{ $program->id }}" class="form-label">Select Import Options</label>
                                    <select name="import_options[]" class="select4 form-control" multiple="multiple" required id="import_options{{ $program->id }}" style="width: 100%;">
                                        <option value="training_materials">Training Materials</option>
                                        <option value="modules">Modules</option>
                                        <option value="score_settings">Score Settings</option>
                                        <option value="certificate_settings">Certificate Settings</option>
                                        <option value="all">All</option>
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        Certificate settings only import when the source training already uses the new certificate designer. Legacy certificate settings are ignored during import.
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Select Training to Import from</label>
                                    <select name="import_from" class="select4 form-control" required style="width: 100%;">
                                        <option value="">Select</option>
                                        @foreach($programCollection as $prog)
                                            @if($prog->id != $program->id)
                                                <option value="{{ $prog->id }}">{{ $prog->p_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection

@section('extra-scripts')
<script>
    $(document).ready(function() {
        $('.select3').select2({
            dropdownParent: $('body'),
            width: '100%'
        });

        $('.select4').select2({
            dropdownParent: $('body'),
            width: '100%',
            minimumResultsForSearch: 0
        });
    });
</script>
@endsection
