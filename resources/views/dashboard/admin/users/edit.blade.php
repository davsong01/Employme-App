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
                    <form action="{{route('users.update', $user->id)}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
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
                                <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                    <label for="location">Location *</label>
                                    <input id="location" type="text" class="form-control" name="location"
                                        value="{{ old('location') ?? $user->t_location }}" autofocus>
                                    @if ($errors->has('location'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('location') }}</strong>
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
                                <div><small style="color:red">{{ $errors->first('class')}}</small></div>
                                <div class="form-group">
                                    <label for="training">Select Training *</label>
                                    <select name="training" id="training" class="form-control">
                                        @foreach ($programs as $program)
                                        <option value="{{ $program->id }}" {{ $program->id == $user->program_id ? 'selected' : ''}}>
                                            {{$program->p_name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('training'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('training') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount Paid *</label><span>Already paid: <strong>{{$user->t_amount}}</strong></span>
                                    <input type="number" name="amount" value="{{ old('amount') ?? 0 }}" class="form-control">
                                </div>
                                <!--Gives the first error for input name-->
                                <div><small style="color:red">{{ $errors->first('amount')}}</small></div>
                                <div class="form-group">
                                    <label>Bank *</label>
                                    <input type="text" name="bank" value="{{ old('bank') ?? $user->t_type }}" class="form-control">
                                </div>
                        
                                <div class="form-group">
                                    <label for="class">Role *</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Student" {{ $user->role_id == 'Student' ? 'selected' : ''}}>Student</option>
                                        <option value="Teacher" {{ $user->role_id == 'Teacher' ? 'selected' : ''}}>Teacher</option>
                                        <option value="Admin" {{ $user->role_id == 'Admin' ? 'selected' : ''}}>Admin</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                                <div class="form-group{{ $errors->has('transaction_id') ? ' has-error' : '' }}">
                                    <label for="transaction_id">Transaction Id</label>
                                    <input id="transaction_id" type="text" class="form-control" name="transaction_id"
                                        value="{{ old('transaction_id') ?? $user->transid }}" autofocus>
                                    @if ($errors->has('transaction_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('transaction_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="1" {{ $user->gender == 1 ? 'selected' : ''}}>Male</option>
                                        <option value="0" {{ $user->gender == 0 ? 'selected' : ''}}>Female</option>
                        
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
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