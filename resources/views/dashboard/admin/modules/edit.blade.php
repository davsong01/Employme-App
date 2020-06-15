@extends('dashboard.admin.index')
@section('title', 'View/Update Module')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                       @include('layouts.partials.alerts')
                    </div>
                    <form action="{{route('modules.update', $module->id)}}" method="POST" class="pb-2">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Module Details</h4>
                                </div>
                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title</label>
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? $module->title }}" autofocus required>
                                    @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                        
                                <div class="form-group">
                                    <label for="class">Select Training *</label>
                                    <select name="program_id" id="program" class="form-control" required>
                                            <option value=""></option>        
                                            @foreach ($programs as $program)     
                                            <option value="{{ $program->id }}" {{ $program->id == $module->program_id ? 'selected' : ''}}>
                                                {{$program->p_name}}</option>  
                                            @endforeach 
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('program')}}</small></div>
                                </div>
                        
                                <div class="form-group">
                                    <label for="class">Type</label>
                                    @if( $module->questions->count() <= 0 )
                                        <select name="type" id="class" class="form-control" required>
                                        <option value="0" {{ $module->type == 'Class Test' ? 'selected' : ''}}>Class Test</option>
                                            <option value="1" {{ $module->type == 'Certification Test' ? 'selected' : ''}}>Certification Test</option>
                                        </select>
                                    @else
                                        <select name="type" id="class" class="form-control" required readonly>
                                            <option value="{{ $module->type == "Class Test" ?? 0}} {{ $module->type == 'Certification Test' ?? 1}}">{{ $module->type }}</option>
                                        </select>
                                    @endif
                                    <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="class" class="form-control">
                                    <option value="0" {{ $module->status == 0 ? 'selected' : ''}}>Disabled</option>
                                        <option value="1" {{ $module->status == 1 ? 'selected' : ''}}>Enabled</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('status')}}</small></div>
                                </div>
                        
                                <div class="form-group{{ $errors->has('noofquestions') ? ' has-error' : '' }}">
                                    <label for="noofquestions">No of Questions(Min: 5; Max: 20)</label>
                                    <input id="noofquestions" type="number" class="form-control" name="noofquestions" value="{{ old('noofquestions') ?? $module->noofquestions }}" autofocus required min="5" max="20">
                                    @if ($errors->has('noofquestions'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('noofquestions') }}</strong>
                                    </span>
                                    @endif
                                </div>
                        
                                <div class="form-group{{ $errors->has('time') ? ' has-error' : '' }}">
                                    <label for="time">How many minutes for Module Questions(Min: 2minutes)</label>
                                    <input id="time" type="number" class="form-control" name="time" value="{{ old('time') ?? $module->time}}" autofocus required min="2">
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
                      

                </div>
            </div>
        </div>
    </div>
    @endsection