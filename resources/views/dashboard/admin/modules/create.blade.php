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
                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title</label>
                                    <input id="title" type="text" class="form-control" name="title"
                                        value="{{ old('title') }}" autofocus required>
                                    @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="class">Training</label>
                                    <input type="text" placeholder= "{{ $program->p_name }}"  disabled  id="program" value="{{ $program->p_name }}" class="form-control" >

                                    <input type="hidden" id="program" name="program" value="{{ $program->id }}" class="form-control" required>

                                    <div><small style="color:red">{{ $errors->first('program')}}</small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                         <option value="" selected>-- Select Option --</option>
                                        <option value="0">Class Test</option>
                                        <option value="1">Certification Test</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="0" selected>Disabled</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('status')}}</small></div>
                                </div>

                                <div class="form-group{{ $errors->has('noofquestions') ? ' has-error' : '' }}">
                                    <label for="noofquestions">No of Questions<small class = "cwarning" style="color:red"> <b>(You can only add 1 question for a certification text)</b> </small> </label>
                                    <input id="noofquestions" type="number" class="form-control" name="noofquestions"
                                        value="{{ old('noofquestions') }}" min="0" utofocus required>
                                    @if ($errors->has('noofquestions'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('noofquestions') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('time') ? ' has-error' : '' }}">
                                    <label for="time">How many minutes for Module Questions(Min: 2minutes)</label>
                                    <input id="time" type="number" class="form-control" name="time"
                                        value="{{ old('time') }}" autofocus required min="2">
                                    @if ($errors->has('time'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('time') }}</strong>
                                    </span>
                                    @endif
                                </div>

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