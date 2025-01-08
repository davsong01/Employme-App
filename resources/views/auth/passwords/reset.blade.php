@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Reset Password
@endsection
@section('pagetitle')
Reset Password
@endsection
@section('content')
<section class="checkout spad" style="padding-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
        <div class="checkout__form">
                <h4>Please Enter a new password</h4>
                <form action="{{ route('password.reset.process') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Password<span>*</span></p>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Confirm Password<span>*</span></p>
                                        <input type="password-confirm" class="form-control" id="password" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="token" value={{ $token }}>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="site-btn checkout-button">Reset Password</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
    </div>
</section>
@endsection