<?php
    $facilitator = \Session::get('facilitator_id');
    $price = $amount;
?>

@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Checkout
@endsection
@section('content')
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
        <div class="checkout__form">
            <h4>Billing Details</h4>
            <form action="{{ route('pay') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Name<span>*</span></p>
                                     <input type="text" class="form-control" id="name" name="name" 
                                        @auth
                                        value="{{ auth()->user()->name }}"  
                                        placeholder="Full Name"
                                        @endauth

                                        @guest 
                                        value="{{ old('name') }}" placeholder="Full Name"  
                                        @endguest required>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Email<span>*</span></p>
                                    <input type="email" id="email" name="email" 
                                        @auth
                                        value="{{ auth()->user()->email }}"  
                                        @endauth
                                        @guest 
                                        value="{{ old('email') }}" placeholder="Enter email"  
                                        @endguest required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Phone<span>*</span></p>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                        @auth
                                        value="{{ auth()->user()->phone }}"  
                                        
                                        @endauth

                                        @guest 
                                        value="{{ old('name') }}" placeholder="Phone number"  
                                        @endguest required>
                                </div>
                            </div>
                        </div>
                            
                        <div class="checkout__input__checkbox">
                            <label for="acc">
                                Agree to <a href="{{ !is_null(\App\Settings::first()->value('tac_link')) ? \App\Settings::first()->value('tac_link') : '#'}}">terms and conditions?</a> 
                                <input type="checkbox" id="acc" required checked>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                         <div class="row">
                           <div class="col-lg-12">
                                <h6 style="margin-bottom: 10px !important;"><span class="icon_tag_alt"></span> Have a coupon? <b onclick="showCoupon()" style="text-decoration: underline; cursor: pointer;" >Click here</b> to enter your code
                                </h6>
                            </div>
                        </div>
                        <span style="color:red; display:none" id="enter-email">You must enter your email and coupon code</span>
                        <span style="color:green; display:none" id="coupon-applied"></span>
                       
                        <div class="row" id="coupon-field" style="display:none">
                            <div class="col-lg-6" style="padding-right:0px">
                                <div class="checkout__input">
                                    <input type="text" id="coupon" name="coupon" value="{{ old('coupon') }}">
                                </div>
                            </div>
                            <div class="col-lg-6" >
                                <div class="checkout__input">
                                <p id="validate-coupon" onclick="validateCoupon({{ old('coupon') }})" class="site-btn">Apply Coupon</p>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="checkout__order">
                            <div class="checkout__order__products">Order details</div>
                            <table style="width: 100%;">
                                <tr class="bor-bottom">
                                    <th class="col1">Course</th>
                                    <td class="col2">{{ $training['p_name'] }}</td>
                                </tr>
                                
                                <tr class="bor-bottom">
                                    <th class="col1">Sub total</th>
                                    <td class="col2">{{ $currency_symbol. number_format($amount) }}</td>
                                </tr>

                                <tr class="bor-bottom" id="show-coupon" style="display:none">
                                    <th class="col1">Coupon Applied</th>
                                    <td class="col2">{{ $currency_symbol }}<span id="coupon_amount"></span> </td>
                                </tr>
                                <tr class="bor-bottom">
                                    <th class="col1">Total</th>
                                    <td class="col2">{{ $currency_symbol}}<span id="total">{{ number_format($amount) }}</span> </td>
                                </tr>
                            </table>
                            
                            <input type="hidden" name="orderID" value="{{ $training['id'] }}">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" class="total" id="amount" name="amount" value="{{ ($amount) }}">
                            <input type="hidden" name="currency" value="{{  $currency }}">
                            <input type="hidden" name="metadata" value="{{ json_encode($array = ['pid' => $training['id'], 'facilitator' => $facilitator , 'coupon_id' => $coupon_id ?? NULL, 'type'=>$type ?? NULL]) }}" > 
                            
                            <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
                
                            <button type="submit" class="site-btn checkout-button">MAKE PAYMENT</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    function showCoupon(){       
        $('#coupon-field').toggle();
    }
   
    function validateCoupon(id){
        email = $('#email').val();
        code = $('#coupon').val();
        pid = "{{ $training['id'] }}";
        price = "{{ $amount }}";
       
        var total = $('#amount').val();
        var newTotal = 0;
        let dollarUSLocale = Intl.NumberFormat('en-US');

        if(email == '' || code == ''){
            return $('#enter-email').show();
        }else{
            $('#enter-email').hide()

            $.post("/validate-coupon", {
                email: email,
                price: price,
                code: code,
                pid: pid,
               
            },function(data, status){
                if(status == 'success'){
                    if(data.amount){
                        $('#coupon_amount').text(dollarUSLocale.format(parseFloat(data.amount)));
                        $('#coupon-applied').text('Coupon: '+data.code+ ' with discount of '+"{{ $currency }}"+dollarUSLocale.format(parseFloat(data.amount))+' successfully applied');
                        $('#coupon-applied').css("color", "green");
                        $('#enter-email').hide()
                       
                        $('#amount').val(data.grand_total);
                        $('#coupon').val(code);
                        
                        $('#total').text(dollarUSLocale.format(parseFloat(data.grand_total)));

                        $('#coupon-applied').show();
                        $('#total').show();
                        $('#show-coupon').show()
                    }else{
                        $('#coupon').val("");
                        $('#coupon-applied').text('Coupon does not exist or you have used it');
                        $('#coupon-applied').css("color", "red");
                        $('#enter-email').hide()
                        $('#coupon-applied').show()
                        $('#show-coupon').hide()
                        $('#total').text(dollarUSLocale.format(parseFloat(price)));
                        $('#amount').val(price);

                    }
                    
                }
            });
        }
	}    
</script>
@endsection
    