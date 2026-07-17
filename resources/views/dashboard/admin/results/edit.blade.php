@php
    $check = [
      'update-certification-score',
      'results.add',
      'update-roleplay-score',
      'update-email-score',
      'update-crm-score',
      'update-class-score',
      'results.grader',
      'results.facilitator'
    ];

    $permissions = checkTrainingHasPermissions($program->id, $check);
@endphp
@extends('dashboard.admin.index')
@section('css')
<style>
  /* The Modal (background) */
  .modal {
    display: none;
    position: fixed;
    z-index: 1;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.4);
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  /* The Close Button */
  .btn-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
  }
</style>
@endsection
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
            <div>
              @if(isset($history) && !empty($history))
                <span class="retake">RESITS </span><span style="background: aqua;padding: 5px;border-radius: 50px;"
                class="thread-count">{{ $history->count() }}</span>
                @if($history->count() > 0)
                <a style="border-radius: 6px;" class="btn btn-info btn-sm" href="#resit" id="myBtn">
                  View Resit History
                </a>
                @endif
              @endif
            </div>
          </div>

          <form action="{{route('results.update', $results->id)}}" method="POST" enctype="multipart/form-data"
            class="pb-2">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <div class="row">
              <div class="col-md-6">
                <h6 style="color:red">Training details</s></h6>
                <!--Gives the first error for input name-->
                <div class="mb-3">
                  <label>Training</label>
                  <input type="text" name="" value="{{ $program->p_name }}" class=" form-control" disabled>
                </div>

                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                <div class="mb-3">
                  <label>Pass Mark Set</label>
                  <input type="number" name="passmark"
                    value="{{ old('passmark') ?? $program->scoresettings->passmark }}" class="form-control" min="0"
                    max="100" required disabled>
                </div>
                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
              </div>

              <div class="col-md-6">
                @if(!empty($program->scoresettings->email) && $program->scoresettings->email > 0) 
                  @if($permissions['update-email-score'])
                    <h6 style="color:red">Add Email score here</h6>
                    <div class="mb-3">
                      <label>Email Score* <span style="color:green">(Max score =
                          {{$program->scoresettings->email }})</span></label>
                      <input type="number" name="emailscore" value="{{ old('emailscore') ?? $details['email_test_score'] }}"
                        class="form-control" min="0" max="{{$program->scoresettings->email }}">
                    </div>
                    <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>
                  @endif
                @endif
                @if(!empty($program->scoresettings->role_play) && $program->scoresettings->role_play > 0) 
                  @if($permissions['update-roleplay-score'])
                  <h6 style="color:red">Add Role play score here</h6>
                  <div class="mb-3">
                    <label>Role Play Score* <span style="color:green">(Max score =
                        {{$program->scoresettings->role_play }})</span></label>
                    <input type="number" name="roleplayscore"
                      value="{{ old('roleplayscore') ?? $details['role_play_score'] }}" class="form-control" min="0"
                      max="{{$program->scoresettings->role_play }}" required>
                  </div>
                  <div><small style="color:red">{{ $errors->first('roleplayscore')}}</small></div>
                  @endif
                @if(!empty($program->scoresettings->crm_test) && $program->scoresettings->crm_test > 0) 
                  @if($permissions['update-crm-score'])
                  <h6 style="color:red">Add CRM test score here</h6>
                  <div class="mb-3">
                    <label>CRM Test Score* <span style="color:green">(Max score =
                        {{$program->scoresettings->crm_test }})</span></label>
                    <input type="number" name="crm_score"
                      value="{{ old('crm_score') ?? $details['crm_test_score'] }}" class="form-control" min="0"
                      max="{{$program->scoresettings->crm_test }}" required>
                  </div>
                  <div><small style="color:red">{{ $errors->first('crm_score')}}</small></div>
                  </div>
                  @endif
                @endif
              @endif

            </div>

            <div class="row">
              <div class="col-md-12">
                @if($permissions['update-certification-score'])
                  <h6 style="color:red">Certificate Test Submision</h6>
                  <p>Please go through this user's attempt and grade user with the grade box below</p>

                  <div class="mb-3">
                    @foreach($user_results as $results)

                    <label for="title"> <strong style="color:green">QUESTION {{ $i ++  }}</strong></label><br>
                    <strong style="color:green">Module:</strong> {!! $results->module->title.'<br><br>' !!}</span>

                    <strong style=:color:green>Question:</strong> {!! $results['title'] .'<br><br>' !!}</span>

                    <strong>Answer:</strong> {!! $results['answer'] .'<br><br>' !!}

                    @endforeach
                    
                  </div>

                  <h6 style="color:red">Now, score this candidate's certification test (Result with score of 10 will be
                    recorded as 'processing' on cadidate's dashboard): </h6>
                  <div class="mb-3">
                    <label><span style="color:green">(Max score =
                        {{ $program->scoresettings->certification}})</span></label>
                    <input type="number" name="certification_score"
                      {{ (checkRoleHas(['Admin','Facilitator'])) ? "" : 'Readonly' }}
                      value="{{ old('certification_score') ?? $details['certification_score'] }}" class="form-control"
                      min="0" max="{{ $program->scoresettings->certification }}">
                  </div>
                @else
                  <input type="hidden" value="{{ $details['certification_score'] }}" name="certification_score">
                @endif
                @if($permissions['update-certification-score'] && $permissions['results.grader'])
                <div class="mb-3">
                  <label>Grader Comment(Optional) </label>
                  <textarea name="grader_comment" class="form-control" id="" cols="30" rows="10"
                    value="{{ old('grader_comment') ?? $details['grader_comment'] }}">{{ old('grader_comment') ?? $details['grader_comment'] }}</textarea>

                </div>
                @endif
                @if($permissions['update-certification-score'] && $permissions['results.facilitator'])
                <div class="mb-3">
                  <label>Facilitator Comment(Optional) </label>
                  <textarea name="facilitator_comment" class="form-control" id="" cols="30" rows="10"
                    value="{{ old('facilitator_comment') ?? $details['facilitator_comment'] }}">{{ old('facilitator_comment') ?? $details['facilitator_comment'] }}</textarea>

                </div>
                @endif
                
                <div class="row">
                  <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                </div>
                
              </div>
            </div>

          </form>
          @else
          <h2>Expecting user to re-take certification tests hence scores cannot be modified </h2>
          @endif
        </div>
      </div>
      <!-- The Modal -->
      <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
          <button type="button" class="btn-close" aria-label="Close"></button>
          <div class="card">
            <div class="card-body">
              <div class="card-title">
                <h4 style="color:green">RESIT HISTORY</h4> <br>
                <div>
                  <div class="card-content">
                    @if(isset($history) && !empty($history))
                      @foreach($history as $result)
                      
                            <strong style="background: #f71193;padding: 11px;border-radius: 20px;color: white;;"> Submitted on: {{ $result->submitted_on }} </strong> <br> <br>

                            <label for="title"> <strong style="color:green">QUESTION {{ $i ++  }}</strong></label><br>
                            <strong style="color:green">Module:</strong> {!! $result->module->title.'<br><br>' !!}</span>

                            <strong style=:color:green>Question:</strong> {!! $result->title .'<br><br>' !!}</span>

                            <strong>Answer:</strong> {!! $result->answer .'<br><br>' !!} <br>
                             <br>
                            <strong style="color:green">Facilitator's comment</strong>({{ $result->marked_by  }}): {!! $result->facilitator_comment !!}</span> <br>
                            <strong style="color:green">Grader's comment:</strong>({{ $result->grader_comment  }}): {!! $result->grader_comment !!}</span> <br>
                            <strong style="color:green">Score:</strong>{{ $result->certification_test_score  }}</span>
                            
                          {{-- </div> --}}
                        {{-- </div> --}}
                        <hr style="border-top: 1px solid red;">

                      @endforeach
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("myBtn");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("btn-close")[0];

            // When the user clicks the button, open the modal 
            btn.onclick = function () {
              modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
              modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
              if (event.target == modal) {
                modal.style.display = "none";
              }
            }
        </script>
      @endsection
