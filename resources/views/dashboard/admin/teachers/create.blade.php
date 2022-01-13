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
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus >
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
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
                                    <label for="class">Available off season?</label>
                                    <select name="off_season_availability" id="class" class="form-control">
                                        <option value="" {{ old('off_season_availability') == '' ? 'selected' : ''}}>No</option>
                                        <option value="1" {{ old('off_season_availability') == '1' ? 'selected' : ''}}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                
                                <div class="form-group">
                                    <label>Upload Profile Picture</label>
                                    <input type="file" name="file" value="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="earning_per_head">Earning per head ({{ \App\Settings::first()->value('DEFAULT_CURRENCY') }})</label>
                                    <input id="earning_per_head" type="number" step="0.01" class="form-control" name="earning_per_head" value="{{ old('earning_per_head')}}">
                                    @if ($errors->has('earning_per_head'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('earning_per_head') }}</strong>
                                    </span>
                                    @endif
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