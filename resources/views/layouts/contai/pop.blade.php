<?php 
    $accounts = [
        [
            'bank' =>  'Access Bank',
            'number' => '0106070151',
            'name' => 'Employme E-learning',
            'status' => 1
        ],  
        [
            'bank' =>  'Mobile Money (MoMo)',
            'number' => '0244627751',
            'name' => '3Y Publicity',
            'status' => 1
        ],  
];
?>
@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Upload POP
@endsection
@section('content')
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
        @if(session()->get('data'))
        <div class="checkout__form transfer">
            <div class="b_transfer" style="font-size: 20px;background: #040080;color: white;padding: 20px;">Please pay &#8358;{{ session()->get('data')['amount'] }} into any of the account details below: <br>
                @foreach($accounts as $account)
                    <div class="inner" style="margin-bottom: 15px;">
                        <strong>Bank:</strong>{{$account['bank']}} <br>
                        <strong>Account Number:</strong>{{$account['number']}} <br>
                        <strong>Name:</strong>{{$account['name']}} <br>
                    </div>
                @endforeach
                And then Upload your proof of payment using the form below
            </div>
        </div>
        @endif
        <div class="checkout__form">
            <h4>Upload Proof of Payment</h4>
            <form action="{{ route('pop.store') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="coupon_id" value="{{  session()->get('data')['metadata']['coupon_id'] ?? null  }}">
                <div class="row">
                    
                    <div class="col-lg-12 col-md-12">
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
                                        value="{{ session()->get('data')['name'] ?? old('name') }}" placeholder="Full Name"  
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
                                        value="{{ session()->get('data')['email'] ?? old('email') }}" placeholder="Enter email"  
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
                                        value="{{ session()->get('data')['phone'] ?? old('phone') }}"  
                                        @endguest required>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Select Course<span>*</span></p>
                                    <select name="training" id="training" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        @foreach($trainings as $training)

                                        <option value="{{ $training->id }}" {{ (isset(session()->get('data')['metadata']['pid']) && session()->get('data')['metadata']['pid'] == $training->id) ? 'selected' : ''  }}>{{ $training->p_name }} | ({{ $currency . number_format($training->p_amount) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Bank paid into<span>*</span></p>
                                    <select name="bank" id="bank" class="form-control">
                                        <option value="">-- Select bank --</option>
                                        <option value="Access">Access</option>
                                        <option value="GTB">GTB</option>
                                        <option value="Mobile Money (MoMo)">Mobile Money (MoMo)</option>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Amount<span>*</span></p>
                                   
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ session()->get('data')['amount'] ??  old('amount') }}" min=1 required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Upload proof of payment<span>*</span></p>
                                    <input type="file" class="form-control" name="file" id="file" value="{{ old('file') }}" required>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Date of payment<span>*</span></p>
                                    
                                    <input type="date" class="form-control" name="date" id="date" value="{{ date('Y/m/d') ?? old('date')}}" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        <input type="hidden" name="currency_symbol" value="{{ $currency_symbol }}">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="site-btn checkout-button">UPLOAD</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection