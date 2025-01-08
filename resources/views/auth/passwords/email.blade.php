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
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @else
            <div class="checkout__form">
                <h4>Enter your email address and we will send you a Password reset link</h4>
                <form action="{{ route('password.email') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Email<span>*</span></p>
                                        <input type="text" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="site-btn checkout-button">Send Password Reset Link</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</section>
@endsection