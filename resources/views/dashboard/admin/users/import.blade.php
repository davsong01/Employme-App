@extends('dashboard.admin.index')
@section('title', 'Add New question')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Import Participants for {{ $program->p_name }}</h5>
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{ route('users.import.new') }}" method="POST" name="importform" class="pb-4" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="file" class="font-weight-bold">Upload File</label>
                                    <small class="text-muted d-block mb-2">
                                        Please upload your file containing the user data. Supported formats: .csv, .xls, .xlsx
                                    </small>
                                    <input type="file" name="file" class="form-control" accept=".csv, .xlsv, .xls, .xlsx">
                                    <div class="mt-2">
                                        <a href="{{ route('user-bulk-sample', 'bulk_users.xlsx') }}" class="text-primary small">
                                            <i class="fa fa-download"></i> Download sample file
                                        </a>
                                    </div>
                                    @if ($errors->has('file'))
                                        <div class="alert alert-danger mt-2 p-1">
                                            <strong>{{ $errors->first('file') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="import_from" class="font-weight-bold">Select Program</label>
                                    <small class="text-muted d-block mb-2">
                                        All participants for the selected program will be imported.
                                    </small>
                                    <select name="import_from" id="import_from" class="form-control">
                                        <option value="">-- Select Program --</option>
                                        @foreach ($programs as $training)
                                            @if($training->id != $program->id)
                                                <option value="{{ $training->id }}" {{ old('import_from') == $training->id ? 'selected' : '' }}>
                                                    {{ $training->p_name }} | <strong>({{ \App\Models\Settings::value('DEFAULT_CURRENCY').number_format($training->p_amount) }}) - {{ $training->fully_paid_count }} Participants</strong>
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date" class="font-weight-bold">Start Date</label>
                                    <small class="text-muted d-block mb-2"> <br>
                                    </small>
                                    <input type="date" id="start_date" class="form-control" name="start_date">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" value="{{ $program->id }}" name="p_id">

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">
                                    <i class="fa fa-upload"></i> Submit Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection