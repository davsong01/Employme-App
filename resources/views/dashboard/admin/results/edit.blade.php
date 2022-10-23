@extends('dashboard.admin.index')
@section('title', $details['user_name'] )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($details['allow_editing'] != 0)
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 style="color:green">Update Scores for: {{ $details['user_name'] }}</h4>
                    </div>
                    <form action="{{route('results.update', $result_id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <h6 style="color:red">Training details</s></h6>
                                <!--Gives the first error for input name-->
                                <div class="form-group">
                                    <label>Training</label>
                                    <input type="text" name="" value="{{ $program->p_name }}"
                                        class=" form-control" disabled>
                                </div>
                               
                                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                                <div class="form-group">
                                    <label>Pass Mark Set</label>
                                    <input type="number" name="passmark"
                                        value="{{ old('passmark') ?? $program->scoresettings->passmark }}" class="form-control" min="0"
                                        max="100" required disabled>
                                </div>
                                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                            </div>

                           
                            <div class="col-md-6">
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')
                               <h6 style="color:red">Add Email score here</h6>
                                <div class="form-group">
                                    <label>Email Score* <span style="color:green">(Max score = {{$program->scoresettings->email }})</span></label>
                                    <input type="number" name="emailscore" value="{{ old('emailscore') ?? $details['email_test_score'] }}" class="form-control"
                                        min="0" max="{{$program->scoresettings->email }}">
                                </div>
                               
                                <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>
                                @endif
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')
                                <h6 style="color:red">Add Role play score here</h6>
                                <div class="form-group">
                                    <label>Role Play Score* <span style="color:green">(Max score = {{$program->scoresettings->role_play }})</span></label>
                                    <input type="number" name="roleplayscore" value="{{ old('roleplayscore') ?? $details['role_play_score'] }}"
                                        class="form-control" min="0" max="{{$program->scoresettings->role_play }}" required>
                                </div>
                                <div><small style="color:red">{{ $errors->first('roleplayscore')}}</small></div>
                            </div>
                            @endif
                            
                        </div>
                       
                        <div class="row">
                            <div class="col-md-12">
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')
                                <h6 style="color:red">Certificate Test Submision</h6>
                                <p>Please go through this user's attempt and grade user with the grade box below</p>
                              
                               
                               <div class="form-group">
                                    @foreach($user_results as $results)
                                    
                                    <label for="title"> <strong style="color:green">QUESTION {{ $i ++  }}</strong></label><br>
                                    <strong style="color:green">Module:</strong> {!! $results->module->title.'<br><br>' !!}</span>
                                    
                                      <strong style=:color:green>Question:</strong> {!! $results['title'] .'<br><br>'  !!}</span>
                                     
                                      <strong>Answer:</strong> {!! $results['answer'] .'<br><br>' !!}
                                 
                                    @endforeach
                                    <div class="form-group">
                                    {{-- <textarea style="max-width: 100%; padding:10px; text-align: justify;" name="answer" id="" rows="12" cols="100" readonly>{!! $results['submission'] !!}</textarea> --}}
                                    </div>
                                </div>
                                {{-- @endforeach --}}
                               
                                <h6 style="color:red">Now, score this candidate's certification test (Result with score of 10 will be recorded as 'processing' on cadidate's dashboard): </h6>
                                <div class="form-group">
                                    <label><span style="color:green">(Max score = {{ $program->scoresettings->certification}})</span></label>
                                    <input type="number" name="certification_score" {{ (Auth::user()->role_id == "Facilitator") && Auth::user()->role_id != "Admin" ? "Readonly" : '' }} value="{{ old('certification_score') ?? $details['certification_score'] }}" class="form-control"
                                        min="0" max="{{ $program->scoresettings->certification }}">
                                </div>
                                @else
                                <input type="hidden" value="{{ $details['certification_score'] }}" name="certification_score">
                                @endif
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')
                                <div class="form-group">
                                    <label>Grader Comment(Optional) </label>
                                    <textarea name="grader_comment" class="form-control" id="" cols="30" rows="10" value="{{ old('grader_comment') ?? $details['grader_comment'] }}">{{ old('grader_comment') ?? $details['grader_comment'] }}</textarea>
                                   
                                </div>
                                @endif
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')
                                <div class="form-group">
                                    <label>Facilitator Comment(Optional) </label>
                                    <textarea name="facilitator_comment" class="form-control" id="" cols="30" rows="10" value="{{ old('facilitator_comment') ?? $details['facilitator_comment'] }}">{{ old('facilitator_comment') ?? $details['facilitator_comment'] }}</textarea>
                                   
                                </div>
                                @endif
                            </div>
                        </div>
                       
                        <div class="row">
                            
                            <button type="submit" class="btn btn-primary"
                                style="width:100%">Submit</button>
                        </div> 
                    </form>  
                    @else
                    <h2>Expecting user to re-take certification tests hence scores cannot be modified </h2>
                    @endif             
            </div>
        </div>
    </div>
    @endsection