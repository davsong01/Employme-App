@extends('dashboard.admin.index')
@section('title')
{{ config('app.name') }} Questions
@endsection
@section('content')

<div class="container-fluid">
     <div class="card">
        <div class="card-body">
            <div class="card-header" style="color:#008000; text-align:center; padding:20px">
                <h2>{{ strtoupper($p_name) }}</h2> 
                <h3 style="text-align:center; padding:20px">QUESTION MANAGEMENT</h3><br>
                <a href="{{ route('questions.add', ['p_id'=> $p_id]) }}">
                    <button type="button" class="btn btn-outline-primary">Add New Question</button>
                </a>
                <a href="{{ route('questions.import', ['p_id' => $p_id]) }}" class="btn btn-success">
                    <i class="fa fa-upload"></i> Import Questions
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')

            {{-- Bulk actions --}}
            {{-- Bulk actions form only wraps checkboxes --}}
            <form id="bulk-action-form" method="POST" action="{{ route('questions.bulkDelete') }}">
                @csrf
                <div style="margin-bottom:15px;">
                    <select required name="action" id="bulk-action-select" class="form-control" style="width:200px; display:inline-block;">
                        <option value="">Select Action</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="submit" class="btn btn-danger">Apply</button>
                </div>

                <table id="zero_config" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>#</th>
                            <th>Date</th>
                            <th>Title</th>                            
                            <th>Associated Module</th>
                            <th>Correct Option</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_questions[]" value="{{ $question->id }}">
                            </td>
                            <td>{{ $i++ }}</td>
                            <td>{{ $question->created_at->format('d/m/Y') }}</td>
                            <td>{!! $question->title !!}</td>
                            <td>{{ $question->module->title }}</td>
                            <td>{{ $question->correct }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-info" href="{{ route('questions.edit', $question->id) }}" 
                                    data-toggle="tooltip" title="Edit question">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr> 
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

{{-- Select all checkbox --}}
<script>
    document.getElementById('select-all').addEventListener('click', function(e) {
        let checkboxes = document.querySelectorAll('input[name="selected_questions[]"]');
        checkboxes.forEach(cb => cb.checked = e.target.checked);
    });

    // Optional: confirm bulk delete
    document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
        let action = document.getElementById('bulk-action-select').value;
        if(action === 'delete') {
            if(!confirm('Are you sure you want to delete selected questions?')) {
                e.preventDefault();
            }
        }
    });
</script>

@endsection
