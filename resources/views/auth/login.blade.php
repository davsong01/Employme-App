@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Login
@endsection
@section('pagetitle')
@if(request()->prefix__ == '/admin')
Admin Login
@elseif(request()->prefix__ == '/company')
Company Admin Login
@else
Login
@endif
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
            <h4>
                @if(request()->prefix__ == '/admin')
                Admin Login
                @elseif(request()->prefix__ == '/company')
                Company Admin Login
                @else
                Login to start learning
                @endif
            </h4>
            @if(request()->prefix__ == '/admin')
            <form action="{{ route('admin.login.post') }}" method="POST" enctype="multipart/form-data">
            @elseif(request()->prefix__ == '/company')
            <form action="{{ route('company_user.login.post') }}" method="POST" enctype="multipart/form-data">
            @else
            <form action="{{ route('login') }}" method="POST" enctype="multipart/form-data">
            @endif
                @csrf
                <input type="hidden" name="coupon_id" value="{{  session()->get('data')['metadata']['coupon_id'] ?? null  }}">
                <div class="row">
                <div class="col-lg-12 col-md-12">
                    @if(request()->prefix__ != "")
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="checkout__input">
                                <p>Email<span>*</span></p>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="checkout__input">
                                <p>Email or Staff ID<span>*</span></p>
                                <input type="text" class="form-control" id="login" name="login" placeholder="Email or Staff ID required">
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="checkout__input">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <label for="password" style="margin-bottom: 0;">Password<span>*</span></label>
                                    <a href="{{ route('password.request') }}" style="font-size: 0.875rem;">Forgot Your Password?</a>
                                </div>
                                <div style="position: relative;">
                                    <input type="password" id="password" name="password" required style="padding-right: 40px;">
                                    <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        👁️
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="site-btn checkout-button">LOGIN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🙈'; 
    });
    </script>
    </form>
    </div>
</div>
</section>
@endsection