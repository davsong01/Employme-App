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
                                    <div class="d-flex align-items-center mb-2">
                                        <input type="checkbox" id="show_archived" class="mr-2">
                                        <label for="show_archived" class="mb-0">Show Archived Programs</label>
                                    </div>

                                    <label for="import_from" class="font-weight-bold">Select Program</label>
                                    <small class="text-muted d-block mb-2">
                                        All participants for the selected program will be imported.
                                    </small>
                                    <select name="import_from" id="import_from" class="form-control select2">
                                        <option value="">-- Select Program --</option>
                                        @foreach ($programs as $training)
                                            @if($training->id != $program->id)
                                                <option value="{{ $training->id }}"
                                                        data-archived="{{ $training->is_archived }}"
                                                        {{ old('import_from') == $training->id ? 'selected' : '' }}>
                                                    {{ $training->p_name }} | 
                                                    <strong>({{ currency().number_format($training->p_amount) }}) 
                                                    - {{ $training->fully_paid_count }} Participants</strong>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <small class="text-muted d-block mb-2">
                                        <label for="amount_to_user" class="font-weight-bold">Amount to use (Optional)</label> <br>
                                        Amount: {{ currency()}}{{number_format($program->p_amount)}} @if($program->early_bird_status) | Early Bird: {{ number_format($program->e_amount) }} @endif
                                    </small>
                                    <input type="number" id="amount_to_use" class="form-control" name="amount_to_use">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remarks" class="font-weight-bold">Admin Remarks (Optional)</label>
                                    <textarea id="remarks" class="form-control" name="remarks" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        @if($source == 'group')
                            <input type="hidden" value="1" name="is_package">
                        @endif
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
    @section('extra-scripts')
    <script>
        $(document).ready(function () {
            function toggleArchived() {
                if ($('#show_archived').is(':checked')) {
                    // Show archived options
                    $('#import_from option[data-archived="1"]').show();
                } else {
                    // Hide archived options
                    $('#import_from option[data-archived="1"]').hide();
                    // If a hidden option was selected, reset back to empty
                    if ($('#import_from option:selected').data('archived') === 1) {
                        $('#import_from').val('');
                    }
                }
                $('#import_from').trigger('change.select2'); // Refresh Select2
            }

            // Initial state (hide archived by default)
            toggleArchived();

            // Handle checkbox toggle
            $('#show_archived').on('change', toggleArchived);
        });

    </script>
    @endsection