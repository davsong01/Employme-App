@extends('dashboard.student.trainingsindex')
@section('title', 'My Tests')

@section('css')
<style>
    .cbt-exam {
        --panel-bg: #ffffff;
        --panel-border: #e5e7eb;
        --panel-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        --accent: #0b5ed7;
        --accent-soft: #dbeafe;
    }

    .cbt-exam .exam-hero {
        background: linear-gradient(135deg, #0f172a 0%, #111827 55%, #2563eb 100%);
        color: #fff;
        border-radius: 18px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .18);
        overflow: hidden;
        position: sticky;
        top: 1rem;
        z-index: 20;
        transition: padding .18s ease, border-radius .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .cbt-exam .exam-hero.is-compact {
        border-radius: 14px;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .16);
    }

    .cbt-exam .exam-hero.is-pinned {
        position: fixed;
        top: 1rem;
        left: 280px;
        right: 1rem;
        z-index: 40;
        padding: .55rem .85rem !important;
    }

    .cbt-exam .exam-hero.is-pinned .hero-stack {
        align-items: center !important;
        gap: .25rem !important;
    }

    .cbt-exam .exam-hero.is-pinned .col-lg-8,
    .cbt-exam .exam-hero.is-pinned .col-lg-4 {
        width: auto;
        flex: 1 1 auto;
    }

    .cbt-exam .exam-hero.is-pinned .col-lg-4 {
        flex: 0 0 auto;
        margin-left: auto;
    }

    .cbt-exam .exam-hero .eyebrow {
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

    .cbt-exam .timer-box {
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        backdrop-filter: blur(8px);
        transition: padding .18s ease, border-radius .18s ease, transform .18s ease;
    }

    .cbt-exam .exam-hero.is-compact .timer-box {
        padding: .65rem .9rem;
        border-radius: 12px;
    }

    .cbt-exam .timer-label {
        font-size: .72rem;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .7);
        font-weight: 700;
        margin-bottom: .25rem;
    }

    .cbt-exam .timer-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.1;
        transition: font-size .18s ease;
    }

    .cbt-exam .exam-hero.is-compact .timer-value {
        font-size: 1.15rem;
    }

    .cbt-exam .exam-hero.is-compact .eyebrow,
    .cbt-exam .exam-hero.is-compact .hero-copy {
        display: none;
    }

    .cbt-exam .exam-hero.is-compact .hero-title {
        margin-bottom: 0 !important;
        font-size: 1.15rem;
    }

    .cbt-exam .exam-hero.is-compact .hero-stack {
        gap: .5rem !important;
    }

    .cbt-exam .exam-hero.is-pinned .eyebrow,
    .cbt-exam .exam-hero.is-pinned .hero-copy,
    .cbt-exam .exam-hero.is-pinned .timer-label {
        display: none;
    }

    .cbt-exam .exam-hero.is-pinned .hero-title {
        margin-bottom: 0 !important;
        font-size: 1rem;
        line-height: 1.2;
    }

    .cbt-exam .exam-hero.is-pinned .timer-box {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: 0;
        background: transparent;
        border: 0;
        backdrop-filter: none;
    }

    .cbt-exam .exam-hero.is-pinned .timer-value {
        font-size: 1rem;
    }

    .cbt-exam .exam-hero.is-pinned .timer-box .small {
        display: none;
    }

    .cbt-exam .test-summary,
    .cbt-exam .instruction-card,
    .cbt-exam .question-card {
        border: 1px solid var(--panel-border);
        border-radius: 16px;
        background: var(--panel-bg);
        box-shadow: var(--panel-shadow);
    }

    .cbt-exam .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .45rem .75rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #334155;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .cbt-exam .question-card {
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .cbt-exam .question-header {
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        padding: 1rem 1.25rem;
    }

    .cbt-exam .question-number {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe, #eff6ff);
        color: var(--accent);
        font-weight: 800;
        flex: 0 0 auto;
    }

    .cbt-exam .question-body {
        padding: 1.25rem;
    }

    .cbt-exam .option-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .cbt-exam .option-card {
        position: relative;
        border: 1px solid #dbe3ec;
        border-radius: 14px;
        padding: .9rem 1rem;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .cbt-exam .option-card:hover {
        border-color: #bfd4f7;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .06);
        transform: translateY(-1px);
    }

    .cbt-exam .option-card input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .cbt-exam .option-card .option-label {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: 0;
        cursor: pointer;
    }

    .cbt-exam .option-dot {
        width: 18px;
        height: 18px;
        border-radius: 999px;
        border: 2px solid #94a3b8;
        flex: 0 0 auto;
        position: relative;
    }

    .cbt-exam .option-card input:checked + .option-label .option-dot {
        border-color: var(--accent);
        background: var(--accent);
        box-shadow: inset 0 0 0 4px #fff;
    }

    .cbt-exam .option-text {
        font-weight: 600;
        color: #0f172a;
        line-height: 1.35;
    }

    .cbt-exam .question-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .cbt-exam .question-meta .badge {
        border-radius: 999px;
        padding: .38rem .65rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .cbt-exam .exam-sticky {
        position: sticky;
        top: 1rem;
        z-index: 10;
    }

    .cbt-exam .submit-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 5;
        background: rgba(255, 255, 255, .9);
        backdrop-filter: blur(10px);
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        padding: 1rem;
    }

    .cbt-exam .submit-bar .btn {
        min-height: 48px;
        border-radius: 12px;
        font-weight: 700;
    }

    .cbt-exam .instruction-card {
        position: sticky;
        top: 6.5rem;
    }

    @media (max-width: 767.98px) {
        .cbt-exam .exam-hero {
            top: .5rem;
        }

        .cbt-exam .exam-hero.is-pinned {
            left: .5rem;
            right: .5rem;
            top: .5rem;
        }

        .cbt-exam .exam-hero.is-pinned .col-lg-4 {
            margin-left: 0;
        }

        .cbt-exam .exam-hero.is-compact .timer-value {
            font-size: 1rem;
        }

        .cbt-exam .option-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
@php
    $firstQuestion = $questions->first();
@endphp

<div class="container-fluid py-3 cbt-exam">
    <div class="exam-hero mb-4 p-4 p-lg-5" id="examHero">
        <div class="row align-items-center g-4 hero-stack">
            <div class="col-lg-8">
                <span class="eyebrow mb-3">
                    <i class="fa fa-pencil-alt"></i>
                    Live CBT
                </span>
                <h3 class="mb-3 hero-title">{{ $module_title }}</h3>
                <p class="mb-4 text-white-50 mb-lg-0 hero-copy">
                    Training: <strong class="text-white">{{ $program_name }}</strong>
                    <br>
                    Answer each question carefully before submitting.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="timer-box text-lg-end">
                    <div class="timer-label">Time Remaining</div>
                    <div class="timer-value" id="quiz-time-left">--:--</div>
                    <div class="small text-white-50 mt-2">The exam will submit automatically when time elapses.</div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.partials.alerts')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="test-summary p-3 p-lg-4 mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <span class="summary-pill"><i class="fa fa-book-open text-primary"></i> Select the correct answer</span>
                    <span class="summary-pill"><i class="fa fa-list-ol text-primary"></i> Questions: {{ $questions->count() }}</span>
                    <span class="summary-pill"><i class="fa fa-clock text-primary"></i> Time limit: {{ $time }} minutes</span>
                </div>
            </div>

            <form name="quiz" id="quiz_form" action="{{ route('participants.tests.store', ['p_id' => $program->id]) }}" method="POST" class="pb-2">
                {{ csrf_field() }}
                <input type="hidden" name="mod_id" value="{{ $firstQuestion?->module?->id }}">

                @foreach($questions as $question)
                    <div class="question-card">
                        <div class="question-header">
                            <div class="d-flex align-items-start gap-3">
                                <div class="question-number">{{ $i++ }}</div>
                                <div class="flex-grow-1">
                                    <div class="question-meta mb-2">
                                        <span class="badge bg-primary">Question {{ $i - 1 }}</span>
                                        <span class="badge bg-light text-dark border">{{ $question->module->type }}</span>
                                    </div>
                                    <div class="fw-semibold text-dark question-text">
                                        {!! $question->title !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="question-body">
                            <div class="option-grid">
                                @foreach([
                                    'A' => $question->optionA,
                                    'B' => $question->optionB,
                                    'C' => $question->optionC,
                                    'D' => $question->optionD,
                                ] as $key => $option)
                                    <label class="option-card">
                                        <input type="radio" name="{{ $question->id }}" value="{{ $key }}" required>
                                        <span class="option-label">
                                            <span class="option-dot"></span>
                                            <span class="option-text">{{ $option }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="submit-bar">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="text-muted small">
                            Review your answers before submitting. The timer will continue running while you work.
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-paper-plane me-1"></i> Submit Test
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="instruction-card p-3 p-lg-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa fa-info-circle text-primary"></i>
                    <strong>Exam Instructions</strong>
                </div>
                <ul class="mb-0 ps-3">
                    <li class="mb-2">Make sure you pick one answer for every question.</li>
                    <li class="mb-2">Do not refresh the page during the test.</li>
                    <li class="mb-2">The exam will auto-submit once the timer runs out.</li>
                    <li>Use the submit button whenever you are done.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    var max_time = {{$time}};
    var c_seconds = 0;
    var total_seconds = 60 * max_time;
    max_time = parseInt(total_seconds / 60);
    c_seconds = parseInt(total_seconds % 60);
    document.getElementById("quiz-time-left").innerHTML = max_time + 'm ' + c_seconds + 's';

    function init() {
        document.getElementById("quiz-time-left").innerHTML = max_time + 'm ' + c_seconds + 's';
        setTimeout("CheckTime()", 999);
    }

    function CheckTime() {
        document.getElementById("quiz-time-left").innerHTML = max_time + 'm ' + c_seconds + 's';
        if (total_seconds <= 0) {
            setTimeout('document.quiz.submit()', 1);
        } else {
            total_seconds = total_seconds - 1;
            max_time = parseInt(total_seconds / 60);
            c_seconds = parseInt(total_seconds % 60);
            setTimeout("CheckTime()", 999);
        }
    }

    init();

    (function () {
        var hero = document.getElementById('examHero');
        if (!hero) {
            return;
        }

        var compactAt = 60;
        var pinAt = hero.offsetTop;

        function syncHeroState() {
            hero.classList.toggle('is-compact', window.scrollY > compactAt);
            hero.classList.toggle('is-pinned', window.scrollY > pinAt);
        }

        window.addEventListener('scroll', syncHeroState, { passive: true });
        syncHeroState();
    })();
</script>
@endsection
