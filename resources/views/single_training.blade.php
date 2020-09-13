@extends('layouts.frontend')
@section('title', 'All Trainings')
@section('content')
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
                            <div class="carousel-item active">
                                <a class="gallery_img" href="{{ asset('ecommerce/img/product-img/pro-big-1.jpg') }}">
                                    <img class="d-block w-100" src="{{ asset('ecommerce/img/product-img/pro-big-1.jpg') }}" alt="First slide">
                                </a>
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
                        <p class="product-price">{{ config('custom.default_currency') }}{{ $training->p_amount }}</p>
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
                        <p class="product-price">{{ config('custom.default_currency') }}{{ $training->p_amount }}</p>
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
                                    <option value="{{ $training->p_amount * 100}}">Full Payment ({{ config('custom.default_currency') }}{{ $training->p_amount }})</option>
                                    @if($training->close_earlybird == 1)
                                     @if($training->e_amount > 0)<option value="{{ $training->e_amount * 100}}">Earlybird {{ config('custom.default_currency') }}{{ $training->e_amount }})</option>@endif
                                    @endif
                                    <option value="{{ ($training->p_amount/2) *100}}">Part Payment ({{ config('custom.default_currency') }}{{ $training->p_amount/2 }})</option>
                                </select>
                            </div>
                        </div>
                        @if ($errors->has('amount'))
                            <span class="help-block">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif

                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <select name="location" id="location" class="form-control">
                                    <option value="">Select Location (Optional)</option>
                                    <option value="Abuja">Abuja</option>
                                    <option value="Ibadan">Ibadan</option>
                                    <option value="Lagos">Lagos</option>
                                    <option value="PHC">Port Harcourt</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                        @if ($errors->has('location'))
                            <span class="help-block">
                                <strong>{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                       
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
                            </div>
                        </div>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Email" required>
                            </div>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Phone" required>
                            </div>
                        </div>
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                        
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="currency" value="{{  config('custom.curr_abbreviation') }}">
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
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Area End -->
@endsection  
    