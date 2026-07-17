@extends('dashboard.admin.index')
@section('title', 'Add New Module')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{route('modules.store')}}" method="POST" class="pb-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Module Details</h4>
                                </div>
                                <div class="mb-3{{ $errors->has('title') ? ' is-invalid' : '' }}">
                                    <label for="title" class="form-label">Title</label>
                                    <input id="title" type="text" class="form-control" name="title"
                                        value="{{ old('title') }}" autofocus required>
                                    @if ($errors->has('title'))
                                    <div class="text-danger small mt-1">{{ $errors->first('title') }}</div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="program_display" class="form-label">Training</label>
                                    <input type="text" placeholder="{{ $program->p_name }}" disabled id="program_display" value="{{ $program->p_name }}" class="form-control">

                                    <input type="hidden" name="program" value="{{ $program->id }}" required>

                                    <div class="text-danger small mt-1">{{ $errors->first('program')}}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="" selected>-- Select Option --</option>
                                        <option value="0">Class Test</option>
                                        <option value="1">Certification Test</option>
                                    </select>
                                    <div class="text-danger small mt-1">{{ $errors->first('type')}}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="0" selected>Disabled</option>
                                    </select>
                                    <div class="text-danger small mt-1">{{ $errors->first('status')}}</div>
                                </div>

                                <div class="mb-3{{ $errors->has('noofquestions') ? ' is-invalid' : '' }}">
                                    <label for="noofquestions" class="form-label">No of Questions <small class="cwarning text-danger"><b>(You can only add 1 question for a certification test)</b></small></label>
                                    <input id="noofquestions" type="number" class="form-control" name="noofquestions"
                                        value="{{ old('noofquestions') }}" min="0" autofocus required>
                                    @if ($errors->has('noofquestions'))
                                    <div class="text-danger small mt-1">{{ $errors->first('noofquestions') }}</div>
                                    @endif
                                </div>

                                <div class="mb-3{{ $errors->has('time') ? ' is-invalid' : '' }}">
                                    <label for="time" class="form-label">How many minutes for module questions (0 means no time limit)</label>
                                    <input id="time" type="number" class="form-control" name="time"
                                        value="{{ old('time') }}" autofocus min="0">
                                    @if ($errors->has('time'))
                                    <div class="text-danger small mt-1">{{ $errors->first('time') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="allow_test_retake" class="form-label">Allow Test retake</label>
                                    <select name="allow_test_retake" class="form-select" id="allow_test_retake" required>
                                        <option value="1" {{ old('allow_test_retake') == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('allow_test_retake') == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
        
    </div>
   
    @endsection
