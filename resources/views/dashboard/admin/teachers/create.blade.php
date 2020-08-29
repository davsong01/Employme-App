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
                            <h4 class="card-title">Add new Facilitator</h4>
                        </div>
                        <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                         
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Name</label>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name')}}" autofocus required >
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
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone">Phone</label>
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone')}}" autofocus>
                                        @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    
                                   
                                </div>
                                <div class="col-md-6">
                                    
                                    <div class="form-group">
                                        <label for="class">Role *</label>
                                        <select name="role" id="class" class="form-control" >
                                            <option value="" disabled>Assign Role</option>
                                            <option value="Facilitator">Facilitator</option>
                                            <option value="Grader">Grader</option>
                                            <option value="Admin">Admin</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="training">Select Training *</label>
                                        <select name="training" id="training" class="form-control" required>
                                            <option value=""></option>
                                            @foreach ($programs as $program)
                                            <option value="{{ $program->id }}">
                                                {{$program->p_name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('training'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('training') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password">Password</label>
                                        <input id="password" type="text" class="form-control" name="password" value="{{ old('password') ?? 12345 }}"
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
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    Submit
                                </button>
                            </div>
                            {{ csrf_field() }}
            </div>
        </div>
    </div>
</div>
@endsection