@extends('dashboard.admin.index')
@section('title', $user->name )
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
                    <form action="{{route('profiles.update', $user->id)}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3{{ $errors->has('name') ? ' is-invalid' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ old('name') ?? $user->name }}" autofocus>
                                    @if ($errors->has('name'))
                                        <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                               
                                <div class="mb-3{{ $errors->has('phone') ? ' is-invalid' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                        value="{{ old('phone') ?? ($user->phone ?? $user->phone) }}" autofocus>

                                    @if ($errors->has('phone'))
                                        <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3{{ $errors->has('password') ? ' is-invalid' : '' }}">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="text-muted small mb-2">Default: 12345. Leave blank if you want to keep the default password.</div>
                                    <input id="password" type="text" class="form-control" name="password"
                                        value="{{ old('password') ?? '' }}" autofocus>
                                    @if ($errors->has('password'))
                                        <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') ?? $user->email }}" disabled>
                                    @if ($errors->has('email'))
                                        <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="training">Current Training</label>
                                    <input id="training" type="text" class="form-control" name="training"
                                        value="{{ $user->programs->p_name }}" autofocus disabled>
                                    @if ($errors->has('training'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('training') }}</strong>
                                    </span>
                                    @endif
                                </div> --}}
                                {{-- <div class="mb-3{{ $errors->has('location') ? ' is-invalid' : '' }}">
                                    <label for="location">Location</label>
                                    <input id="location" type="text" class="form-control" name="location"
                                        value="{{ old('location') ?? $user->t_location }}" autofocus disabled>
                                    @if ($errors->has('location'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('location') }}</strong>
                                    </span>
                                    @endif
                                </div> --}}
                                <div class="mb-3">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control" required>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>

                                <div class="mb-3">
                                    <label>Profile Picture</label>
                                    <input type="file" name="image" value="" class="form-control">
                                </div>
                                <div><small style="color:red">{{ $errors->first('image')}}</small></div>
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
