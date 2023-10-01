@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
<style>
.select2-container--default.select2-container--focus .select2-selection--multiple {
    height: 200px !important;
}
</style>
   
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Edit details for: {{$user->name}}</h4>
                    </div>
                    <form action="{{route('users.update', $user->id)}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                          {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}"
                                        autofocus>
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email }}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') ?? $user->t_phone }}"
                                        autofocus>
                                    @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>   
                            </div>
                            <div class="col-md-6">
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
                                <div><small style="color:red">{{ $errors->first('class')}}</small></div>
                               
                                <div class="form-group">
                                    <label for="class">Role *</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Student" {{ $user->role_id == 'Student' ? 'selected' : ''}}>Student</option>
                                        @if(Auth::user()->role_id == 'Admin')
                                        <option value="Teacher" {{ $user->role_id == 'Teacher' ? 'selected' : ''}}>Facilitator</option>
                                        <option value="Grader" {{ $user->role_id == 'Grader' ? 'selected' : ''}}>Grader</option>
                                        @endif
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                        
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>
                            </div>
                            @if(isset($associated))
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;" required>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ in_array($program->id, $associated) ? 'selected' : ''}} >{{ $program->p_name }}</option>
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
                            @endif
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Update
                            </button>
                        </div>
                      
                </div>
            </div>
        </div>
    </div>
</div>
@endsection