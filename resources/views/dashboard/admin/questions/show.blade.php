@extends('dashboard.admin.index')
@section('title', config('app.name') . ' Questions')
@section('css')
<style>
    .question-bank-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
    }

    .question-bank-card .table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .06em;
        color: #475569;
    }
</style>
@endsection
@section('content')
@php
    $module = $questions->first()?->module;
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero question-bank-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Question Bank</span>
                            <h1 class="h3 fw-bold mb-2">{{ strtoupper($p_name) }}</h1>
                            <p class="text-muted mb-0">Questions now live in the module editor for this training. Use the table below to review existing entries.</p>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm question-bank-card">
        <div class="card-body">
            <form id="bulk-action-form" method="POST" action="{{ route('questions.bulkDelete') }}">
                @csrf
                <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-3">
                    <div class="text-muted small">Bulk actions apply to the selected rows below.</div>
                    <div class="d-flex gap-2">
                        <select required name="action" id="bulk-action-select" class="form-control">
                            <option value="">Select Action</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-danger">Apply</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>#</th>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Associated Module</th>
                                <th>Correct Option</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                                <tr>
                                    <td data-label="">
                                        <input type="checkbox" name="selected_questions[]" value="{{ $question->id }}">
                                    </td>
                                    <td data-label="#">{{ $i++ }}</td>
                                    <td data-label="Date">{{ $question->created_at->format('d/m/Y') }}</td>
                                    <td data-label="Title">{!! $question->title !!}</td>
                                    <td data-label="Associated Module">{{ $question->module->title }}</td>
                                    <td data-label="Correct Option">
                                        @php
                                            $correctField = 'option' . $question->correct;
                                            $correctText = $question->$correctField ?? '';
                                        @endphp
                                        <span class="badge bg-success">{{ $question->correct }}</span>
                                        @if($correctText)
                                            <div class="text-muted small mt-1">{{ $correctText }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end" data-label="Actions">
                                        <a class="btn btn-outline-primary btn-sm" href="{{ route('questions.edit', $question->id) }}" data-bs-toggle="tooltip" title="Edit question">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all').addEventListener('click', function(e) {
        let checkboxes = document.querySelectorAll('input[name="selected_questions[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });

    document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
        let action = document.getElementById('bulk-action-select').value;
        if (action === 'delete' && !confirm('Are you sure you want to delete selected questions?')) {
            e.preventDefault();
        }
    });
</script>
@endsection
