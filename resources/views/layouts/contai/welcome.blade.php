@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }}
@endsection
@section('content')

@if($discounts->count() > 0)
<!-- Earlybird rush -->
<section class="from-blog spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>EarlyBird Rush</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="product__discount__slider owl-carousel">
                @foreach($discounts as $discount)
                <div class="col-lg-4">
                    <div class="product__discount__item">
                        <a href="{{ route('trainings', $discount->id ) }}" target="_blank">
                            <div class="product__discount__item__pic set-bg"
                                data-setbg="{{ $discount->image }}">
                                <div class="product__discount__percent">{{ number_format((($discount->e_amount * 100)/$discount->p_amount) - 100, 0) }}%</div>
                            </div>
                        </a>
                        <div class="product__discount__item__text">
                            <a href="{{ route('trainings', $discount->id ) }}" target="_blank">
                                <h5 style="color: #c2c2c2">{{ $discount->p_name }}</h5>
                            </a>
                            <div class="product__item__price">{{ $currency_symbol. number_format($exchange_rate * $discount->e_amount ) }}<span>{{ $currency_symbol. number_format($exchange_rate * $discount->p_amount) }}</span></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- End of earlybird rush -->
@endif
<!-- Featured Section Begin -->
<section class="">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>All Courses</h2>
                </div>
            </div>
        </div>
        <div class="row featured__filter">
            @foreach($trainings as $training)
                <div class="col-lg-3 col-md-4 col-sm-6 mix oranges fresh-meat">
                    <div class="featured__item">
                        @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                        @else
                        <a href="{{ route('trainings', $training->id ) }}" target="_blank">   
                        @endif
                            <div class="featured__item__pic set-bg" data-setbg="{{ $training->image }}">
                                @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                                <ul class="featured__item__pic__hover">
                                    <li><a href="#" class="disabled-link">Registration closed!</a></li>
                                </ul> 
                                @endif
                            </div>
                        </a>
                        
                        <div class="featured__item__text">
                            <h6>
                                @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                                <a href="#" class="disabled-link">
                                <span class="mobile_closed" style="display:none">Registration closed!</span>
                                {{-- <span style="color:red">Registration closed <br></span> --}}
                                @else
                                <a href="{{ route('trainings', $training->id ) }}" target="_blank">  
                                @endif
                                {{ $training->p_name }}</a>
                            </h6>
                            <h5>
                                 @if($training->close_earlybird == 0 || $training->e_amount != 0)
                                    {{ $currency_symbol }}{{ number_format($exchange_rate*$training->e_amount) }}
                                    <span class="discount-color">&nbsp; {{ $currency_symbol }}<span class="linethrough discount-color">{{ number_format($exchange_rate * $training->p_amount) }}</span></span>
                                @else
                                    {{ $currency_symbol }}{{ number_format($exchange_rate * $training->p_amount) }}
                                @endif
                                
                            </h5> 
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Featured Section End -->
{{-- <section>
    <div class="container">
        <div class="row">
            <!-- <div class="row"> -->
            <div class="col-lg-12">
                <!-- <div class="col-lg-12"> -->
                 <div class="homepage-pagination">
                    {{  $trainings->appends($_GET)->links()  }}
                </div>
                    <!-- </div> -->
            </div>
        <!-- </div> -->
        </div>
    </div>
</section>
<!-- Banner Begin -->
<div class="banner">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="banner__pic">
                    <img src="{{ asset('contai/img/banner/banner-1.jpg')}}" alt="">
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="banner__pic">
                    <img src="{{ asset('contai/img/banner/banner-2.jpg')}}" alt="">
                </div>
            </div>
        </div>
    </div>
</div> --}}
<!-- Banner End -->

<!-- Blog Section Begin -->
{{-- <section class="from-blog spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title from-blog__title">
                    <h2>From WAACSP Blog</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic">
                        <img src="{{ asset('contai/img/blog/blog-1.jpg')}}" alt="">
                    </div>
                    <div class="blog__item__text">
                        <ul>
                            <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                        </ul>
                        <h5><a href="#">Cooking tips make cooking simple</a></h5>
                        <p>Sed quia non numquam modi tempora indunt ut labore et dolore magnam aliquam quaerat </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic">
                        <img src="{{ asset('contai/img/blog/blog-2.jpg')}}" alt="">
                    </div>
                    <div class="blog__item__text">
                        <ul>
                            <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                        </ul>
                        <h5><a href="#">6 ways to prepare breakfast for 30</a></h5>
                        <p>Sed quia non numquam modi tempora indunt ut labore et dolore magnam aliquam quaerat </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="blog__item">
                    <div class="blog__item__pic">
                        <img src="{{ asset('contai/img/blog/blog-3.jpg')}}" alt="">
                    </div>
                    <div class="blog__item__text">
                        <ul>
                            <li><i class="fa fa-calendar-o"></i> May 4,2019</li>
                        </ul>
                        <h5><a href="#">Visit the clean farm in the US</a></h5>
                        <p>Sed quia non numquam modi tempora indunt ut labore et dolore magnam aliquam quaerat </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}
<!-- Blog Section End -->
@endsection
{{-- @extends('layouts.frontend')
@section('title')
    {{ config('app.name') }}
@endsection
@section ('status')
class="active"
@endsection
@section('content')
<div class="products-catagories-area clearfix">
    <div class="amado-pro-catagory clearfix">
        @foreach($trainings as $training)
        <!-- Single Catagory -->
        <div class="single-products-catagory clearfix">
            <a href="{{ route('trainings', $training->id ) }}">
                <img src="{{ $training->image }}"
                    alt="program image">
                <!-- Hover Content -->
                
                
            </a>
            <div class="details">
                <p class="detailsp">{{ mb_strimwidth($training->p_name, 0, 46, "...") }}<br>
                {{ config('custom.default_currency') }}{{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount) }}
                    @if($training->close_earlybird == 1)
                        @if($training->e_amount > 0)<strong> |  Early Bird:</strong> {{ config('custom.default_currency') }}{{ $training->e_amount }}@endif
                    @endif
                <br>
                @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                    <strong class="closed" style="color:red">Registration Closed</strong>
                @else <br>
                @endif

                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection --}}