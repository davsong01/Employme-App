@extends('dashboard.student.index')
@section('title', $user->name )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Edit Profile</h4>
                    </div>
                    <form action="{{route('profiles.update', $user->id)}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ old('name') ?? $user->name }}" autofocus>
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                               
                                <div class="form-group{{ $errors->has('t_phone') ? ' has-error' : '' }}">
                                    <label for="t_phone">Phone</label>
                                    <input id="t_phone" type="text" class="form-control" name="t_phone"
                                        value="{{ old('t_phone') ?? $user->t_phone }}" autofocus>
                                    @if ($errors->has('t_phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('t_phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">Password: </label><span class="help-block">
                                        <strong>Default: 12345</strong> (Leave blank if you want to keep the default password)
                                    </span>
                                    <input id="password" type="text" class="form-control" name="password"
                                        value="{{ old('password') ?? '' }}" autofocus>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
                                    <label for="job_title">Job Title</label>
                                    <input id="job_title" type="text" class="form-control" name="job_title"
                                        value="{{ old('job_title') ?? $user->job_title }}" autofocus>
                                    @if ($errors->has('job_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('job_title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') ?? $user->email }}" {{ !empty($user->email) ? 'disabled' : ''}}>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="staffID">Staff ID</label>
                                    <input id="staffID" type="staffID" class="form-control" name="staffID"
                                        value="{{ old('staffID') ?? $user->staffID }}">
                                </div>
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control" required>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female
                                        </option>

                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>  
                                <div class="form-group">
                                    @if(isset($user->profile_picture) && $user->profile_picture == "avatar.jpg")
                                    <label>Upload Profile Picture</label> <br>
                                    <img src="{{ url('/').'/profiles/avatar.jpg'}}" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                    <input type="file" name="image" value="{{ old('profile_picture') ??  $user->profile_picture }}" class="form-control">
                                    @else
                                    <label>Replace Profile Picture</label> <br>
                                    <img src="{{ asset('/avatars/'.$user->profile_picture) }}" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                        <input type="file" name="image" value="{{ old('image') ??  $user->profile_picture }}" class="form-control">
                                    @endif
                                   
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection