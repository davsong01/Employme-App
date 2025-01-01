@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Login
@endsection
@section('pagetitle')
@if(request()->prefix__ == '/admin')
Admin Login
@else
Company Admin Login
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
                @else
                Company Admin Login
                @endif
            </h4>
            @if(request()->prefix__ == '/admin')
            <form action="{{ route('admin.login.post') }}" method="POST" enctype="multipart/form-data">
            @else
            Company Admin Login
            <form action="{{ route('company_user.login.post') }}" method="POST" enctype="multipart/form-data">
            @endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="coupon_id" value="{{  session()->get('data')['metadata']['coupon_id'] ?? null  }}">
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
                                <div class="checkout__input">
                                    <p>Password<span>*</span></p>
                                    <input type="password" id="password" name="password" required>
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
            </form>
        </div>
    </div>
</section>
@endsection