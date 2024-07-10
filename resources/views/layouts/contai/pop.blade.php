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
            <div class="b_transfer" style="font-size: 20px;background: #040080;color: white;padding: 20px;">Please pay &#8358;{{ number_format(session()->get('data')['amount']) }} (or its equivalent in your local currency) into an account below: <br>
                <div id="nigeria" style="border-radius: 5px;background: #f2f2e8;color: black;padding: 15px;margin: 5px;">
                    <h4 style="">Nigeria (Naira Payment)</h4>
                    @foreach($accounts as $account)
                        @if ($account['country'] == 'Nigeria')
                            <div class="inner" style="margin-bottom: 15px;">
                                <strong>Bank: </strong>{{$account['bank']}} <br>
                                <strong>Account Number: </strong>{{$account['number']}} <br>
                                <strong>Name: </strong>{{$account['name']}} <br>
                            </div> 
                            <hr>
                        @endif
                    @endforeach
                </div>
                <div id="ghana" style="border-radius: 5px;background: #ffff7e;color: black;padding: 15px;margin: 5px;">
                    <h4 style="">Ghana (Cedes Payment)</h4>
                    @foreach($accounts as $account)
                        @if ($account['country'] == 'Ghana')
                            <div class="inner" style="margin-bottom: 15px;">
                                <strong>Bank: </strong>{{$account['bank']}} <br>
                                <strong>Account Number: </strong>{{$account['number']}} <br>
                                <strong>Name: </strong>{{$account['name']}} <br>
                            </div>
                            <hr>

                        @endif
                    @endforeach
                </div>
                <div id="gambia" style="border-radius: 5px;background: #1edb05;color: black;padding: 15px;margin: 5px;">
                    <h4 style="">Gambia</h4>
                    @foreach($accounts as $account)
                        @if ($account['country'] == 'Gambia')
                            <div class="inner" style="margin-bottom: 15px;">
                                <strong>Bank: </strong>{{$account['bank']}} <br>
                                <strong>Account Number: </strong>{{$account['number']}} <br>
                                <strong>Name: </strong>{{$account['name']}} <br>
                            </div>
                            <hr>
                        @endif
                    @endforeach
                </div>
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
                                        <option value="{{ $training->id }}" {{ (isset(session()->get('data')['metadata']['pid']) && session()->get('data')['metadata']['pid'] == $training->id) ? 'selected' : ''  }}>{{ $training->p_name }} | ({{ $currency . number_format($training->p_amount) }} @if(in_array($training->id, [68])), GHc 60, GMD 75
                                    @endif)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Bank paid into<span>*</span></p>
                                    <select name="bank" id="bank" class="form-control" required>
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
                                    <p>Amount (Enter integers only)<span>*</span></p>
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ session()->get('data')['amount'] ??  old('amount') }}" min=1 required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Upload proof of payment<span>*</span> (Image files only)</p>
                                    <input type="file" class="form-control" name="file" id="file" value="{{ old('file') }}" required accept="image/png,image/jpeg">
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