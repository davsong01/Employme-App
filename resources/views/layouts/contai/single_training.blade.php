<?php
    $currency = \Request::get('currency');
?>

@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - {{ $training->p_name }}
@endsection
@section('content')
 <!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__item">
                        <img class="product__details__pic__item--large"
                            src="{{ '/'.$training->image }}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="product__details__text">
                    <h3>{{ $training->p_name }}</h3>
                    
                    <div class="product__details__price">
                        @if($training->close_earlybird == 0 || $training->e_amount != 0)
                            {{ $currency }}{{ number_format($training->e_amount) }}
                            <span class="discount-color">&nbsp; {{ $currency }}<span class="linethrough discount-color">{{ number_format($training->p_amount) }}</span></span>
                        @else
                            {{ $currency }}{{ number_format($training->p_amount) }}
                        @endif
                    </div>
                    <p>{{ $training->description }}</p>
                    <div class="checkout__form">
                        <form action="{{ route('checkout') }}" method="POST">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                   
                                    <div class="checkout__input">
                                        <p>Select paymenet type<span>*</span></p>
                                        <select name="amount" id="" required>
                                            <option value="">Select</option>
                                            <option value="{{ $training->p_amount}}" {{ old('amount') == $training->p_amount ? 'selected' : '' }}>Full Payment ({{ $currency.number_format($training->p_amount) }})</option>
                                            @if($training->close_earlybird == 0 || $training->e_amount > 0)
                                            <option value="{{ $training->e_amount}}" {{ old('amount') == $training->e_amount ? 'selected' : '' }}>Earlybird ({{ $currency.number_format($training->e_amount) }})</option>
                                            @endif
                                            @if($training->haspartpayment == 1)
                                            <option value="{{ $training->p_amount/2}}" {{ old('amount') == ($training->p_amount/2) ? 'selected' : '' }}>Part Payment ({{ $currency.number_format($training->p_amount/2) }})</option>
                                            @endif
                                        </select>
                                        
                                    </div>
                                    @if($locations->count() > 0)
                                    <div class="checkout__input">
                                        <p>Select paymenet type<span>*</span></p>
                                        <select name="location" id="location" required>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}" {{ old('location') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                    </div>  
                                    @endif

                                </div>
                                <input type="hidden" name="training" value="{{ $training }}"> 
                                <input type="hidden" name="facilitator" value="{{ \Session::get('facilitator') }}"> 
                                <input type="hidden" name="facilitator_id" value="{{ \Session::get('facilitator_id') }}"> 
                                <input type="hidden" name="facilitator_name" value="{{ \Session::get('facilitator_name') }}"> 
                                <input type="hidden" name="facilitator_license" value="{{ \Session::get('facilitator_license') }}">

                                <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                            </div>
                            <input class="primary-btn" type="submit" value="PROCEED TO CHECKOUT">
                        </form>
                    </div>                        
                </div>
            </div>
            
        </div>
    </div>
    <script>
        $code = 'eeee';
        $.ajax({
            url: 'validate-coupon/',
            method: 'post',
            dataType: 'json',
            contentType: false,
            processData: false,

            headers: {
                'X-CSRF-TOKEN': token.val()
            },

            data: formData,

            error: function (data) {

                if (data.status === 422) {

                    name_error.html(data.responseJSON.name);
                    link_error.html(data.responseJSON.link);
                    image_error.html(data.responseJSON.image);

                } else {

                    alert('success');
                }
            }
    </script>
</section>

<!-- Product Details Section End -->

<!-- Related Product Section Begin -->
{{-- <section class="related-product">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title related__product__title">
                    <h2>Related Courses</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__discount__item">
                    <a href="https://www.thewaacsp.com/english" target="_blank">
                        <div class="product__discount__item__pic set-bg"
                            data-setbg="img/product/discount/pd-2.jpg">
                            <div class="product__discount__percent single-course-discount-percentage">-20%</div>
                        </div>
                    </a>
                    <div class="product__discount__item__text">
                        <a href="#">
                            <h5 style="color: #c2c2c2">1st diet 2022 ECOWAS-WAACSP Certified customer service
                                professionals [CCSP] Program</h5>
                        </a>
                        <div class="product__item__price">$30.00 <span>$36.00</span></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__discount__item">
                        <a href="https://www.thewaacsp.com/english" target="_blank">
                            <div class="product__discount__item__pic set-bg"
                                data-setbg="img/product/discount/pd-2.jpg">
                                <div class="product__discount__percent">-20%</div>
                            </div>
                        </a>
                        <div class="product__discount__item__text">
                            <a href="#">
                                <h5 style="color: #c2c2c2">1st diet 2022 ECOWAS-WAACSP Certified customer service
                                    professionals [CCSP] Program</h5>
                            </a>
                            <div class="product__item__price">$30.00 <span>$36.00</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="img/product/product-3.jpg">
                        <ul class="product__item__pic__hover">
                            <li><a href="#"><i class="fa fa-heart"></i></a></li>
                            <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                            <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="#">Crab Pool Security</a></h6>
                        <h5>$30.00</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic set-bg" data-setbg="img/product/product-7.jpg">
                        <ul class="product__item__pic__hover">
                            <li><a href="#"><i class="fa fa-heart"></i></a></li>
                            <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                            <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="#">Crab Pool Security</a></h6>
                        <h5>$30.00</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}
<!-- Related Product Section End -->

@endsection

@section('titlee', 'All Trainings')
@section('conteent')
<!-- Product Details Area Start -->
<div class="single-product-area section-padding-100 clearfix">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    @include('layouts.partials.alerts')
                    <ol class="breadcrumb" style="margin-top: 0px;">
                        <li class="breadcrumb-item"><a href="/">All Trainings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $training->p_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-7">
                <div class="" style="margin-bottom: 20px;">
                    <div id="product_details_slider" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="">
                                <img src="{{ '/'.$training->image }}" alt="Training image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="single_product_desc">
                    @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                     <div class="product-meta-data">
                        <div class="line"></div>
                        <p class="product-price">{{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount) }}</p>
                        <a>
                            <h6 style="font-size: 22px !important;">{{ $training->p_name }}</h6>
                        </a>
                       <p style="color:red">SORRY! Registration for this program has closed</p>
                    <div>
                        <a href="{{ url('/') }}"><button type="submit" class="btn btn-block login-btn mb-4">VIEW OTHER TRAININGS</button></a>
                    </div>
                    </div>
                    @else
                    <!-- Product Meta Data -->
                    <div class="product-meta-data">
                        <div class="line"></div>
                        <p class="product-price">{{\App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY'). number_format($training->p_amount) }}</p>
                        <a>
                            <h6 style="font-size: 22px !important;">{{ $training->p_name }}</h6>
                        </a>
                        
                    </div>
                    <div><p>Please Enter your details below and make payment</p></div>
                    <!-- Add to Cart Form -->
                    <form class="cart clearfix" action="{{ route('pay') }}" method="post">
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <select name="amount" id="amount" class="form-control" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="{{ $training->p_amount * 100}}">Full Payment ({{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount) }})</option>
                                    @if($training->close_earlybird == 0 && $training->e_amount > 0)
                                    <option value="{{ $training->e_amount * 100}}">Earlybird ({{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->e_amount) }})</option>
                                    @endif
                                    @if($training->haspartpayment == 1)
                                    <option value="{{ ($training->p_amount/2) *100}}">Part Payment ({{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount/2) }})</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @if ($errors->has('amount'))
                            <span class="help-block">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif

                        @if($locations->count() <= 0)
                        <input type= 'hidden' name="location" id="location"
                        @else
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <select name="location" id="location" class="form-control">
                                    <option value="">Select Location (Optional)</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location') == $location->id ? 'selected' : '' }}>
                                            {{ $location->title }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- @endif --}}
                            </div>
                        </div>
                        @if ($errors->has('location'))
                            <span class="help-block">
                                <strong>{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control" id="name" name="name" 
                                @auth
                                value="{{ auth()->user()->name }}"  
                                placeholder="Full Name"
                                @endauth

                                @guest 
                                 value="{{ old('name') }}" placeholder="Full Name"  
                                 @endguest 
                                
                                required>
                            </div>
                        </div>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="email" name="email" class="form-control" id="email" 
                                @auth
                                value="{{ auth()->user()->email }}"  
                                placeholder="Email"
                                @endauth

                                @guest 
                                 value="{{ old('email') }}" placeholder="Email"  
                                 @endguest 
                                
                                required>
                            </div>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control" name="phone" id="phone" 
                                @auth
                                value="{{ auth()->user()->phone }}"  
                                placeholder="Phone Number"
                                @endauth

                                @guest 
                                 value="{{ old('phone') }}" placeholder="Phone Number"  
                                 @endguest 
                                
                                required>
                            </div>
                        </div>
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                        
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="currency" value="{{  \App\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION') }}">
                        {{-- <input type="hidden" name="metadata2" value="{{ json_encode($array = ['pid' => $training->id]) }}" >  --}}
                        
                        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
            
                        <input type="hidden" name="metadata" id="metadata">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}
                        <script>
                            function fetchMetaValues() { 
                              
                                let metadata = document.getElementById('metadata'); 
                               
                                let name = document.getElementById('name').value; 
                                let phone = document.getElementById('phone').value; 
                                let location = document.getElementById('location').value;
                               let fid = { 'name':name, 'phone': phone, 'location': location, 'pid': {{ $training->id }} }; 
                               metadata.value = JSON.stringify(fid); }
                               
                        </script>
                        <div>
                            <button type="submit" onclick="fetchMetaValues()" class="btn btn-block login-btn mb-4">Make Payment</button>
                        </div>  
                        <div>
                        <a href="{{ url('/') }}" style="background:green" class="btn btn-block login-btn mb-4">VIEW OTHER TRAININGS</a>
                    </div>                      
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Area End -->
@endsection  
    