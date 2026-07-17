@extends('dashboard.student.trainingsindex')
@section('title', 'Pre Class Tests')

@section('css')
<style>
    .cbt-page .hero {
        background: linear-gradient(135deg, #111827 0%, #0f172a 50%, #2563eb 100%);
        color: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
    }

    .cbt-page .hero .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .cbt-page .hero h3 {
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .cbt-page .instruction-card,
    .cbt-page .test-card,
    .cbt-page .meta-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
    }

    .cbt-page .meta-card {
        background: rgba(255, 255, 255, .08);
        color: #fff;
        backdrop-filter: blur(8px);
    }

    .cbt-page .meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
    }

    .cbt-page .meta-label {
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .7);
        font-weight: 700;
    }

    .cbt-page .meta-value {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .cbt-page .test-card {
        background: #fff;
        height: 100%;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    .cbt-page .test-card:hover {
        transform: translateY(-2px);
        border-color: #cfe0ff;
        box-shadow: 0 14px 32px rgba(15, 23, 42, .10);
    }

    .cbt-page .test-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe, #eff6ff);
        color: #0b5ed7;
    }

    .cbt-page .tag-row {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem;
    }

    .cbt-page .tag-row .badge {
        border-radius: 999px;
        padding: .42rem .65rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .cbt-page .test-button {
        border-radius: 12px;
        min-height: 46px;
        font-weight: 700;
    }

    .cbt-page .empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
@php
    $moduleCount = $modules->count();
    $completedCount = $modules->where('completed', 1)->count();
@endphp

<div class="container-fluid py-3 cbt-page">
    <div class="hero mb-4 p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="eyebrow mb-3">
                    <i class="fa fa-clipboard-check"></i>
                    CBT Practice Area
                </span>
                <h3 class="mb-3">Pre Class Tests</h3>
                <p class="mb-4 text-white-50">
                    A cleaner, faster pre-test hub for mock assessments, timing, and quick start actions.
                </p>
                <div class="meta-grid">
                    <div class="meta-card p-3">
                        <div class="meta-label">Available</div>
                        <div class="meta-value">{{ $moduleCount }}</div>
                    </div>
                    <div class="meta-card p-3">
                        <div class="meta-label">Completed</div>
                        <div class="meta-value">{{ $completedCount }}</div>
                    </div>
                    <div class="meta-card p-3">
                        <div class="meta-label">In Progress</div>
                        <div class="meta-value">{{ max($moduleCount - $completedCount, 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="instruction-card p-3 p-lg-4 bg-white text-dark">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fa fa-info-circle text-primary"></i>
                        <strong>Instructions</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Tests are timed and will submit automatically when the timer runs out.</li>
                        <li class="mb-2">Certification tests expect typed answers.</li>
                        <li class="mb-2">Class tests use multiple choice responses.</li>
                        <li>Completed tests stay visible so you can review your progress.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.alerts')

    @if($modules->count() < 1)
        <div class="empty-state text-center">
            <h5 class="mb-2">No Pre Class Tests Available Yet</h5>
            <p class="text-muted mb-0">Please check back later for published mock tests.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($modules as $module)
                <div class="col-md-6 col-xl-4">
                    <div class="card test-card h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="test-icon">
                                    <i class="fa fa-list-alt fa-lg"></i>
                                </div>
                                @if($module->completed == 1)
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Ready</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <h5 class="mb-1 fw-bold">{{ $module->title }}</h5>
                                <div class="text-muted small">{{ $module->type }} assessment</div>
                            </div>

                            <div class="tag-row mb-3">
                                <span class="badge bg-light text-dark border">Questions: {{ $module->questions->count() }}</span>
                                <span class="badge bg-light text-dark border">Time: {{ $module->time }} mins</span>
                            </div>

                            <div class="test-footer mt-auto">
                                @if($module->completed == 0)
                                    <a href="{{ route('participants.mocks.show', ['mock' => $module->id, 'p_id' => $program->id]) }}"
                                       class="btn btn-info test-button w-100"
                                       onclick="return confirm('I have read the instructions above?');">
                                        <i class="fa fa-play me-1"></i>
                                        Start Now
                                    </a>
                                @else
                                    <button type="button" class="btn btn-success test-button w-100" disabled>
                                        <i class="fa fa-check me-1"></i>
                                        Pre Test Completed
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
