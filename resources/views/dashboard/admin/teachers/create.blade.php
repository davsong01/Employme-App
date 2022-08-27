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
                                    <label for="license">WAACSP license</label>
                                    <input id="license" type="text" class="form-control" name="license" value="{{ old('license') }}" autofocus >
                                    <span class="help-block">
                                        <strong style="color:green" id="result"></strong>
                                    </span>
                                <span class="btn btn-info" id="verify" onclick="myFunction()">Verify license</span>

                                </div>
                               
                                <div class="form-group">
                                    <label for="class">Payment method*</label>
                                    <select name="payment_method" id="class" class="form-control">
                                        <option value="" disabled>Select</option>
                                        @foreach($payment_methods as $method)
                                        <option value="payment_method" {{ old('payment_method') == $method->id ? 'selected' : ''}}>{{ ucfirst($method->slug) }}</option>
                                        @endforeach
                                        
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
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

                                <div class="form-group">
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
                                {{-- <div class="form-group">
                                    <label for="earning_per_head">Earning per head ({{ \App\Settings::first()->value('DEFAULT_CURRENCY') }})</label>
                                    <input id="earning_per_head" type="number" step="0.01" class="form-control" name="earning_per_head" value="{{ old('earning_per_head')}}" required>
                                    @if ($errors->has('earning_per_head'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('earning_per_head') }}</strong>
                                    </span>
                                    @endif
                                </div> --}}
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
    src = "{{ !is_null(env('ENT')) ? 'http://127.0.0.1:8001/api/verifyinstructor' : 'http://thewaacsp.com/api/verifyinstructor' }}";
    token = "{{ \App\Settings::value('token') }}";
    function myFunction() {
        $.ajax({
            url: src,
            type: "POST",
            data: {
                license:  $('#license').val(),
                token: token
            },
            beforeSend: function(xhr){
                $('#result').prepend('LOADING...');
            },
            success: function(res){
                // console.log(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#result').html(res.message);
                $('#name').val(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#license').val(res.data.license);
                $('#email').val(res.data.email);
                $('#phone').val(res.data.phone);
                editor.insertText(res.data.short_profile); 
                $("#profile_picture").css("display",'block'); 
                $("#profile_picture").attr("src",res.data.avatar); 
                $("#picture").val(res.data.avatar); 
                $("#verify").css("display",'none'); 
                
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