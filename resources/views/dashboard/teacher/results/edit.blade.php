@extends('dashboard.admin.index')
@section('title', $user_results->user->name )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 style="color:green">Update Scores for: {{$user_results->user->name}}</h4>
                    </div>
                    <form action="{{route('results.update', $user_results->id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color:red">Training details</s></h6>
                                <!--Gives the first error for input name-->
                                <div class="form-group">
                                    <label>Training</label>
                                    <input type="text" name="" value="{{ $user_results->program->p_name }}"
                                        class=" form-control" disabled>
                                </div>
                               
                                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                                <div class="form-group">
                                    <label>Pass Mark Set</label>
                                    <input type="number" name="passmark"
                                        value="{{ old('passmark') ?? $user_results->program->scoresettings->passmark }}" class="form-control" min="0"
                                        max="100" required disabled>
                                </div>
                                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                            </div>

                            <div class="col-md-6">
                               <h6 style="color:red">Add Email and Role play scores here</h6>
                                <div class="form-group">
                                    <label>Email Score* <span style="color:green">(Max score = {{$user_results->program->scoresettings->email }})</span></label>
                                    <input type="number" name="emailscore" {{ (Auth::user()->role_id == "Facilitator") && Auth::user()->role_id != "Admin" ? "Readonly" : '' }} value="{{ old('emailscore') ?? $user_results->email_test_score }}" class="form-control"
                                        min="0" max="{{$user_results->program->scoresettings->email }}">
                                </div>
                                <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>
                                <div class="form-group">
                                    <label>Role Play Score* <span style="color:green">(Max score = {{$user_results->program->scoresettings->role_play }})</span></label>
                                    <input type="number" {{ (Auth::user()->role_id == "Grader") && Auth::user()->role_id != "Admin" ? "Readonly" : '' }} name="roleplayscore" value="{{ old('roleplayscore') ?? $user_results->role_play_score }}"
                                        class="form-control" min="0" max="{{$user_results->program->scoresettings->role_play }}" required>
                                </div>
                                <div><small style="color:red">{{ $errors->first('roleplayscore')}}</small></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 style="color:red">Certificate Test Submision</h6>
                                <p>Please go through this user's attempt and grade user with the grade box below</p>
                                @foreach ($array as $key => $value) 
                                <div class="form-group">
                                    <label for="title">{{ $i ++ .'. ' }}{{$key}}</label><br>
            
                                    <div class="form-group">
                                    <textarea style="max-width: 100%; padding:10px; text-align: justify;" name="answer" id="" rows="12" cols="100" readonly>{{ $value }}</textarea>
                                    </div>
                                </div>
                                @endforeach
                                <h6 style="color:red">Now, score this candidate's certification test: </h6>
                                <div class="form-group">
                                    <label><span style="color:green">(Max score = {{$user_results->program->scoresettings->certification}})</span></label>
                                    <input type="number" name="certification_score" {{ (Auth::user()->role_id == "Facilitator") && Auth::user()->role_id != "Admin" ? "Readonly" : '' }} value="{{ old('certification_score') ?? $user_results->certification_test_score }}" class="form-control"
                                        min="0" max="{{$user_results->program->scoresettings->certification}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <button type="submit" class="btn btn-primary"
                                style="width:100%">Submit</button>
                        </div>
                        
                </div>
            </div>
        </div>
    </div>
    @endsection