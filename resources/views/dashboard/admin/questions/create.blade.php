@extends('dashboard.admin.index')
@section('title', 'Add New Question')
@section('css')
<style>
    .question-builder-card,
    .question-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
    }

    .question-card .form-control,
    .question-builder-card .form-control,
    .question-builder-card .select2-selection--single {
        border-radius: 12px;
    }

    .builder-note {
        color: #64748b;
        font-size: .88rem;
    }
</style>
@endsection
@section('content')
@php
    $questionRows = old('questions');
    if (!is_array($questionRows) || empty($questionRows)) {
        $questionRows = [[]];
    }
    $selectedModuleId = old('module', $selectedModuleId ?? request('module_id'));
    $selectedModuleType = optional($modules->firstWhere('id', $selectedModuleId))->type ?? ($selectedModuleType ?? '');
    $importProgramId = request('p_id') ?? optional($modules->first())->program_id;
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero question-builder-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Question Bank</span>
                            <h1 class="h3 fw-bold mb-2">Batch Question Builder</h1>
                            <p class="text-muted mb-0">Add multiple questions for one module, remove any card you do not need, and choose the correct answer inline.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if($importProgramId)
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importQuestionsModal">
                                    <i class="fa fa-upload me-1"></i> Import Questions
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="builder-note mt-3">Tip: certification modules use title-only questions, while class tests still use options and a correct answer.</div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <form action="{{ route('questions.store') }}" method="POST" id="question-builder-form" class="pb-2">
        @csrf
        <input type="hidden" name="p_id" value="{{ request('p_id') }}">

        <div class="card border-0 shadow-sm mb-4 question-builder-card">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-8">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="module">Associated Module</label>
                        <select name="module" id="module" class="form-control select2" required>
                            <option value="">Select module</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module->id }}" data-module-type="{{ $module->type }}" {{ (string) $selectedModuleId === (string) $module->id ? 'selected' : '' }}>
                                    {{ $module->title }} ({{ max(0, $module->noofquestions - $module->questions_count) }} question(s) left)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div id="question-collection" class="d-grid gap-4">
            @foreach($questionRows as $index => $row)
                @include('dashboard.admin.questions.partials.question-card', [
                    'index' => $index,
                    'row' => $row,
                    'allowRemove' => count($questionRows) > 1,
                    'showObjectiveFields' => $selectedModuleType !== 'Certification Test',
                ])
            @endforeach
        </div>

        <div class="card border-0 shadow-sm mt-4 question-builder-card">
            <div class="card-body d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                <div class="text-muted small">Use Add Question to stack more entries, then save once at the end.</div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary" id="add-question-btn">
                        <i class="fa fa-plus me-1"></i> Add Question
                    </button>
                    <button type="submit" class="btn btn-primary px-4">Save Questions</button>
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
        'showObjectiveFields' => $selectedModuleType !== 'Certification Test',
    ])
</template>

@if($importProgramId)
    @include('dashboard.admin.questions.partials.import-modal', [
        'modalId' => 'importQuestionsModal',
        'p_id' => $importProgramId,
    ])
@endif
@endsection
@section('extra-scripts')
<script>
    (function () {
        const collection = document.getElementById('question-collection');
        const addButton = document.getElementById('add-question-btn');
        const template = document.getElementById('question-card-template').innerHTML;
        const moduleSelect = document.getElementById('module');
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

        function getSelectedModuleType() {
            const selectedOption = moduleSelect.options[moduleSelect.selectedIndex];
            return selectedOption ? (selectedOption.dataset.moduleType || '') : '';
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
            const moduleType = getSelectedModuleType();
            const showObjectives = moduleType !== 'Certification Test';

            collection.querySelectorAll('[data-question-card]').forEach((card) => {
                card.querySelectorAll('[data-objective-group]').forEach((group) => {
                    group.classList.toggle('d-none', !showObjectives);
                    group.querySelectorAll('input, textarea, select').forEach((field) => {
                        setFieldState(field, showObjectives);
                    });
                });

                card.querySelectorAll('[data-correct-group]').forEach((group) => {
                    group.classList.toggle('d-none', !showObjectives);
                    group.querySelectorAll('input, textarea, select').forEach((field) => {
                        setFieldState(field, showObjectives);
                    });
                });
            });
        }

        function addCard() {
            const html = template.replace(/__INDEX__/g, nextIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            const newCard = wrapper.firstElementChild;
            collection.appendChild(newCard);
            nextIndex += 1;
            initQuestionEditors(newCard);
            applyModuleMode();
            $(newCard).find('.select2').select2({
                width: '100%',
                allowClear: true
            });
        }

        addButton.addEventListener('click', addCard);
        moduleSelect.addEventListener('change', applyModuleMode);

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
            applyModuleMode();
            initQuestionEditors();
        });

        $(document).ready(function () {
            $('.select2').select2({
                width: '100%',
                allowClear: true
            });
            initQuestionEditors();
            applyModuleMode();
        });
    })();
</script>
@endsection
