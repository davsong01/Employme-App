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
                            @if ($discount->is_closed == 'no')
                            <div class="product__item__price">{{ $currency_symbol. number_format($exchange_rate * $discount->e_amount ) }}<span>{{ $currency_symbol. number_format($exchange_rate * $discount->p_amount) }}</span></div>
                            @else 
                            <div class="product__item__price" style="color:red">Closed Group Training</span></div>
                            @endif
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
                                @if ($training->is_closed == 'no')
                                    @if(($training->e_amount > 0 ) && $training->close_earlybird == 0 || $training->e_amount != 0)
                                        {{ $currency_symbol }}{{ number_format($exchange_rate*$training->e_amount) }}
                                        <span class="discount-color">&nbsp; {{ $currency_symbol }}<span class="linethrough discount-color">{{ number_format($exchange_rate * $training->p_amount) }}</span></span>
                                    @else
                                        {{ $currency_symbol }}{{ number_format($exchange_rate * $training->p_amount) }}
                                    @endif
                                @else
                                <span style="color:red">Closed Group Training</span>
                                @endif
                            </h5> 
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection