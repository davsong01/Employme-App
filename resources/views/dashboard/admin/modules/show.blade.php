@php
    $checks = [
        'modules.index',
        'modules.create',
        'modules.edit',
        'module.clone',
        'modules.enable',
        'modules.disable',
        'modules.show',
        'modules.destroy'
    ];

    $allPermissions = checkTrainingHasPermissions($p_id, $checks);
@endphp
@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' Test Management' }}
@endsection
@section('css')
<style>
    .module-bulk-bar {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 14px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .04);
        padding: .85rem 1rem;
    }

    .module-bulk-bar .btn {
        border-radius: 10px;
    }

    .module-select-col {
        width: 44px;
        min-width: 44px;
        text-align: center;
    }

    .module-select-col .form-check-input {
        float: none;
        margin: 0;
        cursor: pointer;
        transform: translateY(1px);
    }

    .module-table thead th {
        vertical-align: middle;
    }

    .module-table tbody td:first-child,
    .module-table thead th:first-child {
        text-align: center;
    }
</style>
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <h2 style="color:#008000; text-align:center; padding:20px">{{ strtoupper($program_name->p_name) }} <br> MODULE MANAGEMENT</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
        <a href="{{ route('modules.index')}}">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> {{$modules->count()}} Module(s)</h6>
                </div>
            </div>
        </a>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
        <a href="{{ route('modules.edit', ['p_id' => $p_id, 'module' => $modules->first()?->id])}}">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                <h6 class="text-white"><b></b> {{ $questions_count }} Questions</h6>
                </div>
            </div>
        </a>
        </div>
        
        <div class="col-md-4 col-lg-4">
        <a href="{{ route('scoreSettings.index')}}">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-cog"></i></h1>
                <h6 class="text-white"><b></b> Score Settings </h6>
                </div>
            </div>
        </a>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @include('layouts.partials.alerts')

            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                <div>
                    <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Module Desk</span>
                    <h1 class="h4 fw-bold mb-1">{{ strtoupper($program_name->p_name) }} Modules</h1>
                    <p class="text-muted mb-0">Use the bulk controls to activate or deactivate several modules at once.</p>
                </div>
                @if($allPermissions['modules.create'])
                    <a href="{{route('modules.create', ['p_id' => $program_name->id] )}}" class="btn btn-outline-primary">
                        <i class="fa fa-plus me-1"></i>Add New Module
                    </a>
                @endif
            </div>

            <form id="bulkModuleStatusForm" method="POST" action="{{ route('modules.bulk-status') }}">
                @csrf
                <input type="hidden" name="p_id" value="{{ $p_id }}">
            </form>

            <div class="module-bulk-bar mb-3">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" form="bulkModuleStatusForm" name="bulk_status" value="enable" class="btn btn-success btn-sm" onclick="return confirm('Activate the selected modules?');">
                            <i class="fa fa-check me-1"></i>Activate selected
                        </button>
                        <button type="submit" form="bulkModuleStatusForm" name="bulk_status" value="disable" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Deactivate the selected modules?');">
                            <i class="fa fa-ban me-1"></i>Deactivate selected
                        </button>
                        <span class="text-muted small">Select one or more rows to update their status together.</span>
                    </div>
                    <button type="button" class="btn btn-link btn-sm text-decoration-none px-0" id="selectAllModules">
                        Select all
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0 module-table">
                    <thead>
                        <tr>
                            <th class="module-select-col">
                                <input type="checkbox" id="selectAllModulesHeader" class="form-check-input">
                            </th>
                            <th>#</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Associated Training</th>
                            <th>Expected Questions</th>
                            <th>Set Questions</th>
                            <th>Question Time</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                        <tr>
                            <td class="module-select-col" data-label="">
                                <input type="checkbox" name="module_ids[]" value="{{ $module->id }}" form="bulkModuleStatusForm" class="form-check-input js-module-select">
                            </td>
                            <td data-label="#">{{ $i++ }}</td>
                            <td data-label="Date">{{ $module->created_at->format('d/m/Y') }}</td>
                            <td data-label="Title">
                                {{ $module->title }}<br>
                                <span class="text-danger small fw-semibold">{{ $module->type }}</span>
                            </td>
                            <td data-label="Associated Training">
                                {{ $module->program->p_name }}
                                @if($module->allow_test_retake == 1)
                                    <br><span class="badge bg-danger rounded-pill mt-1">Can Retake Tests</span>
                                @endif
                            </td>
                            <td data-label="Expected Questions">{{ $module->noofquestions }}</td>
                            <td data-label="Set Questions">{{ $module->questions->count() }}</td>
                            <td data-label="Question Time">{{ $module->time }} minutes</td>
                            <td data-label="Type">{{ $module->type }}</td>
                            <td data-label="Status">
                                <span class="badge {{ $module->status == 0 ? 'bg-secondary' : 'bg-success' }} rounded-pill">
                                    {{ $module->status == 0 ? 'Disabled' : 'Enabled' }}
                                </span>
                            </td>
                            <td class="text-end" data-label="Actions">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    @if($allPermissions['modules.edit'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Edit Module"
                                            class="btn btn-outline-primary btn-sm"
                                            href="{{ route('modules.edit', ['p_id' => $p_id, 'module' => $module->id]) }}"
                                            onclick="return confirm('Are you really sure?');">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endif

                                    @if($module->status == 0)
                                        @if($allPermissions['modules.enable'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Enable Module Questions"
                                                class="btn btn-outline-secondary btn-sm"
                                                href="{{ route('modules.enable', ['p_id' => $p_id, 'id' => $module->id]) }}"
                                                onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        @endif
                                    @else
                                        @if($allPermissions['modules.disable'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Disable Module Questions"
                                                class="btn btn-outline-secondary btn-sm"
                                                href="{{ route('modules.disable', ['p_id' => $p_id, 'id' => $module->id]) }}"
                                                onclick="return confirm('Are you really sure?');">
                                                <i class="fa fa-ban"></i>
                                            </a>
                                        @endif
                                    @endif

                                    @if($allPermissions['module.clone'] && $module->questions->count() > 0)
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Clone Module"
                                            class="btn btn-outline-info btn-sm"
                                            href="{{ route('module.clone', ['p_id' => $p_id, 'module' => $module->id]) }}">
                                            <i class="fa fa-clone"></i>
                                        </a>
                                    @endif

                                    @if($module->status == 0 && $module->questions->count() < 1 && $allPermissions['modules.destroy'])
                                        <form action="{{ route('modules.destroy', ['p_id' => $p_id, 'module' => $module->id]) }}" method="POST"
                                            onsubmit="return confirm('Do you really want to Delete?');" class="m-0">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete module">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="dropdown d-inline-flex d-md-none module-actions-mobile">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($allPermissions['modules.edit'])
                                            <li><a class="dropdown-item" href="{{ route('modules.edit', ['p_id' => $p_id, 'module' => $module->id]) }}"><i class="fa fa-edit me-2"></i>Edit Module</a></li>
                                        @endif
                                        @if($module->status == 0)
                                            @if($allPermissions['modules.enable'])
                                                <li><a class="dropdown-item" href="{{ route('modules.enable', ['p_id' => $p_id, 'id' => $module->id]) }}"><i class="fa fa-check me-2"></i>Enable Module</a></li>
                                            @endif
                                        @else
                                            @if($allPermissions['modules.disable'])
                                                <li><a class="dropdown-item" href="{{ route('modules.disable', ['p_id' => $p_id, 'id' => $module->id]) }}"><i class="fa fa-ban me-2"></i>Disable Module</a></li>
                                            @endif
                                        @endif
                                        @if($allPermissions['module.clone'] && $module->questions->count() > 0)
                                            <li><a class="dropdown-item" href="{{ route('module.clone', ['p_id' => $p_id, 'module' => $module->id]) }}"><i class="fa fa-clone me-2"></i>Clone Module</a></li>
                                        @endif
                                        @if($module->status == 0 && $module->questions->count() < 1 && $allPermissions['modules.destroy'])
                                            <li>
                                                <form action="{{ route('modules.destroy', ['p_id' => $p_id, 'module' => $module->id]) }}" method="POST"
                                                    onsubmit="return confirm('Do you really want to Delete?');" class="m-0">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash me-2"></i>Delete Module
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

@endsection

@section('extra-scripts')
<script>
    (function () {
        const header = document.getElementById('selectAllModulesHeader');
        const helper = document.getElementById('selectAllModules');

        if (!header || !helper) {
            return;
        }

        const boxes = () => Array.from(document.querySelectorAll('.js-module-select'));
        const setAll = (checked) => {
            boxes().forEach((box) => {
                box.checked = checked;
            });
            header.checked = checked;
        };

        header.addEventListener('change', () => setAll(header.checked));
        helper.addEventListener('click', () => setAll(!header.checked));
    })();
</script>
@endsection
