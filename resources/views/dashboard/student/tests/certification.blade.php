@extends('dashboard.student.trainingsindex')
@section('title', 'My Tests')

@section('css')
<style>
    .cbt-exam {
        --panel-bg: #ffffff;
        --panel-border: #e5e7eb;
        --panel-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        --accent: #0b5ed7;
    }

    .cbt-exam .exam-hero {
        background: linear-gradient(135deg, #111827 0%, #0f172a 50%, #2563eb 100%);
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

    .cbt-exam .instruction-card,
    .cbt-exam .question-card,
    .cbt-exam .submit-bar {
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

    .cbt-exam .answer-field {
        border-radius: 14px;
        border-color: #dbe3ec;
        min-height: 220px;
        resize: vertical;
    }

    .cbt-exam .answer-field:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .15);
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

    .cbt-exam .submit-bar {
        position: sticky;
        bottom: 1rem;
        z-index: 5;
        background: rgba(255, 255, 255, .9);
        backdrop-filter: blur(10px);
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

        .cbt-exam .answer-field {
            min-height: 180px;
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
                    <i class="fa fa-pen-fancy"></i>
                    Live Certification
                </span>
                <h3 class="mb-3 hero-title">{{ $module_title }}</h3>
                <p class="mb-4 text-white-50 mb-lg-0 hero-copy">
                    Training: <strong class="text-white">{{ $program_name }}</strong>
                    <br>
                    Write your answers clearly and keep them concise.
                </p>
            </div>
            <div class="col-lg-4">
                @if($time > 0)
                    <div class="timer-box text-lg-end">
                        <div class="timer-label">Time Remaining</div>
                        <div class="timer-value" id="quiz-time-left">--:--</div>
                        <div class="small text-white-50 mt-2">The exam will submit automatically when time elapses.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('layouts.partials.alerts')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <span class="summary-pill"><i class="fa fa-edit text-primary"></i> Type your answers below</span>
                    <span class="summary-pill"><i class="fa fa-list-ol text-primary"></i> Questions: {{ $questions->count() }}</span>
                    @if($time > 0)
                        <span class="summary-pill"><i class="fa fa-clock text-primary"></i> Time limit: {{ $time }} minutes</span>
                    @endif
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
                                    <div class="fw-semibold text-dark">
                                        {!! $question->title !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="question-body">
                            <label for="text{{ $question->id }}" class="form-label fw-bold">
                                Your answer
                                <strong class="text-success">(Maximum words: 500)</strong>
                            </label>
                            <textarea
                                id="text{{ $question->id }}"
                                class="form-control answer-field answer"
                                name="{{ $question->id }}"
                                rows="8"
                                placeholder="Type your answer for question {{ $i - 1 }} here"
                                required></textarea>
                            <div class="mt-2 d-flex justify-content-between align-items-center small">
                                <span class="text-muted">Keep answers focused and well structured.</span>
                                <span class="fw-bold" id="wordcount{{ $question->id }}">0 / 500 words</span>
                            </div>
                        </div>
                    </div>

                    <script>
                        (function() {
                            var editor = CKEDITOR.replace("text{{ $question->id }}");

                            function countWords(html) {
                                var text = $('<div>').html(html || '').text().replace(/\s+/g, ' ').trim();
                                if (!text) {
                                    return 0;
                                }
                                return text.split(' ').length;
                            }

                            function updateWordCount() {
                                var words = countWords(editor.getData());
                                var label = $('#wordcount{{ $question->id }}');
                                label.text(words + ' / 500 words');
                                label.toggleClass('text-danger', words > 500);
                                label.toggleClass('text-success', words > 0 && words <= 500);
                            }

                            editor.on('instanceReady', updateWordCount);
                            editor.on('change', updateWordCount);
                            editor.on('keyup', updateWordCount);
                            editor.on('paste', function() {
                                setTimeout(updateWordCount, 0);
                            });
                        })();
                    </script>
                @endforeach

                <div class="submit-bar">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="text-muted small">
                            Review your responses before submitting. If time expires, the system will submit automatically.
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-paper-plane me-1"></i> Submit Certification
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <div class="instruction-card p-3 p-lg-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fa fa-info-circle text-primary"></i>
                    <strong>Writing Tips</strong>
                </div>
                <ul class="mb-0 ps-3">
                    <li class="mb-2">Answer in complete sentences where appropriate.</li>
                    <li class="mb-2">Stay within the word limit for each answer.</li>
                    <li class="mb-2">Do not refresh the page while writing.</li>
                    <li>Submit once you have reviewed all your responses.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if($time > 0)
<script>
    var total_seconds = {{ (int) $remainingSeconds }};

    function formatRemaining(seconds) {
        var minutes = parseInt(seconds / 60);
        var secs = parseInt(seconds % 60);

        return minutes + 'm ' + secs + 's';
    }

    document.getElementById("quiz-time-left").innerHTML = formatRemaining(total_seconds);

    function init() {
        document.getElementById("quiz-time-left").innerHTML = formatRemaining(total_seconds);
        setTimeout("CheckTime()", 999);
    }

    function CheckTime() {
        document.getElementById("quiz-time-left").innerHTML = formatRemaining(total_seconds);
        if (total_seconds <= 0) {
            setTimeout('document.quiz.submit()', 1);
        } else {
            total_seconds = total_seconds - 1;
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
@endif
@endsection
