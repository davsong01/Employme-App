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
                        @if(isset($modes) && count($modes) > 0)
                            {{ $modes['Online'] > $modes['Offline'] ? $currency_symbol.number_format($modes['Offline']) .'/'. $currency_symbol.number_format($modes['Online']) : $currency_symbol.number_format($modes['Online']) .'/'. $currency_symbol.number_format($modes['Offline'])}}
                        @else
                            @if(($training->e_amount > 0 ) && $training->close_earlybird == 0 || $training->e_amount != 0)
                                {{ $currency_symbol }}{{ number_format($training->e_amount) }}
                                <span class="discount-color">&nbsp; {{ $currency_symbol }}<span class="linethrough discount-color">{{ number_format($training->p_amount) }}</span></span>
                            @else
                                @if(!empty($training->price_range))
                                    From {{ $currency_symbol.number_format($exchange_rate * $training->price_range['from']) }} to {{ $currency_symbol.number_format($exchange_rate * $training->price_range['to']) }}
                                @else
                                    {{ $currency_symbol }}{{ number_format($exchange_rate * $training->p_amount) }}
                                @endif
                            @endif
                        @endif
                    </div>
                    @if(isset($training->description) && !empty($training->description))
                    <p>{{ $training->description }}</p>
                    @endif
                    <div class="checkout__form">
                        <form action="{{ route('trainings') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                    @if(isset($training->subPrograms) && $training->subPrograms->count() > 0)
                                        <div class="checkout__input">
                                            <p>Select Variation<span>*</span></p>
                                            <select name="training" id="training" class="training-select" style="font-size: 12px !important" required>
                                                <option value="">Select</option>
                                                @foreach($training->subPrograms->sortBy('p_amount') as $tran)
                                                <option value="{{ $tran->id }}">{{ $tran->p_name }} ({{$currency_symbol.number_format($exchange_rate * $tran->p_amount)}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                                {{-- <input type="hidden" name="training" value="{{ $training }}">  --}}
                                {{-- <input type="hidden" name="bypassChildren" value="1"> 
                                <input type="hidden" name="facilitator" value="{{ \Session::get('facilitator') }}"> 
                                <input type="hidden" name="facilitator_id" value="{{ \Session::get('facilitator_id') }}"> 
                                <input type="hidden" name="facilitator_name" value="{{ \Session::get('facilitator_name') }}"> 
                                <input type="hidden" name="facilitator_license" value="{{ \Session::get('facilitator_license') }}"> --}}
                                
                                {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">  --}}
                            </div>
                            <input class="primary-btn" style="background: #dfa802; !important" type="submit" value="PROCEED">
                        </form>
                    </div>                        
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('payments').niceSelect();
        $("#payment_modes").on('change', function () {
            if(this.value){
                if(this.value == 'Offline'){
                    getPayment(this.value, {{ $training->id }});
                    $("#locations_div").show();
                    $("#locations").attr('required',true);
                    $("#payment_types").show();
                }else{
                    getPayment(this.value, {{ $training->id }});
                    $("#locations").attr('required',false);
                    $("#locations_div").hide();
                    $("#payment_types").show();
                }
            }else{
                $("#payment_types").hide();
                $("#locations_div").hide();
            }
        });

        function getPayment(payment_mode, training){
            // $("#payments").prepend(data.data);
            $('#payments').empty();
            
            $.post("/get-mode-payment-types", {
                payment_mode: payment_mode,
                training: training,
                currency_symbol : "{{ $currency_symbol }}",
            },function(data, status){
                if(status == 'success'){
                    $("#payments").append(data.data);
                    $('#payments').niceSelect('update'); //destroy the plugin 
                    $('#payments').niceSelect(); //apply again
                }
            });
        }
    });

    
</script>
@endsection

