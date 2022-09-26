@extends('dashboard.admin.index')
@section('title', 'Add Facilitator')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    <div class="card-title">
                        <h4 class="card-title">Add new Facilitator/Grader</h4>
                    </div>
                    <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class">Role*</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Facilitator" {{ old('role') == 'Facilitator' ? 'selected' : ''}}>Facilitator</option>
                                        <option value="Grader" {{ old('role') == 'Grader' ? 'selected' : ''}}>Grader</option>
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : ''}}>Admin</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="license">WAACSP license</label> <br>
                                    <input id="license" type="text" style="width:79%;float:left" class="form-control" name="license" value="{{ old('license') }}" autofocus >
                                    <span id="verify-button"><span class="btn btn-info" style="float:left"  id="verify" onclick="myFunction()">Verify license</span></span>
                                    <span class="help-block">
                                        <strong id="result"></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="waacsp_url" style="margin-top: 15px;">WAACSP url</label> <br>
                                    <input id="waacsp_url" type="text"  class="form-control" name="waacsp_url" value="{{ old('waacsp_url') }}" autofocus >
                                    
                                </div>
                              
                                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode " class="form-control" required>
                                        @foreach($payment_modes as $mode)
                                        <option value="{{ $mode->id }}" selected alt="">{{ ucFirst($mode->name) }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus >
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label for="status">Status</label>
                                    <select name="status" id="type" class="form-control" required>
                                        <option value="active" selected>Active</option> 
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Profile Picture</label>
                                    <img style="display:none; width: 80px;border-radius: 50%;height: 80px;padding: 10px;" id="profile_picture" src="" alt="">
                                    <input type="file" name="file" value="{{ old('avatar') }}" class="form-control">
                                </div>
                                <input type="hidden" id="picture" name="picture" value="">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div style="margin-top: 5px;" class="form-group">
                                    <label for="email">Phone</label>
                                    <input id="phone" type="phone" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="class">Available off season?</label>
                                    <select name="off_season_availability" id="class" class="form-control">
                                        <option value="" {{ old('off_season_availability') == '' ? 'selected' : ''}}>No</option>
                                        <option value="1" {{ old('off_season_availability') == '1' ? 'selected' : ''}}>Yes</option>
                                    </select>
                                </div>
                                
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">Password: </label><span class="help-block">
                                        <strong>Default: 12345</strong>
                                    </span>
                                    <input id="password" type="text" class="form-control" name="password" value="{{ old('password') ?? '' }}"
                                        autofocus>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;" required>
                                        @foreach($programs as $program)
                                             <option value="{{ $program->id }}">{{ $program->p_name }}</option>
                                        @endforeach
                                        </select>
                                    <div>
                                        @if ($errors->has('training'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('training') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                <div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                                    
                                    <label for="profile" style="color:red">Profile overview</label>
                                    <textarea id="ckeditor" type="text" class="form-control" name="profile" value="{{ old('profile') }}" rows="8" autofocus>{{ old('profile')  }}</textarea>

                                    @if ($errors->has('profile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('profile') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                       {{ csrf_field() }}
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
</div>
@section('extra-scripts')
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('ckeditor');
    var editor = CKEDITOR.instances['ckeditor'];
    src = "{{ env('ENT') == 'demo' ? 'http://127.0.0.1:8000/api/verifyinstructor' : 'https://thewaacsp.com/api/verifyinstructor' }}";
    token = "{{ \App\Settings::value('token') }}";

    function myFunction() {
        $.ajax({
            url: src,
            headers: {
                license:  $('#license').val(),
                token: token
            },

            beforeSend: function(xhr){
                $('#result').prepend('LOADING...');
            },
            success: function(res){
                // console.log(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#result').css('display','none');
                $('#name').val(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#license').val(res.data.license);
                $('#email').val(res.data.email);
                $('#phone').val(res.data.phone);
                $('#waacsp_url').val(res.data.url);
                editor.insertText(res.data.short_profile); 
                $("#profile_picture").css("display",'block'); 
                $("#profile_picture").attr("src",res.data.avatar); 
                $("#picture").val(res.data.avatar); 
                $("#verify-button").css("bakground:green");
                $("#verify-button").html("<span class='btn btn-success' style='float:left' id='verify'>Verified!</span>"); 
                
            },

            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                // console.log(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#result').html(err.message);
                $('#result').css('color','red');

            }
        });
    }
   
</script>

<script>                                
    $('#role').on('change', function(){
        console.log($('#role').val());
            
            if($('#role').val()=='Facilitator' || $('#role').val()=='Grader' ){
                $('.selecttraining').css('display','block');
                
            }else if($('#role').val()=='Admin'){
                $('.selecttraining').css('display','none');
                
            }
    });


    
</script>
@endsection
@endsection