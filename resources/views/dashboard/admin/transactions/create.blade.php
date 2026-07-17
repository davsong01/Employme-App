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

                                <div class="mb-3{{ $errors->has('name') ? ' is-invalid' : '' }}">

                                    <label for="name" class="form-label">Name</label>

                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ old('name')}}" autofocus>

                                    @if ($errors->has('name'))

                                    <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>

                                    @endif

                                </div>

                                <div class="mb-3{{ $errors->has('email') ? ' is-invalid' : '' }}">

                                    <label for="email" class="form-label">E-Mail Address</label>

                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ old('email') }}">

                                    @if ($errors->has('email'))

                                    <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>

                                    @endif

                                </div>

                                <div class="mb-3{{ $errors->has('phone') ? ' is-invalid' : '' }}">

                                    <label for="phone" class="form-label">Phone</label>

                                    <input id="phone" type="text" class="form-control" name="phone"
                                        value="{{ old('phone')}}" autofocus>

                                    @if ($errors->has('phone'))

                                    <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>

                                    @endif

                                </div>

                                <div class="mb-3{{ $errors->has('location') ? ' is-invalid' : '' }}">

                                    <label for="location" class="form-label">Location *</label>

                                    <input id="location" type="text" class="form-control" name="location"
                                        value="{{ old('location')}}" autofocus>

                                    @if ($errors->has('location'))

                                    <div class="text-danger small mt-1">{{ $errors->first('location') }}</div>

                                    @endif

                                </div>

                                <div class="mb-3{{ $errors->has('password') ? ' is-invalid' : '' }}">

                                    <label for="password" class="form-label">Password</label>

                                    <input id="password" type="text" class="form-control" name="password"
                                        value="{{ old('password') ?? 12345 }}" autofocus>

                                    @if ($errors->has('password'))

                                    <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>

                                    @endif

                                </div>

                                <div class="mb-3">

                                    <label for="training" class="form-label">Select Training *</label>

                                    <select name="training" id="training" class="form-select">

                                        <option value=""></option>

                                        @foreach ($programs as $program)

                                        <option value="{{ $program->id }}">

                                            {{$program->p_name}}</option>

                                        @endforeach

                                    </select>

                                    @if ($errors->has('training'))

                                    <div class="text-danger small mt-1">{{ $errors->first('training') }}</div>

                                    @endif

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">Amount Paid *</label>

                                    <input type="number" name="amount" value="{{ old('amount') }}" min="0"
                                        class="form-control">

                                </div>

                                <div class="text-danger small mt-1">{{ $errors->first('amount') }}</div>

                                <div class="mb-3">

                                    <label class="form-label">Bank *</label>

                                    <input type="text" name="bank" value="{{ old('bank') }}" class="form-control">

                                </div>

                                @if ($errors->has('bank'))

                                <div class="text-danger small mt-1">{{ $errors->first('bank') }}</div>

                                @endif

                                <div class="mb-3">

                                    <label for="role" class="form-label">Role *</label>

                                    <select name="role" id="role" class="form-select">

                                        <option value="" disabled>Assign Role</option>

                                        <option value="Student">Student</option>

                                    </select>

                                    <div class="text-danger small mt-1">{{ $errors->first('role') }}</div>

                                </div>

                                <div class="mb-3{{ $errors->has('transaction_id') ? ' is-invalid' : '' }}">

                                    <label for="transaction_id" class="form-label">Transaction Id</label>

                                    <input id="transaction_id" type="text" class="form-control" name="transaction_id"
                                        value="{{ old('transaction_id') }}" autofocus>

                                    @if ($errors->has('transaction_id'))

                                    <div class="text-danger small mt-1">{{ $errors->first('transaction_id') }}</div>

                                    @endif

                                </div>

                                <div class="mb-3">

                                    <label for="gender" class="form-label">Gender</label>

                                    <select name="gender" id="gender" class="form-select">

                                        <option value="" disabled>Select gender</option>

                                        <option value="Male">Male</option>

                                        <option value="Female">Female</option>



                                    </select>

                                    <div class="text-danger small mt-1">{{ $errors->first('gender') }}</div>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label d-block">Bypass EarlyBird Check</label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="earlybird" id="earlybird">
                                        <label class="form-check-label text-danger small" for="earlybird">
                                            Check this only if the student paid EarlyBird amount after Earlybird has expired.
                                        </label>
                                    </div>

                                    <div class="text-danger small mt-1">{{ $errors->first('earlybird') }}</div>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <button type="submit" class="btn btn-primary" style="width:100%">

                                Submit

                            </button>

                        </div>

                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
