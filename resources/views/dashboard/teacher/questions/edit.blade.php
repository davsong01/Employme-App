
@extends('dashboard.admin.index')
@section('title', 'Edit Question')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{route('questions.update', $question->id)}}" method="POST" class="pb-2">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Question Details</h4>
                                </div>
                                <div class="form-group">
                                    <label for="class">Associated Module</label>
                                    <select name="module_id" id="module" class="form-control" readonly required>
                                        <option value="{{ $question->module->id }}">{{ $question->module->title }}</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('module')}}</small></div>
                                </div>

                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title</label>
                                    <input id="title" type="text" class="form-control" name="title"
                                        value="{{ old('title') ?? $question->title}}" autofocus required>
                                    @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                                @if($question->module->type == "Class Test")
                                <div class="form-group{{ $errors->has('optionA') ? ' has-error' : '' }}">
                                    <label for="optionA">Option A</label>
                                    <input id="optionA" type="text" class="form-control" name="optionA"
                                        value="{{ old('optionA') ?? $question->optionA }}" autofocus>
                                    @if ($errors->has('optionA'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionA') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('optionB') ? ' has-error' : '' }}">
                                    <label for="optionB">Option B</label>
                                    <input id="optionB" type="text" class="form-control" name="optionB"
                                        value="{{ old('optionB') ?? $question->optionB}}" autofocus>
                                    @if ($errors->has('optionB'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionB') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('optionC') ? ' has-error' : '' }}">
                                    <label for="optionC">Option C</label>
                                    <input id="optionC" type="text" class="form-control" name="optionC"
                                        value="{{ old('optionC') ?? $question->optionC }}" autofocus >
                                    @if ($errors->has('optionC'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionC') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('optionD') ? ' has-error' : '' }}">
                                    <label for="optionD">Option D</label>
                                    <input id="optionD" type="text" class="form-control" name="optionD"
                                        value="{{ old('optionD') ?? $question->optionD }}" autofocus>
                                    @if ($errors->has('optionD'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('optionD') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="class" style="color:red">Which option above is the correct one?</label>
                                    <select name="correct" id="correct" class="form-control">
                                        <option value="">Select Option</option>
                                        <option value="A" {{ $question->correct == 'A' ? 'selected' : ''}} >A</option>
                                        <option value="B" {{ $question->correct == 'B' ? 'selected' : ''}} >B</option>
                                        <option value="C" {{ $question->correct == 'C' ? 'selected' : '' }} >C</option>
                                        <option value="D" {{ $question->correct == 'D' ? 'selected' : '' }}>D</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('correct')}}</small></div>
                                </div>

                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                        {{ csrf_field() }}
                         {{ method_field('PATCH') }}
                </div>
            </div>
        </div>
    </div>
    @endsection
