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

<div class="row">
  <div class="container-fluid">
      <div class="card-title">
        <h4 style="color:green">Update Scores for: {{ $details['user_name'] }}</h4>
        <div>
          {{-- @if(isset($history) && !empty($history))
            <span class="retake">RESITS</span>
            <span style="background: aqua; padding: 5px 10px; border-radius: 50%; display: inline-block; text-align: center; width: 30px; height: 30px; line-height: 20px;" class="thread-count">
                {{ $history->count() }}
            </span>
            @if($history->count() > 0)
            <a style="border-radius: 6px;" class="btn btn-info btn-sm" href="#resit" id="myBtn">
              View Resit History
            </a>
            @endif
          @endif --}}
        </div>
      </div>
      
      <form id="editResultForm" action="{{route('results.update', $result_id)}}" method="POST" enctype="multipart/form-data"
          class="pb-2">
          {{ csrf_field() }}
          {{ method_field('PATCH') }}
          <div class="row">
            <div class="col-md-6">
              <h6 style="color:red">Training details</s></h6>
              <!--Gives the first error for input name-->
              <div class="form-group">
                <label>Training</label>
                <input type="text" name="" value="{{ $program->p_name }}" class=" form-control" disabled>
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
              @if(!empty($program->scoresettings->email) && $program->scoresettings->email > 0) 
                @if($permissions['update-email-score'])
                  <h6 style="color:red">Add Email score here</h6>
                  <div class="form-group">
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
                <div class="form-group">
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
                <div class="form-group">
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

          @if(!empty($program->scoresettings->certification) && $program->scoresettings->certification > 0)
            <div class="row">
              <div class="col-md-12">
                @if($permissions['update-certification-score'])
                  <h6 style="color:red">Certificate Test Submision</h6>
                  <p>Please go through this user's attempt and grade user with the grade box below</p>

                  <div class="form-group">
                    @foreach($user_results as $results)
                      <label for="title"> <strong style="color:green">QUESTION {{ $i ++  }}</strong></label><br>
                      <strong style="color:green">Module:</strong> {!! $results->module->title.'<br><br>' !!}</span>
                      <strong style=:color:green>Question:</strong> {!! $results['title'] .'<br><br>' !!}</span>
                      <strong>Answer:</strong> {!! $results['answer'] .'<br><br>' !!}
                    @endforeach
                  </div>

                  <h6 style="color:red">Now, score this candidate's certification test: </h6>
                  <div class="form-group">
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
                <div class="form-group">
                  <label>Grader Comment(Optional) </label>
                  <textarea name="grader_comment" class="form-control" id="" cols="30" rows="10"
                    value="{{ old('grader_comment') ?? $details['grader_comment'] }}">{{ old('grader_comment') ?? $details['grader_comment'] }}</textarea>

                </div>
                @endif
                @if($permissions['update-certification-score'] && $permissions['results.facilitator'])
                <div class="form-group">
                  <label>Facilitator Comment(Optional) </label>
                  <textarea name="facilitator_comment" class="form-control" id="" cols="30" rows="10"
                    value="{{ old('facilitator_comment') ?? $details['facilitator_comment'] }}">{{ old('facilitator_comment') ?? $details['facilitator_comment'] }}</textarea>

                </div>
                @endif
              
              </div>
            </div>
          @endif
          <input type="hidden" name="real_result_id" value="{{ $real_result_id }}">
          <div class="row">
              <button type="submit" class="btn btn-primary form-group" style="width:100%">Submit</button>
            </div>
      </form>
  </div>
</div>

<script>
  $('#editResultForm').on('submit', function (e) {
    e.preventDefault();
    const form = $(this); 
    const url = form.attr('action');
    const formData = new FormData(form[0]); 
 
    $('#formErrors').html('');
    $('#formErrorSpan').hide();

    $.ajax({
      url: url,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
          if (response.success) {
              $('#editResultModal').modal('hide');

              $(`#role_play_score` + response.id).text(response.role_play_score);
              $(`#certification_test_score` + response.id).text(response.certification_test_score);
              $(`#email_test_score` + response.id).text(response.email_test_score);
              $(`#crm_test_score` + response.id).text(response.crm_test_score);
              $(`#certification_facilitator` + response.id).text(response.certification_facilitator);
              $(`#certification_grader` + response.id).text(response.certification_grader);
              $(`#updated_at` + response.id).text(response.updated_at);

              $(`#total_score${response.id}`).html(`
                  <strong class="tit" id="total_score${response.id}" 
                          style="color: ${response.total_score < response.passmark ? 'red' : 'green'};">
                      ${response.total_score}%
                  </strong>
              `);
              
              window.location.href = window.location.href.split('#')[0] + '#result-row-' + response.id;
                  
              $('#formSuccessSpan-' + response.id).show();
              $('.formSuccess').html(response.message || 'Test Scores Updated Successfully');
          } else {
              $('#formErrorSpan').show();
              $('#formErrors').html(response.message || 'An error occurred.');
          }
      },
      error: function (xhr) {
          $('#formSuccessSpan-' + response.id).hide();

          if (xhr.responseJSON && xhr.responseJSON.errors) {
              const errors = Object.values(xhr.responseJSON.errors)
                  .map(err => `<div>${err}</div>`)
                  .join('');
              $('#formErrors').html(errors);
          } else {
              $('#formErrors').html('An unexpected error occurred.');
          }
      }
    });
  });
</script>
      