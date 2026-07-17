@php
    $row = $row ?? [];
    $index = (string) $index;
    $showObjectiveFields = $showObjectiveFields ?? true;
    $questionId = data_get($row, 'id');
    $optionValues = [
        'A' => data_get($row, 'optionA'),
        'B' => data_get($row, 'optionB'),
        'C' => data_get($row, 'optionC'),
        'D' => data_get($row, 'optionD'),
    ];
    $correctValue = data_get($row, 'correct');
    $titleValue = data_get($row, 'title');
@endphp
<div class="card border-0 shadow-sm question-card" data-question-card data-index="{{ $index }}">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
                <span class="badge bg-light text-dark rounded-pill px-3 py-2">Question {{ is_numeric($index) ? $index + 1 : '' }}</span>
            </div>
            @if($allowRemove ?? false)
                <button type="button" class="btn btn-outline-danger btn-sm" data-remove-question>
                    <i class="fa fa-trash me-1"></i> Remove
                </button>
            @endif
        </div>

        <div class="row g-3">
            <div class="col-12">
                <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $questionId }}">
            </div>
            <div class="col-12 {{ $showObjectiveFields ? 'col-md-5' : '' }}">
                <textarea name="questions[{{ $index }}][title]" id="title_{{ $index }}" data-id-base="title" data-question-title-editor="1" class="form-control" rows="4" placeholder="Enter the question text..." required>{!! $titleValue !!}</textarea>
            </div>

            <div class="col-12 col-md-7 {{ $showObjectiveFields ? '' : 'd-none' }}" data-objective-group>
                <div class="option-grid">
                    @foreach(['A', 'B', 'C', 'D'] as $letter)
                        @php $radioId = 'correct_' . $index . '_' . $letter; @endphp
                        <div class="d-flex align-items-center gap-2 mb-2 flex-nowrap">
                            <div class="flex-shrink-0">
                                <input
                                    type="radio"
                                    class="btn-check"
                                    name="questions[{{ $index }}][correct]"
                                    value="{{ $letter }}"
                                    id="{{ $radioId }}"
                                    title="Mark as correct"
                                    aria-label="Mark option {{ $letter }} as correct"
                                    {{ $correctValue === $letter ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary btn-sm rounded-pill px-2 py-1 question-correct-pill" for="{{ $radioId }}">{{ $letter }}</label>
                            </div>
                            <div class="flex-grow-1">
                                <input type="text" class="form-control" name="questions[{{ $index }}][option{{ $letter }}]" id="option{{ $letter }}_{{ $index }}" data-id-base="option{{ $letter }}" value="{{ $optionValues[$letter] ?? '' }}" placeholder="Enter option text">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
