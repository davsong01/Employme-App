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
                    <form action="{{route('participants.profiles.update', $user->id)}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ old('name') ?? $user->name }}" autofocus>
                                    @if ($errors->has('name'))
                                        <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                               
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                        value="{{ old('phone') ?? $user->phone }}" autofocus>
                                    @if ($errors->has('phone'))
                                        <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="text-muted small mb-2">Default: 12345. Leave blank if you want to keep the current password.</div>
                                    <input id="password" type="password" class="form-control" name="password"
                                        value="{{ old('password') ?? '' }}" autofocus>
                                    @if ($errors->has('password'))
                                        <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="job_title">Job Title</label>
                                    <input id="job_title" type="text" class="form-control" name="job_title"
                                        value="{{ old('job_title') ?? $user->job_title }}" autofocus>
                                    @if ($errors->has('job_title'))
                                        <div class="text-danger small mt-1">{{ $errors->first('job_title') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') ?? $user->email }}" {{ !empty($user->email) ? 'disabled' : ''}}>
                                    @if ($errors->has('email'))
                                        <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="staffID">Staff ID</label>
                                    <input id="staffID" type="staffID" class="form-control" name="staffID"
                                        value="{{ old('staffID') ?? $user->staffID }}">
                                </div>
                                <div class="mb-3">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control" required>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female
                                        </option>

                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>  
                                <div class="mb-3">
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
