@extends('dashboard.admin.index')
@section('title', 'Retake test')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Allow User Retake Test</h4>
                    </div>
                    <form action="{{route('saveredotest')}}" method="POST" class="pb-2">
                        <div class="row">

                            <div class="col-md-12">

                                <div class="mb-3{{ $errors->has('program') ? ' is-invalid' : '' }}">

                                    <label for="program" class="form-label">Choose program you want to enable its tests for this participant</label>
                                    <select name="program" id="program" class="form-select" required>

                                        <option value="">-- Select Option --</option>
                                        @foreach( $programs as $program )
                                        <option value="{{ $program->program_id }}" {{ old('program') == $program->program_id ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                        @endforeach

                                    </select>
                                    @if ($errors->has('program'))
                                    <div class="text-danger small mt-1">{{ $errors->first('program') }}</div>
                                    @endif

                                </div>
                               
                                <input type="hidden" name="user_id" value="{{ $id }}">


                            </div>

                        </div>

                        <div class="row">

                            <button type="submit" class="btn btn-primary" style="width:100%">

                                Submit

                            </button>

                        </div>

                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
    @endsection
