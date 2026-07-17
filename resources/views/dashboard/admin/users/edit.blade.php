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
                                <div class="mb-3{{ $errors->has('name') ? ' is-invalid' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}"
                                        autofocus>
                                    @if ($errors->has('name'))
                                        <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email }}">
                                    @if ($errors->has('email'))
                                        <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3{{ $errors->has('phone') ? ' is-invalid' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') ?? $user->phone }}"
                                        autofocus>
                                    @if ($errors->has('phone'))
                                        <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div> 
                                <div class="mb-3{{ $errors->has('job_title') ? ' is-invalid' : '' }}">
                                    <label for="job_title">Job Title</label>
                                    <input id="job_title" type="text" class="form-control" name="job_title" value="{{ old('job_title') ?? $user->job_title }}"
                                        autofocus>
                                    @if ($errors->has('job_title'))
                                        <div class="text-danger small mt-1">{{ $errors->first('job_title') }}</div>
                                    @endif
                                </div>  
                            </div>
                            <div class="col-md-6">
                                 <div class="mb-3{{ $errors->has('password') ? ' is-invalid' : '' }}">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="text-muted small mb-2">Default: 12345</div>
                                    <input id="password" type="password" class="form-control" name="password" value="{{ old('password') ?? '' }}"
                                        autofocus>
                                    @if ($errors->has('password'))
                                        <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                <div><small style="color:red">{{ $errors->first('class')}}</small></div>
                               
                                <div class="mb-3">
                                    <label for="class">Role *</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Student" {{ $user->roles == 'Student' ? 'selected' : ''}}>Student</option>
                                        @if(checkRoleHas(['Admin']))
                                        <option value="Teacher" {{ $user->roles == 'Teacher' ? 'selected' : ''}}>Facilitator</option>
                                        <option value="Grader" {{ $user->roles == 'Grader' ? 'selected' : ''}}>Grader</option>
                                        @endif
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                        
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>
                                
                                <div class="mb-3{{ $errors->has('staffID') ? ' is-invalid' : '' }}">
                                    <label for="staffID">Staff Id</label>
                                    <input id="staffID" type="text" class="form-control" name="staffID" value="{{ old('staffID') ?? $user->staffID }}"
                                        autofocus>
                                    @if ($errors->has('staffID'))
                                        <div class="text-danger small mt-1">{{ $errors->first('staffID') }}</div>
                                    @endif
                                </div>
                            </div>
                            @if(isset($associated))
                                <div class="col-md-12">
                                    <div class="row mb-3">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-select mt-3" multiple="multiple" style="width: 100%;" required>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ in_array($program->id, $associated) ? 'selected' : ''}} >{{ $program->p_name }}</option>
                                        @endforeach
                                        </select>

                                        <div>
                                        @if ($errors->has('training'))
                                            <div class="text-danger small mt-1">{{ $errors->first('training') }}</div>
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
