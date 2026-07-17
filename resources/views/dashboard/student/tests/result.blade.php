@extends('dashboard.student.trainingsindex')

@section('title', 'My Results')

@section('css')
<style>
    .results-page .hero {
        background: linear-gradient(135deg, #0f172a 0%, #111827 48%, #2563eb 100%);
        color: #fff;
        border-radius: 18px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        overflow: hidden;
    }

    .results-page .eyebrow {
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

    .results-page .summary-card,
    .results-page .result-card,
    .results-page .note-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
        background: #fff;
    }

    .results-page .summary-stat {
        border-radius: 14px;
        background: rgba(255, 255, 255, .08);
        backdrop-filter: blur(8px);
        padding: 1rem;
    }

    .results-page .summary-label {
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .7);
        font-weight: 700;
    }

    .results-page .summary-value {
        font-size: 1.4rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .results-page .result-card {
        height: 100%;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    .results-page .result-card:hover {
        transform: translateY(-2px);
        border-color: #cfe0ff;
        box-shadow: 0 14px 32px rgba(15, 23, 42, .10);
    }

    .results-page .result-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe, #eff6ff);
        color: #0b5ed7;
    }

    .results-page .pill-row {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .results-page .pill-row .badge {
        border-radius: 999px;
        padding: .42rem .7rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .results-page .score-box {
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        padding: .9rem 1rem;
        margin-bottom: .75rem;
    }

    .results-page .score-label {
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 700;
        margin-bottom: .35rem;
    }

    .results-page .score-value {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
    }

    .results-page .accent-green {
        color: #15803d;
    }

    .results-page .accent-red {
        color: #dc2626;
    }

    .results-page .note-card {
        border-style: dashed;
        background: #f8fafc;
    }

    .results-page .empty-state {
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
@php
    $mockByModule = $mock_results->keyBy('module_id');
    $resultCount = $results->count();
    $moduleCount = $results->pluck('module_id')->unique()->count();
    $certifiedModules = $results->where('module.type', 'Certification Test')->count();
    $classModules = $results->where('module.type', 'Class Test')->count();
    $certScore = $results->first()?->certification_test_score ?? 0;
@endphp

<div class="container-fluid py-3 results-page">
    <div class="hero mb-4 p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="eyebrow mb-3">
                    <i class="fa fa-chart-line"></i>
                    Assessment Results
                </span>
                <h3 class="mb-3">My Completed Tests</h3>
                <p class="mb-4 text-white-50">
                    Review your pre-class and post-class performance in a cleaner dashboard view.
                    Pre-class results are shown separately and do not feed the final score summary.
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="summary-stat">
                            <div class="summary-label">Completed Tests</div>
                            <div class="summary-value">{{ $resultCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-stat">
                            <div class="summary-label">Modules Covered</div>
                            <div class="summary-value">{{ $moduleCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-stat">
                            <div class="summary-label">Certification Score</div>
                            <div class="summary-value">{{ $certScore }}%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="note-card p-3 p-lg-4 bg-white text-dark">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fa fa-info-circle text-primary"></i>
                        <strong>Notes</strong>
                    </div>
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Pre-class results are not included in the final result computation.</li>
                        <li class="mb-2">Retake is available where the module permits it.</li>
                        <li>Comments appear when a grader or facilitator has left feedback.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.alerts')

    @if($results->count() < 1)
        <div class="empty-state text-center">
            <h5 class="mb-2">No completed tests yet</h5>
            <p class="text-muted mb-0">Go back to the tests page and take a post-class test to see your results here.</p>
        </div>
    @else
        <div class="mb-4">
            <h5 class="fw-bold mb-1">Overall Test Results</h5>
            <p class="text-muted mb-0">Your latest scores are grouped by module below.</p>
        </div>

        <div class="row g-4">
            @foreach($results as $result)
                @php
                    $module = $result->module;
                    $mockResult = $mockByModule->get($module->id);
                    $showComments = (isset($result->grader_comment) && !empty($result->grader_comment)) || (isset($result->facilitator_comment) && !empty($result->facilitator_comment));
                    $scoreColor = $result->module->type == 'Class Test'
                        ? (in_array($result->redo_test, [0, 2]) ? 'accent-green' : 'accent-red')
                        : (($result->certification_test_score ?? 0) > 0 && in_array($result->redo_test, [0, 2]) ? 'accent-green' : 'accent-red');
                @endphp

                <div class="col-md-6 col-xl-4">
                    <div class="card result-card h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="result-icon">
                                    <i class="fa fa-list-alt fa-lg"></i>
                                </div>
                                <span class="badge bg-success">Completed</span>
                            </div>

                            <div class="mb-3">
                                <h5 class="mb-1 fw-bold">{{ $program->p_name }}</h5>
                                <div class="text-muted small">{{ $module->title }}</div>
                            </div>

                            <div class="pill-row mb-3">
                                <span class="badge bg-light text-dark border">{{ $module->type }}</span>
                                @if($module->type == 'Class Test')
                                    <span class="badge bg-primary">Class Test</span>
                                @else
                                    <span class="badge bg-info text-light">Certification</span>
                                @endif
                                @if($mockResult)
                                    <span class="badge bg-secondary">Pre-test linked</span>
                                @endif
                            </div>

                            <div class="score-box">
                                <div class="score-label">Post Class Test Score</div>
                                <div class="score-value {{ $scoreColor }}">
                                    @if($module->type == 'Class Test')
                                        {{ in_array($result->redo_test, [0, 2]) ? $result->class_test_score . '/' . $module->noofquestions : 'Processing' }}
                                    @else
                                        {{ ($result->certification_test_score > 0 && in_array($result->redo_test, [0, 2])) ? $result->certification_test_score . '/' . $program->scoresettings->certification : 'Processing' }}
                                    @endif
                                </div>
                            </div>

                            @if($mockResult)
                                <div class="score-box">
                                    <div class="score-label">Pre Class Test Score</div>
                                    <div class="score-value">
                                        @if($mockResult->module->type == 'Class Test')
                                            {{ $mockResult->class_test_score . '/' . $mockResult->module->noofquestions }}
                                        @else
                                            {{ $mockResult->certification_test_score ?? 'Processing' }}
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($result->module->type == 'Class Test' && $result->module->allow_test_retake == 1)
                                @php
                                    $details = certificationStatus($program->id, resolveAuthUser()->id);
                                @endphp
                                @if($details['status'] == 'NOT CERTIFIED')
                                    <a onclick="return confirm('This will clear all your scores for this module. Are you sure you want to do this?');"
                                       href="{{ route('user.retake.module.test', ['module' => $result->module_id, 'p_id' => $result->program_id]) }}"
                                       class="btn btn-outline-danger btn-sm w-100 test-footer">
                                        <i class="fas fa-redo me-1"></i> Retake Module
                                    </a>
                                @endif
                            @endif

                            @if($showComments)
                                <a style="width:auto"
                                   href="{{ route('participants.tests.results.comment', ['id'=>$result->id, 'p_id'=>$program->id]) }}"
                                   class="btn btn-info btn-sm w-100 mt-2">
                                    <i class="fa fa-eye me-1"></i> View Comments
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
