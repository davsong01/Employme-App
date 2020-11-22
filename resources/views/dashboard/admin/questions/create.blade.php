@extends('dashboard.admin.index')
@section('title', 'Add New question')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{route('questions.store')}}" method="POST" class="pb-2">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Question Details</h4>
                                </div>
                                <div class="form-group">
                                    <label for="class">Associated Module</label>
                                    <select name="module" id="module" class="form-control" required>
                                        <option value="">Select Option</option>
                                        @foreach ($modules as $module)
                                        @if($module->questions_count < $module->noofquestions)
                                        <option value="{{ $module->id }}">{{$module->title}} ({{ $module->noofquestions - $module->questions_count .' question(s) left to complete' }})</option>
                                      
                                        @endif
                                       
                                        @endforeach
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('program')}}</small></div>
                                </div>

                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title</label>
                                    <textarea id="title" type="text" class="form-control" name="title"
                                        value="{{ old('title') }}" autofocus required>{{ old('title') }}</textarea>
                                    @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <h5>Ignore the sections below if question is an open ended question</h5>
                                <div class="form-group{{ $errors->has('optionA') ? ' has-error' : '' }}">
                                    <label for="optionA">Option A</label>
                                    <input id="optionA" type="text" class="form-control" name="optionA"
                                        value="{{ old('optionA') }}" autofocus>
                                    @if ($errors->has('optionA'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionA') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('optionB') ? ' has-error' : '' }}">
                                    <label for="optionB">Option B</label>
                                    <input id="optionB" type="text" class="form-control" name="optionB"
                                        value="{{ old('optionB') }}" autofocus>
                                    @if ($errors->has('optionB'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionB') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('optionC') ? ' has-error' : '' }}">
                                    <label for="optionC">Option C</label>
                                    <input id="optionC" type="text" class="form-control" name="optionC"
                                        value="{{ old('optionC') }}" autofocus>
                                    @if ($errors->has('optionC'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionC') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('optionD') ? ' has-error' : '' }}">
                                    <label for="optionD">Option D</label>
                                    <input id="optionD" type="text" class="form-control" name="optionD"
                                        value="{{ old('optionD') }}" autofocus>
                                    @if ($errors->has('optionD'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionD') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="class" style="color:red">Which option above is the correct one?</label>
                                    <select name="correct" id="correct" class="form-control">
                                        <option value="">Slect Option</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('correct')}}</small></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection