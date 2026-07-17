@extends('dashboard.admin.index')
@section('title', 'View/Update Module')
@section('css')
<style>
    .module-edit-card,
    .question-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
    }

    .module-edit-card .form-control,
    .module-edit-card .select2-selection--single,
    .question-card .form-control {
        border-radius: 12px;
    }

    .module-edit-card .builder-note {
        color: #64748b;
        font-size: .88rem;
    }
</style>
@endsection
@section('content')
@php
    $questionRows = old('questions');
    if (!is_array($questionRows) || empty($questionRows)) {
        $questionRows = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'title' => $question->title,
                'optionA' => $question->optionA,
                'optionB' => $question->optionB,
                'optionC' => $question->optionC,
                'optionD' => $question->optionD,
                'correct' => $question->correct,
            ];
        })->values()->all();
    }
    if (empty($questionRows)) {
        $questionRows = [[]];
    }
    $moduleTypeValue = old('type', $module->type);
    $showObjectiveFields = in_array((string) $moduleTypeValue, ['0', 'Class Test'], true);
    $moduleQuestionsLimit = old('noofquestions', $module->noofquestions);
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero module-edit-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Module Manager</span>
                            <h1 class="h3 fw-bold mb-2">{{ $module->title }}</h1>
                            <p class="text-muted mb-0">Update the module details and its question set from one screen.</p>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <form action="{{ route('modules.questions.sync', $module->id) }}" method="POST" id="module-question-builder-form">
        @csrf

        <div class="card border-0 shadow-sm module-edit-card mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-3">
                    <div class="col-12">
                        <h4 class="mb-0">Module Details</h4>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? $module->title }}" autofocus required>
                        @error('title')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="program_id" class="form-label">Training</label>
                        <input type="text" readonly value="{{ $program->p_name }}" class="form-control" required>
                        <input type="hidden" id="program_id" name="program_id" value="{{ $program->id }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="type" class="form-label">Type</label>
                        @if($module->questions->count() <= 0)
                            <select name="type" id="type" class="form-control" required>
                                <option value="0" {{ (string) $moduleTypeValue === '0' || $moduleTypeValue === 'Class Test' ? 'selected' : '' }}>Class Test</option>
                                <option value="1" {{ (string) $moduleTypeValue === '1' || $moduleTypeValue === 'Certification Test' ? 'selected' : '' }}>Certification Test</option>
                            </select>
                        @else
                            <input type="hidden" id="type" name="type" value="{{ (string) $moduleTypeValue === '1' || $moduleTypeValue === 'Certification Test' ? 1 : 0 }}">
                            <input type="text" class="form-control" value="{{ $module->type }}" readonly>
                            <div class="text-muted small mt-1">Type is locked because this module already has questions.</div>
                        @endif
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="0" {{ old('status', $module->status) == 0 ? 'selected' : '' }}>Disabled</option>
                            <option value="1" {{ old('status', $module->status) == 1 ? 'selected' : '' }}>Enabled</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="computation_status" class="form-label">Computation Status</label>
                        <select name="computation_status" id="computation_status" class="form-control">
                            <option value="1" {{ old('computation_status', $module->computation_status) == 1 ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ old('computation_status', $module->computation_status) == 0 ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="noofquestions" class="form-label">No of Questions</label>
                        <input id="noofquestions" type="number" class="form-control" name="noofquestions" value="{{ $moduleQuestionsLimit }}" min="1" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="time" class="form-label">Question Time (minutes)</label>
                        <input id="time" type="number" class="form-control" name="time" value="{{ old('time') ?? $module->time }}" min="0" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="allow_test_retake" class="form-label">Allow Test Retake</label>
                        <select name="allow_test_retake" class="form-control" id="allow_test_retake" required>
                            <option value="1" {{ old('allow_test_retake', $module->allow_test_retake) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('allow_test_retake', $module->allow_test_retake) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm module-edit-card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-4">
                    <div>
                        <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Questions</span>
                        <h4 class="mb-1">Inline Question Builder</h4>
                        <div class="builder-note">Add or remove cards here. The number of cards should not exceed the module limit above.</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importQuestionsModal">
                            <i class="fa fa-upload me-1"></i> Import Questions
                        </button>
                    </div>
                </div>

                <div id="question-collection" class="d-grid gap-4">
                    @foreach($questionRows as $index => $row)
                        @include('dashboard.admin.questions.partials.question-card', [
                            'index' => $index,
                            'row' => $row,
                            'allowRemove' => count($questionRows) > 1,
                            'showObjectiveFields' => $showObjectiveFields,
                        ])
                    @endforeach
                </div>

                <div class="card border-0 shadow-sm mt-4 module-edit-card">
                    <div class="card-body d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                        <div class="text-muted small">Save updates the module and all attached questions in one step.</div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-primary" id="add-question-btn">
                                <i class="fa fa-plus me-1"></i> Add Question
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="save-module-questions-btn">Save Module &amp; Questions</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="question-card-template">
    @include('dashboard.admin.questions.partials.question-card', [
        'index' => '__INDEX__',
        'row' => [],
        'allowRemove' => true,
        'showObjectiveFields' => $showObjectiveFields,
    ])
</template>

@include('dashboard.admin.questions.partials.import-modal', [
    'modalId' => 'importQuestionsModal',
    'p_id' => $module->program_id,
])
@endsection
@section('extra-scripts')
<script>
    (function () {
        const collection = document.getElementById('question-collection');
        const addButton = document.getElementById('add-question-btn');
        const saveButton = document.getElementById('save-module-questions-btn');
        const template = document.getElementById('question-card-template').innerHTML;
        const typeField = document.getElementById('type');
        const limitField = document.getElementById('noofquestions');
        const compactEditorConfig = {
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
        };
        let nextIndex = collection.querySelectorAll('[data-question-card]').length;

        function getModuleTypeValue() {
            if (!typeField) {
                return '';
            }

            return typeField.value;
        }

        function getQuestionLimit() {
            const parsed = parseInt(limitField.value, 10);
            return Number.isNaN(parsed) ? 0 : parsed;
        }

        function destroyQuestionEditor(card) {
            const textarea = card.querySelector('textarea[data-question-title-editor]');
            if (textarea && textarea.id && CKEDITOR.instances[textarea.id]) {
                CKEDITOR.instances[textarea.id].destroy(true);
            }
        }

        function destroyQuestionEditors() {
            Object.keys(CKEDITOR.instances).forEach((id) => {
                const editor = CKEDITOR.instances[id];
                const element = editor.element && editor.element.$;
                if (element && collection.contains(element)) {
                    editor.destroy(true);
                }
            });
        }

        function initQuestionEditors(root = collection) {
            root.querySelectorAll('textarea[data-question-title-editor]').forEach((textarea) => {
                if (!textarea.id || CKEDITOR.instances[textarea.id]) {
                    return;
                }
                CKEDITOR.replace(textarea.id, compactEditorConfig);
            });
        }

        function setFieldState(field, enabled) {
            field.disabled = !enabled;
            if (enabled) {
                field.removeAttribute('tabindex');
            } else {
                field.setAttribute('tabindex', '-1');
            }
        }

        function applyModuleMode() {
            const showObjectives = getModuleTypeValue() === '0';

            collection.querySelectorAll('[data-question-card]').forEach((card) => {
                card.querySelectorAll('[data-objective-group]').forEach((group) => {
                    group.classList.toggle('d-none', !showObjectives);
                    group.querySelectorAll('input, textarea, select').forEach((field) => {
                        setFieldState(field, showObjectives);
                    });
                });
            });
        }

        function updateAddState() {
            const limit = getQuestionLimit();
            const count = collection.querySelectorAll('[data-question-card]').length;
            addButton.disabled = limit > 0 && count >= limit;
            saveButton.disabled = limit > 0 && count > limit;
        }

        function refreshBuilder(root = collection) {
            initQuestionEditors(root);
            applyModuleMode();
            updateAddState();
        }

        function addCard() {
            const limit = getQuestionLimit();
            const count = collection.querySelectorAll('[data-question-card]').length;
            if (limit > 0 && count >= limit) {
                return;
            }

            const html = template.replace(/__INDEX__/g, nextIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const newCard = wrapper.firstElementChild;
            collection.appendChild(newCard);
            nextIndex += 1;
            refreshBuilder(newCard);
        }

        addButton.addEventListener('click', addCard);

        limitField.addEventListener('input', updateAddState);

        if (typeField && typeField.tagName === 'SELECT') {
            typeField.addEventListener('change', function () {
                applyModuleMode();
            });
        }

        collection.addEventListener('click', function (event) {
            const removeButton = event.target.closest('[data-remove-question]');
            if (!removeButton) {
                return;
            }

            event.preventDefault();
            const cards = collection.querySelectorAll('[data-question-card]');
            if (cards.length === 1) {
                return;
            }

            const card = removeButton.closest('[data-question-card]');
            destroyQuestionEditor(card);
            card.remove();
            refreshBuilder();
        });

        $(document).ready(function () {
            initQuestionEditors();
            applyModuleMode();
            updateAddState();
        });
    })();
</script>
@endsection
