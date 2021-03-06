@extends('layouts.app')

@section('content')
<div class="col-md-7">
    <div class="card-body">
        <div class="brand-wrapper">
            <img src="{{ asset('login_files/assets/images/logo.png') }}" alt="logo"
                style="width: 230px !important">
        </div>
        <p class="login-card-description">Reset Password</p>
        <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="control-label">E-Mail Address</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group mb-4{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="control-label">Password</label>
                    <input id="password" type="password" class="form-control" name="password" required>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
             </div>

            <div class="form-group mb-4{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <label for="password-confirm" class="control-label">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
            </div>

            <div class="form-group mb-4">
                    <button type="submit" class="btn btn-block login-btn mb-4">
                        Reset Password
                    </button>
            </div>
        </form>
        <br><br>
        <span>Designed by <a href="https://techdaves.com">Techdaves</a></span>
    </div>
</div>

@endsection