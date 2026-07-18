@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' Test Management' }}
@endsection
@section('content')
@php
    $totalPrograms = method_exists($programs_with_modules, 'total') ? $programs_with_modules->total() : $programs_with_modules->count();
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-lg-3">
            <a href="{{ route('modules.index') }}">
                <div class="card border-0 shadow-sm crm-hero h-100">
                    <div class="card-body text-center">
                        <div class="h1 mb-2"><i class="fa fa-list-alt"></i></div>
                        <div class="fw-semibold">{{ $modules->count() }} Module(s)</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="card border-0 shadow-sm crm-hero h-100">
                <div class="card-body text-center">
                    <div class="h1 mb-2"><i class="fa fa-check-circle text-success"></i></div>
                    <div class="fw-semibold">{{ $activeModulesCount ?? 0 }} Active Modules</div>
                    <div class="small text-muted">Currently enabled</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="card border-0 shadow-sm crm-hero h-100">
                <div class="card-body text-center">
                    <div class="h1 mb-2"><i class="fa fa-ban text-secondary"></i></div>
                    <div class="fw-semibold">{{ $inactiveModulesCount ?? 0 }} Inactive Modules</div>
                    <div class="small text-muted">Currently disabled</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <a href="{{ route('results.index') }}">
                <div class="card border-0 shadow-sm crm-hero h-100">
                    <div class="card-body text-center">
                        <div class="h1 mb-2"><i class="fas fa-user-graduate"></i></div>
                        <div class="fw-semibold">Grades</div>
                    </div>
                </div>
            </a>
        </div>
        @if(resolveAuthUser()->roles == "Admin")
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('scoreSettings.index') }}">
                    <div class="card border-0 shadow-sm crm-hero h-100">
                        <div class="card-body text-center">
                            <div class="h1 mb-2"><i class="fa fa-cog"></i></div>
                            <div class="fw-semibold">Score Settings</div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Select a Training to manage its modules</h1>
                            <p class="text-muted mb-0">Pick a training below to view its modules and question counts.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalPrograms }} trainings</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Training</th>
                            <th>Modules</th>
                            <th>Questions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs_with_modules as $programs)
                            <?php
                                $permissionsToCheck = ['modules.index'];
                                $permissions = checkTrainingHasPermissions($programs->id, $permissionsToCheck);
                            ?>
                            <tr>
                                <td data-label="#">
                                    {{ $i++ }}
                                </td>
                                <td data-label="Training">
                                    @if($permissions['modules.index'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Click to view modules for this training" class="btn btn-outline-primary btn-sm" href="{{ route('facilitatormodules', ['p_id'=>$programs->id]) }}">
                                            {{ $programs->p_name }}
                                        </a>
                                    @else
                                        <span class="btn btn-outline-secondary btn-sm">{{ $programs->p_name }}</span>
                                    @endif
                                </td>
                                <td data-label="Modules">{{ $programs->modules->count() }}</td>
                                <td data-label="Questions">{{ $programs->questions->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
