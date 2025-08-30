<?php
    $facilitator = \Session::get('facilitator_id');
    $price = $amount;
    $settings = \App\Models\Settings::first();
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
                                    value="{{ resolveAuthUser()->name }}"  
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
                                        value="{{ resolveAuthUser()->email }}"  
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
                                        value="{{ resolveAuthUser()->phone }}"
                                        @endauth

                                        @guest 
                                        value="{{ old('name') }}" placeholder="Phone number"  
                                        @endguest required>
                                </div>
                            </div>
                        </div>
                            
                        <div class="checkout__input__checkbox">
                            <label for="acc">
                                Agree to <a href="{{ !is_null(\App\Models\Settings::first()->value('tac_link')) ? \App\Models\Settings::first()->value('tac_link') : '#'}}">terms and conditions?</a> 
                                <input type="checkbox" id="acc" required checked>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        @if(isset($type) && $type == 'full')
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
                        
                        @endif
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="checkout__order border rounded-3 p-4 shadow-sm bg-white">
                            {{-- ===== Title ===== --}}
                            <h5 class="fw-semibold text-primary mb-4">Order Summary</h5>
                    
                            {{-- ===== Order Table ===== --}}
                            <table class="table table-sm align-middle mb-4">
                                <tbody>
                                    {{-- Payment type on one neat line --}}
                                    <tr class="border-0">
                                        <th class="text-muted w-35">Payment Type:</th>
                                        <td class="fw-medium">{{ ucfirst($type) }} Payment</td>
                                    </tr>
                                    @if($isPackage && isset($training['programs']))
                                    <tr class="border-0">
                                        <th class="text-muted w-35">Package Name:</th>
                                        <td class="fw-medium">{{ ucfirst($training['p_name']) }} </td>
                                    </tr>
                                    @endif
                                    {{-- Trainings, numbered + light background --}}
                                    <tr class="border-0">
                                        <th class="text-muted">Training{{ $isPackage ? 's' : '' }}:</th>
                                        @if ($isPackage && isset($training['programs']))
                                        <td class="bg-light rounded p-2" style="padding: 5px 20px !important;font-size: 14px;color: black;">
                                            <div class="bg-light rounded p-2">
                                                    <ol class="mb-0 ps-3">
                                                        @foreach ($training['programs'] as $child)
                                                            <li>{{ $child['p_name'] }}</li>
                                                        @endforeach
                                                    </ol>
                                            </div>
                                            
                                        </td>
                                        @else
                                        <td>
                                            {{ $training['p_name'] }}
                                        </td>
                                        @endif
                                    </tr>
                    
                                    {{-- Sub‑total --}}
                                    <tr class="border-0">
                                        <th class="text-muted">Subtotal:</th>
                                        <td>
                                            {{ $currency_symbol . number_format($amount) }}
                                            @if ($type === 'earlybird')
                                                {!! getAmountExtraCurrencies($trainingObject ?? [], $type, $trainingObject->e_amount, 'yes')['string'] !!}
                                            @else
                                                {!! getAmountExtraCurrencies($trainingObject ?? [], $type, $trainingObject->p_amount)['string'] !!}
                                            @endif
                                        </td>
                                    </tr>
                    
                                    {{-- Coupon (hidden by default) --}}
                                    <tr id="show-coupon" class="border-0 d-none">
                                        <th class="text-muted">Coupon Applied:</th>
                                        <td>{{ $currency_symbol }}<span id="coupon_amount"></span></td>
                                    </tr>
                    
                                    {{-- Grand Total --}}
                                    <tr class="border-top fw-semibold">
                                        <th class="text-dark pt-2">Total:</th>
                                        <td class="pt-2">
                                            {{ $currency_symbol }}
                                            <span id="total">
                                                {{ number_format($amount) }}
                                                @if ($type === 'earlybird')
                                                    {!! getAmountExtraCurrencies($trainingObject ?? [], $type, $trainingObject->e_amount, 'yes')['string'] !!}
                                                @else
                                                    {!! getAmountExtraCurrencies($trainingObject ?? [], $type, $trainingObject->p_amount)['string'] !!}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            {{-- ===== Hidden Inputs ===== --}}
                            <input type="hidden" name="modes"             value="{{ $modes }}">
                            @if($isPackage && isset($training['programs']))
                            <input type="hidden" name="programs" value="{{ json_encode(array_column($training['programs'], 'id')) }}">
                            @else 
                            <input type="hidden" name="programs" value="{{ json_encode([$training['id']]) }}">
                            @endif
                            <input type="hidden" name="location"          value="{{ $location }}">
                            <input type="hidden" name="preferred_timing"  value="{{ $preferred_timing }}">
                            <input type="hidden" name="orderID"           value="{{ $training['id'] }}">
                            <input type="hidden" name="quantity"          value="1">
                            <input type="hidden" id="amount" class="total" name="amount" value="{{ $amount }}">
                            <input type="hidden" name="currency"          value="{{ $currency }}">
                            <input type="hidden" name="metadata"
                                   value="{{ json_encode([
                                       'pid'        => $training['id'],
                                       'facilitator'=> $facilitator,
                                       'coupon_id'  => $coupon_id ?? null,
                                       'type'       => $type ?? null,
                                       'isPackage'     => $isPackage
                                   ]) }}">
                    
                            {{-- ===== Payment Methods ===== --}}
                            <div class="mt-4">
                                @if ($amount > 0)
                                    <h6 class="mb-3 fw-semibold">Choose Payment Method</h6>
                                    
                                    <div class="d-flex flex-wrap gap-3" style="gap: 15px;">
                                        @if (resolveAuthUser())
                                            <button type="button" class="btn btn-outline-dark pay-option" name="payment_mode" value="wallet">
                                                <i class="fa-solid fa-wallet me-2"></i> Pay from Balance
                                            </button>
                                        @endif
                            
                                        @if ($settings->allow_transfer_button === 'yes' || in_array($training['id'], [68]))
                                            <button type="submit" class="btn btn-outline-secondary pay-option" name="payment_mode" value="0">
                                                <i class="fa fa-bank me-2"></i> Bank Transfer
                                            </button>
                                        @endif
                            
                                        @if (!in_array($training['id'], [68]))
                                            @foreach ($payment_modes as $mode)
                                                @if ($mode->type === 'card')
                                                    <button type="submit" class="btn btn-outline-primary pay-option d-flex align-items-center" name="payment_mode" value="{{ $mode->id }}">
                                                        <i class="fa fa-credit-card me-2"></i> Pay with
                                                        <span class="ms-2 d-inline-block" style="width: 24px; height: 16px; background-image: url('{{ url('/paymentmodes/' . $mode->image) }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></span>
                                                    </button>
                                                @endif
                            
                                                @if ($mode->type === 'crypto')
                                                    <button type="submit" class="btn btn-outline-warning pay-option d-flex align-items-center" name="payment_mode" value="{{ $mode->id }}">
                                                        <i class="fa fa-bitcoin me-2"></i> Pay with
                                                        <span class="ms-2 d-inline-block" style="width: 24px; height: 16px; background-image: url('{{ url('/paymentmodes/' . $mode->image) }}'); background-size: contain; background-repeat: no-repeat; background-position: center;"></span>
                                                    </button>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                @else
                                    <button type="button" class="btn btn-primary mt-3 pay-option w-100" name="payment_mode" value="register">
                                        <i class="fa fa-hand-pointer-o me-2"></i> Complete Registration
                                    </button>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
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
        isPackage = "{{$isPackage ?? 0}}";
        payment_type = "{{$type ?? 0}}";
        amount =

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
                payment_type: payment_type,
                isPackage: isPackage
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
    