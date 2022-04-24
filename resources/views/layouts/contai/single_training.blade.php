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
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
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
                                        <p>Select payment type<span>*</span></p>
                                        <select name="type" id="" required>
                                            <option value="">Select</option>
                                            <option value="full" {{ old('amount') == $training->p_amount ? 'selected' : '' }}>Full Payment ({{ $currency.number_format($training->p_amount) }})</option>
                                            @if($training->close_earlybird == 0 || $training->e_amount > 0)
                                            <option value="earlybird" {{ old('amount') == $training->e_amount ? 'selected' : '' }}>Earlybird ({{ $currency.number_format($training->e_amount) }})</option>
                                            @endif
                                            @if($training->haspartpayment == 1)
                                            <option value="part" {{ old('amount') == ($training->p_amount/2) ? 'selected' : '' }}>Part Payment ({{ $currency.number_format($training->p_amount/2) }})</option>
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
