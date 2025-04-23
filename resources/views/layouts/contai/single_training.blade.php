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
                        @if($training->is_closed == 'no')
                            @if(isset($modes) && count($modes) > 0)
                                {{ $modes['Online'] > $modes['Offline'] ? $currency_symbol.number_format($modes['Offline']) .'/'. $currency_symbol.number_format($modes['Online']) : $currency_symbol.number_format($modes['Online']) .'/'. $currency_symbol.number_format($modes['Offline'])}}
                                {{-- {{ number_format($modes['Online'] > $modes['Offline'] ? ) }} / {{ $currency_symbol }} {{ number_format($modes['Offline']) }} --}}
                            @else
                                @if($training->p_amount > 0)
                                {{ $currency_symbol }}{{ number_format($training->p_amount) }} {!! getAmountExtraCurrencies($training, null, $training->p_amount)['string'] !!}
                                @else
                                <span style="color:green">FREE TRAINING</span>
                                @endif
                            @endif
                        @else
                        <span style="color:red">Closed Group Training</span>

                        @endif
                        
                    </div>
                    @if(isset($training->description) && !empty($training->description))
                    <p>{{ $training->description }}</p>
                    @endif
                    
                    <div class="checkout__form">
                        <form action="{{ route('checkout') }}" method="POST">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                    @if(isset($modes) && count($modes) > 0)
                                        <div class="checkout__input">
                                            <p>Select Mode<span>*</span></p>
                                            <select name="modes" id="payment_modes" required>
                                                <option value="">Select</option>
                                                @foreach($modes as $mode=>$value)
                                                <option value="{{ $mode }}">{{ $mode }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(isset($locations) && count($locations) > 0 )
                                            <div class="checkout__input" id="locations_div" style="display:none">
                                                <p>Select location<span>*</span></p>
                                                <select name="location" id="locations" required>
                                                    <option value="">Select Location</option>
                                                    @foreach($locations as $location=>$value)
                                                        <option value="{{ $location }}" {{ old('location_name') == $location ? 'selected' : '' }}>
                                                            {{ $location }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> 
                                        @endif
                                        
                                        <div class="checkout__input" id="payment_types" style="display:none">
                                            <p>Select payment type<span>*</span></p>
                                            <div class="select-block">
                                                <select name="type" id="payments" required>
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div> 
                                        @if(isset($training->allow_preferred_timing) && $training->allow_preferred_timing == 'yes' )
                                            <div class="checkout__input">
                                                <p>Your preferred Off Diet month<span>*</span></p>
                                                <select name="preferred_timing" id="preferred_timing" required>
                                                    <option value="">Select...</option>
                                                    @foreach($training->programRange() as $preferred_timing)
                                                        <option value="{{ $preferred_timing }}" {{ old('preferred_timing') == $preferred_timing ? 'selected' : '' }}>
                                                        {{ $preferred_timing }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> 
                                        @endif
                                    @else 
                                        @if(isset($locations) && count($locations) > 0 )
                                            <div class="checkout__input">
                                                <p>Select location<span>*</span></p>
                                                <select name="location" id="locations" required>
                                                    <option value="">Select Location</option>
                                                    @foreach($locations as $location=>$value)
                                                        <option value="{{ $location }}" {{ old('location_name') == $location ? 'selected' : '' }}>
                                                            {{ $location }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> 
                                        @endif
                                        
                                        @if($training->p_amount > 0)
                                        <div class="checkout__input">
                                            <p>Select payment type<span>*</span></p>
                                            <select name="type" id="" required>
                                                <option value="">Select</option>
                                                <option value="full" {{ old('amount') == $training->p_amount ? 'selected' : '' }}>Full Payment ({{ $currency_symbol.number_format($training->p_amount) }} {!! getAmountExtraCurrencies($training)['string'] !!})</option>
                                                
                                                @if($training->haspartpayment == 1)
                                                <option value="part" {{ old('amount') == ($training->p_amount / 2) ? 'selected' : '' }}>
                                                    Part Payment ({{ $currency_symbol . number_format($training->p_amount / 2) }} {!! getAmountExtraCurrencies($training, 'part')['string'] !!})
                                                </option>
                                                @endif
                                            </select>
                                        </div>
                                        @else
                                        <input type="hidden" value="full" name="type">
                                        @endif
                                        
                                        @if(isset($training->allow_preferred_timing) && $training->allow_preferred_timing == 'yes' )
                                            <div class="checkout__input">
                                                <p>Your preferred Off Diet month<span>*</span></p>
                                                <select name="preferred_timing" id="preferred_timing" required>
                                                    <option value="">Select...</option>
                                                    @foreach($training->programRange() as $preferred_timing)
                                                        <option value="{{ $preferred_timing }}" {{ old('preferred_timing') == $preferred_timing ? 'selected' : '' }}>
                                                        {{ $preferred_timing }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div> 
                                        @endif
                                    @endif
                                    
                                </div>
                                <input type="hidden" name="training" value="{{ $training }}"> 
                                <input type="hidden" name="facilitator" value="{{ \Session::get('facilitator') }}"> 
                                <input type="hidden" name="facilitator_id" value="{{ \Session::get('facilitator_id') }}"> 
                                <input type="hidden" name="facilitator_name" value="{{ \Session::get('facilitator_name') }}"> 
                                <input type="hidden" name="facilitator_license" value="{{ \Session::get('facilitator_license') }}">

                                <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                            </div>
                            @if($training->is_closed == 'no')
                            @if($training->p_amount > 0)
                            <input class="primary-btn" type="submit" value="PROCEED TO CHECKOUT">
                            @else 
                            <input class="primary-btn" type="submit" value="PROCEED TO REGISTER">
                            @endif
                            @endif
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

