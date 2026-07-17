@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' Questions' }}
@endsection
@section('content')
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Question Bank</span>
                            <h1 class="h3 fw-bold mb-2">Training Questions</h1>
                            <p class="text-muted mb-0">Select a training to manage its questions. Trainings without questions are hidden automatically.</p>
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
                            <th>Questions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs_with_questions as $programs)
                            @if($programs->questions_count > 0)
                                <tr>
                                    <td data-label="#">
                                        {{ $i++ }}
                                    </td>
                                    <td data-label="Training">
                                        <span class="btn btn-outline-secondary btn-sm disabled">{{ $programs->p_name }}</span>
                                    </td>
                                    <td data-label="Questions">
                                        <span class="badge bg-light text-dark rounded-pill">{{ $programs->questions_count }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
