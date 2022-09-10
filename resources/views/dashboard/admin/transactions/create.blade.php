@extends('dashboard.admin.index')
@section('title', 'Add User')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Add new User</h4>
                    </div>
                    <form action="{{route('users.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">

                                    <label for="name">Name</label>

                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ old('name')}}" autofocus>

                                    @if ($errors->has('name'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('name') }}</strong>

                                    </span>

                                    @endif

                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                    <label for="email">E-Mail Address</label>

                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') }}">

                                    @if ($errors->has('email'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('email') }}</strong>

                                    </span>

                                    @endif

                                </div>

                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">

                                    <label for="phone">Phone</label>

                                    <input id="phone" type="text" class="form-control" name="phone"
                                        value="{{ old('phone')}}" autofocus>

                                    @if ($errors->has('phone'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('phone') }}</strong>

                                    </span>

                                    @endif

                                </div>

                                <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">

                                    <label for="location">Location *</label>

                                    <input id="location" type="text" class="form-control" name="location"
                                        value="{{ old('location')}}" autofocus>

                                    @if ($errors->has('location'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('location') }}</strong>

                                    </span>

                                    @endif

                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                    <label for="password">Password</label>

                                    <input id="password" type="text" class="form-control" name="password"
                                        value="{{ old('password') ?? 12345 }}" autofocus>

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

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label>Amount Paid *</label>

                                    <input type="number" name="amount" value="{{ old('amount') }}" min="0"
                                        class="form-control">

                                </div>

                                <!--Gives the first error for input name-->

                                <div><small style="color:red">{{ $errors->first('amount')}}</small></div>

                                <div class="form-group">

                                    <label>Bank *</label>

                                    <input type="text" name="bank" value="{{ old('bank') }}" class="form-control">

                                </div>

                                @if ($errors->has('bank'))

                                <span class="help-block">

                                    <strong>{{ $errors->first('bank') }}</strong>

                                </span>

                                @endif

                                <div class="form-group">

                                    <label for="class">Role *</label>

                                    <select name="role" id="class" class="form-control">

                                        <option value="" disabled>Assign Role</option>

                                        <option value="Student">Student</option>

                                    </select>

                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>

                                </div>

                                <div class="form-group{{ $errors->has('transaction_id') ? ' has-error' : '' }}">

                                    <label for="transaction_id">Transaction Id</label>

                                    <input id="transaction_id" type="text" class="form-control" name="transaction_id"
                                        value="{{ old('transaction_id') }}" autofocus>

                                    @if ($errors->has('transaction_id'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('transaction_id') }}</strong>

                                    </span>

                                    @endif

                                </div>

                                <div class="form-group">

                                    <label for="class">Gender</label>

                                    <select name="gender" id="class" class="form-control">

                                        <option value="" disabled>Select gender</option>

                                        <option value="Male">Male</option>

                                        <option value="Female">Female</option>



                                    </select>

                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>

                                </div>

                                <div class="form-group">

                                    <label for="class">Bypass EarlyBird Check</label>

                                    <input type="checkbox" name="earlybird"> <small style="color:red"> Check this only
                                        if student paid EarlyBird

                                        amount

                                        after Earlybird has expired</small>

                                    </select>

                                    <div><small style="color:red">{{ $errors->first('earlybird')}}</small></div>

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