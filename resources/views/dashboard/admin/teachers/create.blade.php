@extends('dashboard.admin.index')
@section('title', 'Add Facilitator')
@section('css')
    @parent
    @include('dashboard.admin.teachers.partials.form-styles')
@endsection
@section('content')
<div class="container-fluid teacher-page">
    <header class="teacher-page-header">
        <div>
            <span class="teacher-page-eyebrow">Teacher management / New</span>
            <h1 class="teacher-page-title">Add a teacher</h1>
            <p class="teacher-page-description">Create a staff account and set up professional details and access.</p>
        </div>
        <a href="{{ route('teachers.index') }}" class="teacher-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i> All teachers</a>
    </header>
    <div class="row">
        <div class="col-md-12">
            <div class="card teacher-form-card">
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        <section class="teacher-form-section">
                            <h2 class="teacher-section-title">Account details</h2>
                            <p class="teacher-section-hint">Set the login details, staff role, and account status.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role">Role*</label>
                                    <select name="role[]" id="role" class="select2 role form-control" multiple="multiple" required>
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Facilitator" {{ in_array('Facilitator', (array) old('role', [])) ? 'selected' : '' }}>Facilitator</option>
                                        <option value="Grader" {{ in_array('Grader', (array) old('role', [])) ? 'selected' : '' }}>Grader</option>
                                        <option value="Admin" {{ in_array('Admin', (array) old('role', [])) ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <div><small class="text-danger">{{ $errors->first('role') }}</small></div>
                                </div>
                                <div class="mb-3{{ $errors->has('payment_mode') ? ' is-invalid' : '' }}">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode" class="form-control">
                                        <option value="">No payment mode</option>
                                        @foreach($payment_modes as $mode)
                                            <option value="{{ $mode->id }}" {{ old('payment_mode') == $mode->id ? 'selected' : '' }}>
                                                {{ ucFirst($mode->name) }}{{ $mode->status !== 'active' ? ' (Inactive)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_mode')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3{{ $errors->has('name') ? ' is-invalid' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>
                                    @if ($errors->has('name'))
                                        <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3{{ $errors->has('status') ? ' is-invalid' : '' }}">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Upload Profile Picture</label>
                                    <div class="teacher-profile-preview mb-2" id="profile_picture" style="display:none">
                                        <img src="" alt="Verified teacher profile picture">
                                    </div>
                                    <input type="file" name="file" value="{{ old('avatar') }}" class="form-control">
                                </div>
                                <input type="hidden" id="picture" name="picture" value="">
                                <div class="mb-3{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                        <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>

                                <div style="margin-top: 5px;" class="mb-3">
                                    <label for="email">Phone</label>
                                    <input id="phone" type="phone" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                                <div class="mb-3{{ $errors->has('password') ? ' is-invalid' : '' }}">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="text-muted small mb-2">Default: 12345</div>
                                    <input id="password" type="password" class="form-control" name="password"
                                        autocomplete="new-password">
                                    @if ($errors->has('password'))
                                        <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                 
                            </div>
                        </div>
                        </section>
                        @include('dashboard.admin.teachers.partials.profile-fields', ['teacher' => null, 'showLicenseVerification' => true])
                        <section class="teacher-form-section">
                            <h2 class="teacher-section-title">Training access</h2>
                            <p class="teacher-section-hint">Choose the programs this teacher will support.</p>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="training">Select trainings</label>
                                        <select name="training[]" id="training" class="select2 form-select mt-1" multiple="multiple" style="width: 100%;">
                                            @foreach($programs as $program)
                                                <option value="{{ $program->id }}" {{ in_array($program->id, (array) old('training', [])) ? 'selected' : '' }}>{{ $program->p_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('training')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                        {{-- <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12">
                               <span><h6>Admin Menu Permissions</h6></span>
                               
                            </div>
                            @foreach(app('app\Http\Controllers\Controller')->adminMenus() as $menu)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="menu_permissions[]" value="{{$menu['id']}}" id="{{$menu['id']}}">
                                    <label class="form-check-label" for="{{$menu['id']}}">
                                        {{$menu['name']}}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div> --}}
                        {{ csrf_field() }}
                        <div class="teacher-submit-bar">
                            <button type="submit" class="btn teacher-submit-button">Create teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
    <script>
        CKEDITOR.replace('ckeditor');
        var editor = CKEDITOR.instances['ckeditor'];
        src = "{{ env('ENT') == 'demo' ? 'http://localhost:8888/waacsp/public/api/verifyinstructor' : 'https://thewaacsp.com/api/verifyinstructor' }}";
        token = "{{ \App\Models\Settings::value('token') }}";
        
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
                    $("#profile_picture img").attr("src",res.data.avatar);
                    $("#picture").val(res.data.avatar); 
                    $("#verify-button").css("bakground:green");
                    $("#verify-button").html("<span class='btn btn-success' style='float:left' id='verify'>Verified!</span>"); 
                    
                },

                error: function(xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    // console.log(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                    $('#result').html(err.message).show();
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
