@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }}
@endsection
@section('content')

<section class="">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>Search Results ({{ $trainings->count() }})</h2>
                </div>
            </div>
        </div>
        <div class="row featured__filter">
            @if($trainings->count() > 0)
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
                                <h6 style="min-height:60px">
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
                                            @if(!empty($training->price_range))
                                                From {{ $currency_symbol.number_format($exchange_rate * $training->price_range['from']) }} to {{ $currency_symbol.number_format($exchange_rate * $training->price_range['to']) }}
                                            @else
                                                {{ $currency_symbol }}{{ number_format($exchange_rate * $training->p_amount) }}
                                            @endif
                                        @endif
                                    @else
                                    <span style="color:red">Closed Group Training</span>
                                    @endif
                                </h5> 
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
            No results found
            @endif
        </div>
    </div>
</section>
<!-- Featured Section End -->
<section>
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