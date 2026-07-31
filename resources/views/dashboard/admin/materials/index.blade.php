@php
    $permissionsToCheck = ['materials.store', 'material.clone', 'materials.download', 'materials.destroy', 'getmaterial'];
    $totalMaterials = method_exists($materials, 'total') ? $materials->total() : $materials->count();
    $selectedProgramId = request('program_id');
    $materialItems = collect(method_exists($materials, 'items') ? $materials->items() : $materials);
    $programCount = $programs->count();
    $latestMaterial = $materialItems->sortByDesc('created_at')->first();
    $selectedProgramName = $selectedProgramId
        ? optional($programs->firstWhere('id', $selectedProgramId))->p_name
        : null;
@endphp
@extends('dashboard.admin.index')

@section('title', 'Study Materials')

@section('css')
<style>
    .materials-dashboard-card {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, .06);
    }

    .materials-dashboard-card .table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .08em;
        color: #64748b;
        border-bottom: 0;
    }

    .materials-dashboard-card .table tbody td {
        vertical-align: middle;
    }

    .materials-dashboard-card .table thead th:first-child,
    .materials-dashboard-card .table tbody td:first-child,
    .materials-dashboard-card .table tfoot th:first-child {
        width: 34px;
        padding-left: .5rem;
        padding-right: .35rem;
        text-align: center;
    }

    .material-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: 999px;
        padding: .3rem .75rem;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1;
    }

    .material-chip.program {
        background: #eef2ff;
        color: #4338ca;
    }

    .material-chip.file {
        background: #ecfeff;
        color: #0e7490;
    }

    .material-title {
        max-width: 320px;
    }

    .upload-progress {
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        background: #e2e8f0;
    }

    .upload-progress .progress-bar {
        background: linear-gradient(90deg, #2563eb, #0ea5e9);
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
                            <h1 class="h3 fw-bold mb-2">Study Materials</h1>
                            <p class="text-muted mb-0">Browse, filter, upload, download, clone, and delete materials from one dashboard.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if(checkRoleHas(['Admin','Facilitator']))
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterial">
                                    <i class="fa fa-plus me-1"></i> Add Material
                                </button>
                            @endif
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalMaterials }} materials</span>
                            @if(!empty(request('program_id')))
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                    Filtered by: {{ $selectedProgramName ?? 'Program' }}
                                </span>
                            @endif
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
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Materials in view</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ $totalMaterials }}</div>
                            <div class="text-muted small">Matching your filters</div>
                        </div>
                        <div class="crm-metric-icon bg-primary-subtle text-primary">
                            <i class="fa fa-folder-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Programs available</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold mb-0">{{ $programCount }}</div>
                            <div class="text-muted small">Assigned programs only</div>
                        </div>
                        <div class="crm-metric-icon bg-success-subtle text-success">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Latest upload</div>
                    <div class="fw-bold mb-1">{{ $latestMaterial?->title ?? 'No uploads yet' }}</div>
                    <div class="text-muted small">{{ $latestMaterial?->created_at?->format('d M Y, h:i A') ?? 'Waiting for the first material' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Active filter</div>
                    <div class="fw-bold mb-1">{{ $selectedProgramName ?? 'All programs' }}</div>
                    <div class="text-muted small">{{ request('search') ? 'Search: '.request('search') : 'No search query applied' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5 col-lg-4">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Search</label>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title or program">
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Program</label>
                    <select name="program_id" class="form-select select2" data-placeholder="All programs">
                        <option value=""></option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" @selected((string) $selectedProgramId === (string) $program->id)>
                                {{ $program->p_name }} ({{ $program->materials_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small text-uppercase fw-semibold text-muted">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <button class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if(checkRoleHas(['Admin','Facilitator']))
        <div class="modal fade" id="addMaterial" tabindex="-1" aria-labelledby="addMaterialLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMaterialLabel">Add New Study Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="material-upload-form" action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Program</label>
                                    <select name="program_id" id="material-program-select" class="form-select select2" data-placeholder="Select a program" required>
                                        <option value=""></option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" @selected((string) $selectedProgramId === (string) $program->id)>
                                                {{ $program->p_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Select files</label>
                                    <input type="file" name="file[]" id="material-files-input" class="form-control" multiple required>
                                </div>
                            </div>
                            <div class="mt-4 d-none" id="upload-progress-wrap">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="small text-muted" id="upload-status-text">Preparing upload...</div>
                                    <div class="small text-muted" id="upload-progress-percent">0%</div>
                                </div>
                                <div class="progress upload-progress">
                                    <div class="progress-bar" id="upload-progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="material-upload-submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(checkRoleHas(['Admin','Facilitator']))
        <div class="modal fade" id="cloneMaterial" tabindex="-1" aria-labelledby="cloneMaterialLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cloneMaterialLabel">Clone Study Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="clone-material-form" method="POST" action="">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info border-0 mb-4">
                                <div class="fw-semibold mb-1">Source material</div>
                                <div id="clone-source-title" class="small"></div>
                                <div id="clone-source-program" class="small text-muted"></div>
                            </div>
                            <div class="alert alert-warning border-0 mb-4">
                                This will duplicate the file into the selected program.
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Clone to program</label>
                                    <select name="program_id" id="clone-material-program-select" class="form-select select2" data-placeholder="Select a program" required>
                                        <option value=""></option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}">{{ $program->p_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-info">Clone Material</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(checkRoleHas(['Admin','Facilitator']))
        <div class="modal fade" id="bulkCloneMaterial" tabindex="-1" aria-labelledby="bulkCloneMaterialLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkCloneMaterialLabel">Clone Selected Materials</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="bulk-clone-material-form" method="POST" action="{{ route('materials.bulk-clone') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info border-0 mb-4">
                                <div class="fw-semibold mb-1">Selected materials</div>
                                <div id="bulk-clone-count" class="small"></div>
                            </div>
                            <div class="alert alert-warning border-0 mb-4">
                                This will duplicate the selected files into the chosen program.
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Clone to program</label>
                                    <select name="program_id" id="bulk-clone-program-select" class="form-select select2" data-placeholder="Select a program" required>
                                        <option value=""></option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}">{{ $program->p_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="bulk-clone-hidden-inputs"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-info">Clone Selected</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm materials-dashboard-card">
        <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
                <div>
                    <h2 class="h5 mb-1">Materials library</h2>
                    <p class="text-muted small mb-0">Review uploads, verify program ownership, and manage files in one place.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $materials->count() }} on this page</span>
                    @if($selectedProgramName)
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $selectedProgramName }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <form id="materials-bulk-delete-form" method="POST" action="{{ route('materials.bulk-destroy') }}">
                @csrf
            </form>

            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 align-items-md-center mb-3">
                <div class="text-muted small">
                    Showing {{ $materials->count() }} records on this page. Use the checkboxes for batch selection.
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                        <span id="selected-material-count">0</span> selected
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="select-all-materials-btn">
                        Select all on page
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-selection-btn" disabled>
                        Clear selection
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="bulk-actions-dropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            Bulk actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bulk-actions-dropdown">
                            <li><button class="dropdown-item" type="button" id="bulk-clone-action">Clone selected</button></li>
                            <li><button class="dropdown-item text-danger" type="submit" form="materials-bulk-delete-form" id="bulk-delete-btn">Delete permanently</button></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle crm-table crm-mobile-stack mb-0" id="materials-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 28px;">
                                <input type="checkbox" id="select-all-materials" class="form-check-input">
                            </th>
                            <th>#</th>
                            <th>Title</th>
                            <th>Program/Class</th>
                            <th>Uploaded By</th>
                            <th>Uploaded At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                            @php
                                $rowPermissions = checkTrainingHasPermissions($material->program_id, $permissionsToCheck);
                                $decodedFile = base64_decode($material->file) ?: '';
                                $fileExtension = strtoupper(pathinfo($decodedFile, PATHINFO_EXTENSION) ?: 'FILE');
                            @endphp
                            <tr>
                                <td data-label="">
                                    <input type="checkbox" class="form-check-input material-row-check" data-material-id="{{ $material->id }}">
                                </td>
                                <td data-label="#">{{ paginationIndex($materials, $loop) }}</td>
                                <td data-label="Title">
                                    <div class="d-flex flex-column gap-2 material-title">
                                        <div class="fw-semibold">{{ $material->title }}</div>
                                        <span class="material-chip file">
                                            <i class="fa fa-file"></i>
                                            {{ $fileExtension }}
                                        </span>
                                    </div>
                                </td>
                                <td data-label="Program/Class">
                                    <span class="material-chip program mt-2">
                                        <i class="fa fa-graduation-cap"></i>
                                        {{ $material->program->p_name ?? 'Unknown program' }}
                                    </span>
                                </td>
                                <td data-label="Uploaded By">
                                    <div class="fw-semibold">{{ $material->uploader?->name ?? $material->uploader?->full_name ?? 'System' }}</div>
                                </td>
                                <td data-label="Uploaded At">
                                    {{ optional($material->uploaded_at ?? $material->created_at)->format('d/m/Y h:i A') }}
                                </td>
                                <td class="text-end" data-label="Actions">
                                    <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                        @if(isset($material->program->id) && !empty($rowPermissions['getmaterial']))
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Download Material" class="btn btn-outline-primary btn-sm" href="{{ route('getmaterial', ['p_id' => $material->program->id, 'filename' => $material->file]) }}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        @endif
                                        @if(isset($material->program->id) && !empty($rowPermissions['material.clone']))
                                            <button
                                                type="button"
                                                class="btn btn-outline-info btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cloneMaterial"
                                                data-clone-action="{{ route('material.clone', ['material_id' => $material->id]) }}"
                                                data-clone-title="{{ $material->title }}"
                                                data-clone-program="{{ $material->program->p_name ?? 'Unknown program' }}"
                                            >
                                                <i class="fa fa-clone"></i>
                                            </button>
                                        @endif
                                        @if(!empty($rowPermissions['materials.destroy']))
                                            <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete material">
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
                                            @if(isset($material->program->id) && !empty($rowPermissions['getmaterial']))
                                                <li><a class="dropdown-item" href="{{ route('getmaterial', ['p_id' => $material->program->id, 'filename' => $material->file]) }}"><i class="fa fa-download me-2"></i>Download Material</a></li>
                                            @endif
                                        @if(isset($material->program->id) && !empty($rowPermissions['material.clone']))
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cloneMaterial"
                                                        data-clone-action="{{ route('material.clone', ['material_id' => $material->id]) }}"
                                                        data-clone-title="{{ $material->title }}"
                                                        data-clone-program="{{ $material->program->p_name ?? 'Unknown program' }}"
                                                    >
                                                        <i class="fa fa-clone me-2"></i>Clone Material
                                                    </button>
                                                </li>
                                            @endif
                                            @if(!empty($rowPermissions['materials.destroy']))
                                                <li>
                                                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fa fa-trash me-2"></i>Delete Material
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted mb-2">No materials found.</div>
                                    <div class="small text-muted">Try adjusting the filters or add a new material if you have permission.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($materials, 'links'))
                <div class="mt-4">
                    {{ $materials->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const programSelect = document.getElementById('material-program-select');
        const form = document.getElementById('material-upload-form');
        const fileInput = document.getElementById('material-files-input');
        const progressWrap = document.getElementById('upload-progress-wrap');
        const progressBar = document.getElementById('upload-progress-bar');
        const progressPercent = document.getElementById('upload-progress-percent');
        const uploadStatus = document.getElementById('upload-status-text');
        const submitBtn = document.getElementById('material-upload-submit');
        const cloneModal = document.getElementById('cloneMaterial');
        const cloneForm = document.getElementById('clone-material-form');
        const cloneTitle = document.getElementById('clone-source-title');
        const cloneProgram = document.getElementById('clone-source-program');
        const cloneProgramSelect = document.getElementById('clone-material-program-select');
        const selectAll = document.getElementById('select-all-materials');
        const selectAllBtn = document.getElementById('select-all-materials-btn');
        const clearBtn = document.getElementById('clear-selection-btn');
            const bulkActionsDropdown = document.getElementById('bulk-actions-dropdown');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCountLabel = document.getElementById('selected-material-count');
            const bulkCloneAction = document.getElementById('bulk-clone-action');
            const bulkCloneModal = document.getElementById('bulkCloneMaterial');
            const bulkCloneForm = document.getElementById('bulk-clone-material-form');
            const bulkCloneCount = document.getElementById('bulk-clone-count');
            const bulkCloneHiddenInputs = document.getElementById('bulk-clone-hidden-inputs');
            const bulkCloneProgramSelect = document.getElementById('bulk-clone-program-select');
            const rowChecks = Array.from(document.querySelectorAll('.material-row-check'));

        if (programSelect && window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            window.jQuery(programSelect).select2({
                width: '100%',
                dropdownParent: window.jQuery('#addMaterial')
            });
        }

        if (cloneProgramSelect && window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            window.jQuery(cloneProgramSelect).select2({
                width: '100%',
                dropdownParent: window.jQuery('#cloneMaterial')
            });
        }

        if (bulkCloneProgramSelect && window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            window.jQuery(bulkCloneProgramSelect).select2({
                width: '100%',
                dropdownParent: window.jQuery('#bulkCloneMaterial')
            });
        }

        if (cloneModal && cloneForm) {
            cloneModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;
                if (!trigger) {
                    return;
                }

                const action = trigger.getAttribute('data-clone-action');
                const title = trigger.getAttribute('data-clone-title') || 'Unknown material';
                const program = trigger.getAttribute('data-clone-program') || 'Unknown program';

                if (action) {
                    cloneForm.setAttribute('action', action);
                }

                if (cloneTitle) {
                    cloneTitle.textContent = title;
                }

                if (cloneProgram) {
                    cloneProgram.textContent = program;
                }

                if (cloneProgramSelect && window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                    window.jQuery(cloneProgramSelect).val(null).trigger('change');
                } else if (cloneProgramSelect) {
                    cloneProgramSelect.value = '';
                }
            });
        }

        function updateSelectionState() {
            const selectedCount = rowChecks.filter(cb => cb.checked).length;
            if (selectedCountLabel) {
                selectedCountLabel.textContent = selectedCount;
            }
            if (clearBtn) {
                clearBtn.disabled = selectedCount === 0;
            }
            if (bulkDeleteBtn) {
                bulkDeleteBtn.disabled = selectedCount === 0;
            }
            if (bulkActionsDropdown) {
                bulkActionsDropdown.disabled = selectedCount === 0;
            }
            if (selectAll) {
                selectAll.checked = rowChecks.length > 0 && selectedCount === rowChecks.length;
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

        rowChecks.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSelectionState);
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                rowChecks.forEach(cb => cb.checked = false);
                updateSelectionState();
            });
        }

        updateSelectionState();

        function selectedMaterialIds() {
            return rowChecks
                .filter(cb => cb.checked)
                .map(cb => cb.getAttribute('data-material-id'))
                .filter(Boolean);
        }

        const bulkDeleteForm = document.getElementById('materials-bulk-delete-form');
        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function (event) {
                const ids = selectedMaterialIds();
                if (!ids.length) {
                    event.preventDefault();
                    return;
                }

                bulkDeleteForm.querySelectorAll('input[name="material_ids[]"]').forEach(node => node.remove());

                ids.forEach(function (id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'material_ids[]';
                    input.value = id;
                    bulkDeleteForm.appendChild(input);
                });

                if (!confirm('Delete the selected materials permanently? This cannot be undone.')) {
                    event.preventDefault();
                }
            });
        }

        if (bulkCloneAction && bulkCloneModal && bulkCloneForm) {
            bulkCloneAction.addEventListener('click', function () {
                const ids = selectedMaterialIds();
                if (!ids.length) {
                    return;
                }

                if (bulkCloneHiddenInputs) {
                    bulkCloneHiddenInputs.innerHTML = '';

                    ids.forEach(function (id) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'material_ids[]';
                        input.value = id;
                        bulkCloneHiddenInputs.appendChild(input);
                    });
                }

                if (bulkCloneCount) {
                    bulkCloneCount.textContent = ids.length + ' material(s) selected';
                }

                const modal = bootstrap.Modal.getOrCreateInstance(bulkCloneModal);
                modal.show();
            });

            bulkCloneForm.addEventListener('submit', function (event) {
                if (!selectedMaterialIds().length) {
                    event.preventDefault();
                    return;
                }

                if (!confirm('Clone the selected materials into the chosen program?')) {
                    event.preventDefault();
                }
            });
        }

        if (!form || !fileInput || !progressWrap || !progressBar || !progressPercent || !uploadStatus || !submitBtn) {
            return;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (!fileInput.files || !fileInput.files.length) {
                form.submit();
                return;
            }

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();

            progressWrap.classList.remove('d-none');
            progressBar.style.width = '0%';
            progressPercent.textContent = '0%';
            uploadStatus.textContent = 'Uploading...';
            submitBtn.disabled = true;

            xhr.open(form.method || 'POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function (e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressPercent.textContent = percent + '%';
                    uploadStatus.textContent = 'Uploading...';
                }
            });

            xhr.onload = function () {
                window.location.reload();
            };

            xhr.onerror = function () {
                uploadStatus.textContent = 'Upload failed. Please try again.';
                submitBtn.disabled = false;
            };

            xhr.send(formData);
        });
    });
</script>
@endsection
