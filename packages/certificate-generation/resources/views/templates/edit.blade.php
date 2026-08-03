@extends(config('certificates.layout', 'layouts.app'))

@php
    $isEdit = isset($template) && $template->exists;
@endphp

@section('title', $isEdit ? 'Edit Certificate Designer' : 'Create Certificate Designer')

@section(config('certificates.content_section', 'content'))
@php
    $programIds = old('program_ids');
    $programStatuses = old('program_statuses', []);

    if (! is_array($programIds) || $programIds === []) {
        if ($isEdit && is_array($selectedProgramRows ?? []) && $selectedProgramRows !== []) {
            $programIds = collect($selectedProgramRows)->pluck('program_id')->filter()->values()->all();
            $programStatuses = collect($selectedProgramRows)->pluck('status')->values()->all();
        } else {
            $programIds = $selectedProgramIds ?? [];
        }
    }

    $programIds = is_array($programIds) ? $programIds : [];
    $programStatuses = is_array($programStatuses) ? $programStatuses : [];

    $programRows = [];

    foreach ($programIds as $index => $programId) {
        $programRows[] = [
            'program_id' => $programId,
            'status' => $programStatuses[$index] ?? 'draft',
        ];
    }

    if ($programRows === []) {
        $programRows[] = [
            'program_id' => '',
            'status' => 'draft',
        ];
    }

    $actionUrl = $isEdit
        ? route(config('certificates.routes.name', 'certificates.').'manage.templates.update', $template)
        : route(config('certificates.routes.name', 'certificates.').'manage.templates.store');
@endphp

<style>
    .certificate-templates-page .badge {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: auto !important;
        height: auto !important;
        min-width: 0;
        padding: .35rem .6rem;
        border-radius: .35rem !important;
        box-shadow: none;
        font-size: .75rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }
</style>

<div class="container-fluid py-3 certificate-templates-page">
    <form method="POST" action="{{ $actionUrl }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        
        <div class="card mb-3">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Program assignments</h5>
                        <small class="text-muted">Add one row per program, with its own status.</small>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-program-row">
                        Add program
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 48%">Program</th>
                                <th style="width: 22%">Status</th>
                                <th style="width: 12%"></th>
                            </tr>
                        </thead>
                        <tbody id="program-rows">
                            @foreach($programRows as $row)
                                <tr class="program-row">
                                    <td>
                                        <select name="program_ids[]" class="select2 form-control program-select" data-placeholder="Select a program">
                                            <option value="">Select a program</option>
                                            @foreach($programs as $program)
                                                <option value="{{ $program->id }}" @selected((string) $program->id === (string) ($row['program_id'] ?? ''))>
                                                    {{ $program->p_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="program_statuses[]" class="form-control program-status">
                                            <option value="published" @selected(($row['status'] ?? 'draft') === 'published')>Published</option>
                                            <option value="draft" @selected(($row['status'] ?? 'draft') === 'draft')>Draft</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-program-row">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('certificates::templates._designer', ['template' => $isEdit ? $template : null])
    </form>
</div>
@endsection

@section('extra-scripts')
<script>
    $(function () {
        const $rows = $('#program-rows');
        const programOptions = @json(
            collect($programs)->map(fn ($program) => [
                'id' => $program->id,
                'text' => $program->p_name,
            ])->values()
        );

        function initSelect2($scope) {
            $scope.find('.select2').select2({
                width: '100%',
                placeholder: 'Select a program'
            });
        }

        // REMOVED: toggleProgramRowRequirements() setting native required on hidden selects/inputs
        // Let Javascript submission handler manage validation entirely.

        function buildProgramOptions(selectedId = '', excludedIds = []) {
            const selected = String(selectedId || '');
            const excluded = excludedIds.map((id) => String(id));
            let html = '<option value="">Select a program</option>';

            programOptions.forEach(function (program) {
                const programId = String(program.id);
                const isSelected = programId === selected;
                if (! isSelected && excluded.includes(programId)) {
                    return;
                }

                const optionSelected = isSelected ? ' selected' : '';
                html += '<option value="' + program.id + '"' + optionSelected + '>' + $('<div>').text(program.text).html() + '</option>';
            });

            return html;
        }

        function refreshProgramRows() {
            $rows.find('.program-row').each(function () {
                const $row = $(this);
                const $select = $row.find('.program-select');
                const currentValue = $select.val();
                const excludedIds = $rows.find('.program-row').not($row).find('.program-select').map(function () {
                    return $(this).val();
                }).get().filter(Boolean);

                if ($select.data('select2')) {
                    $select.select2('destroy');
                }

                $select.html(buildProgramOptions(currentValue, excludedIds));
                $select.val(currentValue);
                initSelect2($row);
            });
        }

        function addRow(data = {}) {
            const rowHtml = `
                <tr class="program-row">
                    <td>
                        <select name="program_ids[]" class="select2 form-control program-select" data-placeholder="Select a program">
                            ${buildProgramOptions(data.program_id ?? '')}
                        </select>
                    </td>
                    <td>
                        <select name="program_statuses[]" class="form-control program-status">
                            <option value="draft"${String(data.status ?? 'draft') === 'draft' ? ' selected' : ''}>Draft</option>
                            <option value="published"${String(data.status ?? 'draft') === 'published' ? ' selected' : ''}>Published</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-program-row">Remove</button>
                    </td>
                </tr>
            `;

            const $row = $(rowHtml);
            $rows.append($row);
            refreshProgramRows();
        }

        $('form').on('submit', function (e) {
            let isValid = true;

            $rows.find('.program-row').each(function () {
                const $row = $(this);
                const $select = $row.find('.program-select');
                const $status = $row.find('.program-status');

                const programVal = $select.val();
                const statusVal = $status.val();

                // If user filled out status, but didn't pick a program
                const hasExtraFields = (statusVal && statusVal !== 'draft');
                
                if (!programVal && hasExtraFields) {
                    isValid = false;
                    $row.find('.select2-selection').addClass('is-invalid');
                } else {
                    $row.find('.select2-selection').removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopImmediatePropagation();
                alert('Please select a program for all active rows or clear unnecessary fields.');
                return false;
            }
        });
        

        $rows.on('change', '.program-select', function () {
            $(this).closest('.program-row').find('.select2-selection').removeClass('is-invalid');
            refreshProgramRows();
        });

        initSelect2($(document));
        refreshProgramRows();

        $('#add-program-row').on('click', function () {
            addRow();
        });

        $rows.on('click', '.remove-program-row', function () {
            const $row = $(this).closest('.program-row');

            if ($rows.find('.program-row').length === 1) {
                $row.find('select, input').val('');
                $row.find('.program-status').val('draft');
                $row.find('.program-select').val('').trigger('change');
                refreshProgramRows();
                return;
            }

            const $select = $row.find('.program-select');
            if ($select.data('select2')) {
                $select.select2('destroy');
            }

            $row.remove();
            refreshProgramRows();
        });
    });
</script>
@endsection
