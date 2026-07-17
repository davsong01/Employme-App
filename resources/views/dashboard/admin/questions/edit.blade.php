@extends('dashboard.admin.index')
@section('title', 'Edit Question')
@section('css')
<style>
    .question-edit-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
    }

    .question-edit-card .form-control,
    .question-edit-card .select2-selection--single {
        border-radius: 12px;
    }
</style>
@endsection
@section('content')
@php
    $optionValues = [
        'A' => old('optionA') ?? $question->optionA,
        'B' => old('optionB') ?? $question->optionB,
        'C' => old('optionC') ?? $question->optionC,
        'D' => old('optionD') ?? $question->optionD,
    ];
    $isCertification = $question->module->type === 'Certification Test';
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero question-edit-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Question Bank</span>
                            <h1 class="h3 fw-bold mb-2">Edit Question</h1>
                            <p class="text-muted mb-0">Update the question text and answer choices without leaving the question workflow.</p>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm question-edit-card">
        <div class="card-body">
            <form action="{{ route('questions.update', $question->id) }}" method="POST" class="pb-2">
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold text-muted">Associated Module</label>
                        <input type="text" class="form-control" value="{{ $question->module->title }}" readonly>
                        <input type="hidden" name="module_id" value="{{ $question->module->id }}">
                        @if($isCertification)
                            <div class="text-muted small mt-2">This module uses title-only certification questions, so answer options are hidden here.</div>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="title">Question Title</label>
                        <textarea id="title" class="form-control" name="title" rows="4" required>{!! old('title') ?? $question->title !!}</textarea>
                    </div>

                    @if(!$isCertification)
                        <div class="col-12">
                            <div class="row g-3">
                                @foreach(['A', 'B', 'C', 'D'] as $letter)
                                    <div class="col-12">
                                        <label class="form-label small text-uppercase fw-semibold text-muted" for="option{{ $letter }}">Option {{ $letter }}</label>
                                        <input id="option{{ $letter }}" type="text" class="form-control" name="option{{ $letter }}" value="{{ $optionValues[$letter] }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small text-uppercase fw-semibold text-muted">Correct Answer</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(['A', 'B', 'C', 'D'] as $letter)
                                    @php $radioId = 'edit_correct_' . $letter; @endphp
                                    <input type="radio" class="btn-check" name="correct" value="{{ $letter }}" id="{{ $radioId }}" {{ $question->correct == $letter ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary {{ $question->correct == $letter ? 'active' : '' }}" for="{{ $radioId }}">{{ $letter }}</label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace('title', {
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'editing', items: ['Undo', 'Redo'] }
        ],
        removeButtons: 'Subscript,Superscript,Anchor,Image,Table,HorizontalRule,SpecialChar,Source,Maximize,Scayt,About',
        removePlugins: 'elementspath,flash,forms,iframe,smiley,showblocks',
        extraPlugins: '',
        height: 180
    });
</script>
@endsection
