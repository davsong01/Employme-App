@extends('dashboard.admin.index')
@section('title')
{{ config('app.name') }} Test Management
@endsection
@section('content')
@php
    $totalScores = method_exists($scores, 'total') ? $scores->total() : $scores->count();
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Assessment Parameters</h1>
                            <p class="text-muted mb-0">Tune score weights for each training carefully. Existing modules still limit what can be edited.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalScores }} settings</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-3">
                <div class="text-muted small">Keep the defaults unless you are intentionally changing assessment logic.</div>
                <a href="{{ route('scoreSettings.create') }}" class="btn btn-outline-primary">Add New Assessment Parameter</a>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Training</th>
                            <th>Enabled Modules</th>
                            <th>Class Tests</th>
                            <th>Role Play Score</th>
                            <th>Email Score</th>
                            <th>Cert. Score</th>
                            <th>CRM Score</th>
                            <th>Passmark</th>
                            <th>Total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scores as $score)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Training">{{ $score->program->p_name ?? '' }}</td>
                            <td data-label="Enabled Modules">{{ $score->module_status_count ?? 0 }}</td>
                            <td data-label="Class Tests">{{ $score->class_test ?? 0 }}%</td>
                            <td data-label="Role Play Score">{{ $score->role_play ?? 0 }}%</td>
                            <td data-label="Email Score">{{ $score->email ?? 0 }}%</td>
                            <td data-label="Cert. Score">{{ $score->certification ?? 0 }}%</td>
                            <td data-label="CRM Score">{{ $score->crm_test ?? 0 }}%</td>
                            <td data-label="Passmark">{{ $score->passmark }}</td>
                            <td data-label="Total">{{ $score->total }}</td>
                            <td class="text-end" data-label="Actions">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-outline-primary btn-sm" href="{{ route('scoreSettings.edit', $score->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @if($score->module_status_count <= 0)
                                        <form action="{{ route('scoreSettings.destroy', $score->id) }}" method="POST" onsubmit="return confirm('Do you really want to Delete forever?');" class="m-0">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete scores">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </div>
                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">More</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('scoreSettings.edit', $score->id) }}"><i class="fa fa-edit me-2"></i>Edit Setting</a></li>
                                        @if($score->module_status_count <= 0)
                                            <li>
                                                <form action="{{ route('scoreSettings.destroy', $score->id) }}" method="POST" onsubmit="return confirm('Do you really want to Delete forever?');" class="m-0">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash me-2"></i>Delete Setting
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
