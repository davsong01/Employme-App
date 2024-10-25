@extends('layouts.app')

@section('content')
<div class="col-md-7">
    <div class="card-body">
        <div class="brand-wrapper">
            <a href="/">
                <img src="{{ asset('login_files/assets/images/logo.png') }}" alt="logo"
                style="width: 230px !important">
            </a>
        </div>
        <p class="login-card-description">Login to Start learning</p>
        @include('layouts.partials.alerts')

        <form method="POST" action="{{ route('login') }}" method="post" style="max-width:100% !important">
            {{ csrf_field() }} 
            <div class="form-row">
                    <div class="form-group col-md-12 mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="login" class="sr-only">Email or Staff ID</label>
                        <input type="text" name="login" value="{{ old('login') }}"  id="login" class="form-control"
                            placeholder="Email or Staff ID">
                    </div>
                    @if ($errors->has('login'))
                        <span class="help-block" style="font-weight: 50 !important;">
                            <strong>{{ $errors->first('login') }}</strong>
                        </span>
                    @endif
                    <div class="form-group col-md-12 mb-4{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" name="password" id="password" class="form-control"
                        >
                    </div>
                    @if ($errors->has('password'))
                        <span class="help-block" style="font-weight: 50 !important;">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif

                    <button type="submit" class="btn btn-block login-btn mb-4">
                        Login
                    </button>
               
            </div>    
        </form>
        <a class="btn btn-link" href="{{ route('password.request') }}">
            Forgot Your Password?
        </a><br><br>
        <span>Designed by <a href="https://techdaves.com">Techdaves</a></span>
    </div>
</div>

@endsection
{{-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}