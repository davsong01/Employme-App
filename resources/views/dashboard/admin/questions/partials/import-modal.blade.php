@php
    $modalId = $modalId ?? 'importQuestionsModal';
    $p_id = $p_id ?? null;
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="{{ $modalId }}Label">Import Questions</h5>
                    <p class="text-muted small mb-0">Upload a spreadsheet to add questions to this training without leaving the page.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('layouts.partials.alerts')

                <form action="{{ route('questions.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="p_id" value="{{ $p_id }}">

                    <div class="mb-3">
                        <label for="{{ $modalId }}_file" class="form-label">Upload File</label>
                        <input type="file" id="{{ $modalId }}_file" name="file" class="form-control" accept=".csv,.xls,.xlsx" required>
                        @error('file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Questions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($errors->has('file'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById(@json($modalId));
            if (modalElement && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    </script>
@endif
