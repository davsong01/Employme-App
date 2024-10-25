@extends('layouts.app')

@section('content')
<div class="col-md-7">
    <div class="card-body">
        <div class="brand-wrapper">
            <img src="{{ asset('login_files/assets/images/logo.png') }}" alt="logo"
                style="width: 230px !important">
        </div>
        <p>Enter your email address and we will send you a Password reset link</p>
        <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
            {{ csrf_field() }}
            <div class="form-group mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="sr-only">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"  id="email" class="form-control"
                    placeholder="Email address">
            </div>
            @if ($errors->has('email'))
                <span class="help-block" style="font-weight: 50 !important;">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
            <button type="submit" class="btn btn-block login-btn mb-4">
                Send Password Reset Link
            </button>
        </form>
       <br><br>
        <span>Designed by <a href="https://techdaves.com" target="_blank">Techdaves</a></span>
    </div>
</div>

@endsection