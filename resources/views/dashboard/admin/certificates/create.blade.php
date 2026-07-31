@extends('dashboard.admin.index')
@section('title', 'Add Certificate')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Certificate</h4>
                    </div>
                    <form id="certificate-program-form" action="{{ url('admin/suser/0') }}" method="GET" class="pb-2">
                        <!--Gives the first error for input name-->

                        <div><small>{{ $errors->first('title')}}</small></div>
                        <div class="mb-3">

                            <label for="class">Select Training *</label>

                            <select name="program_id" id="program_id" class="form-control" required>

                                <option value="">-- Select Training --</option>

                                @foreach ($programs as $program)
                                @if($program->users_count)
                                <option value="{{ $program->id }}">{{$program->p_name}}</option>
                                @endif
                                @endforeach
                            </select>
                            <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
                        </div>

                        <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

                       
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('certificate-program-form');

            if (!form) {
                return;
            }

            form.addEventListener('submit', function (event) {
                const programId = document.getElementById('program_id')?.value;

                if (!programId) {
                    return;
                }

                event.preventDefault();
                form.action = '{{ url('admin/suser') }}/' + programId;
                form.method = 'GET';
                form.submit();
            });
        });
    </script>
@endsection
