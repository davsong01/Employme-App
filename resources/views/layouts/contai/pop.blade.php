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
        
        <?php
            $data = session()->get('data');
            $extraCurrencies = $data['extraCurrencies']['array'] ?? [];
            $transaction = $data['transaction'] ?? null;
            $groups = $data['groups'] ?? $groups;
            
        ?>
        @if(session()->get('data'))
            <div class="checkout__form transfer">
                <div class="b_transfer" style="font-size: 20px;background: #040080;color: white;padding: 20px;">
                    Please pay &#8358;{{ number_format($transaction->amount) }} (or the appropriate amount in your local currency) into the appropraite account below: <br>
                    <?php $training_id = $transaction->program_id; ?>
                    <div id="nigeria" style="border-radius: 5px;background: #f2f2e8;color: black;padding: 15px;margin: 5px;">
                        <h4 style="">Nigeria (Naira Payment) - <span style="color:red">&#8358;{{ number_format($transaction->amount) }}</span> 

                        </h4>
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

                    {{-- @if($type == 'earlybird')
                    {!! getAmountExtraCurrencies($trainingObject ?? [], $type,$trainingObject->e_amount, 'yes',)['string'] !!}
                    @else
                    {!! getAmountExtraCurrencies($trainingObject ?? [], $type,$trainingObject->p_amount)['string'] !!}
                    @endif --}}

                    @if(isset($extraCurrencies['Ghana']))
                    <div id="ghana" style="border-radius: 5px;background: #ffff7e;color: black;padding: 15px;margin: 5px;">
                        <h4 style="">Ghana (Cedes Payment)
                            @if(isset($extraCurrencies['Ghana']))  - 
                                <span style="color:red">
                                    {{$extraCurrencies['Ghana']['symbol']}}{{$extraCurrencies['Ghana']['amount']}}
                                </span>
                            @endif
                        </h4>
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
                    @endif

                    @if(isset($extraCurrencies['Gambia']))
                    <div id="gambia" style="border-radius: 5px;background: #1edb05;color: black;padding: 15px;margin: 5px;">
                        <h4 style="">Gambia
                            @if(isset($extraCurrencies['Gambia']))  - 
                                <span style="color:red">
                                    {{ $extraCurrencies['Gambia']['symbol'] }}{{ $extraCurrencies['Gambia']['amount'] }}
                                </span>
                            @endif
                        </h4>
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
                    @endif

                    @if(isset($extraCurrencies['Benin Rep & Togo']))
                    <div id="gambia" style="border-radius: 5px;background: #c4f502;color: black;padding: 15px;margin: 5px;">
                        <h4 style="">Benin Rep & Togo
                            @if(isset($extraCurrencies['Benin Rep & Togo']))  - 
                                <span style="color:red">
                                    {{ $extraCurrencies['Benin Rep & Togo']['symbol'] }}{{ $extraCurrencies['Benin Rep & Togo']['amount'] }}
                                </span>
                            @endif
                        </h4>
                        @foreach($accounts as $account)
                            @if ($account['country'] == 'Benin Rep & Togo')
                                <div class="inner" style="margin-bottom: 15px;">
                                    <strong>Bank: </strong>{{$account['bank']}} <br>
                                    <strong>Account Number: </strong>{{$account['number']}} <br>
                                    <strong>Name: </strong>{{$account['name']}} <br>
                                </div>
                                <hr>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    @if(isset($extraCurrencies['Cameroon']))
                    <div id="gambia" style="border-radius: 5px;background: #3d5de9;color: black;padding: 15px;margin: 5px;">
                        <h4 style="">Cameroon
                            @if(isset($extraCurrencies['Cameroon']))  - 
                                <span style="color:red">
                                    {{ $extraCurrencies['Cameroon']['symbol'] }}{{ $extraCurrencies['Cameroon']['amount'] }}
                                </span>
                            @endif  
                        </h4>
                        @foreach($accounts as $account)
                            @if ($account['country'] == 'Cameroon')
                                <div class="inner" style="margin-bottom: 15px;">
                                    <strong>Bank: </strong>{{$account['bank']}} <br>
                                    <strong>Account Number: </strong>{{$account['number']}} <br>
                                    <strong>Name: </strong>{{$account['name']}} <br>
                                </div>
                                <hr>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    And then Upload your proof of payment using the form below
                </div>
            </div>
        @endif
        <div class="checkout__form">
            <h4>Upload Proof of Payment</h4>
            <form action="{{ route('store-proof-of-payment') }}" method="POST" enctype="multipart/form-data">
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
                                            value="{{ resolveAuthUser()->name }}"  
                                            placeholder="Full Name"
                                            @endauth
                                            @guest 
                                            value="{{ session()->get('data')['transaction']['name'] ?? old('name') }}" placeholder="Full Name"  
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
                                            value="{{ session()->get('data')['transaction']['email'] ?? old('email') }}" placeholder="Enter email"  
                                            @endguest required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <p>Whatsapp Number<span>*</span></p>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                            @auth

                                            value="{{ resolveAuthUser()->phone }}"  
                        
                                            @endauth

                                            @guest 
                                            value="{{ session()->get('data')['transaction']['phone'] ?? old('phone') }}"  
                                            @endguest required>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                {{-- Program Type --}}
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Program Type <span>*</span></p>
                                    <select name="program_type" id="program_type" class="form-control" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="package" {{ (!empty($transaction) && $transaction->is_package) ? 'selected' : '' }}>Package</option>
                                        <option value="training" {{ (empty($transaction) || !$transaction->is_package) ? 'selected' : '' }}>Training</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Package Section --}}
                            <div class="col-lg-12 program-section" id="package_section" style="display: none;">
                                <div class="checkout__input">
                                    <p>Select Package <span>*</span></p>
                                    <select name="package_id" id="package_id" class="form-control">
                                        <option value="">-- Select --</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}"
                                                {{ (!empty($transaction) && $transaction->program_id == $group->id) ? 'selected' : '' }}>
                                                {{ $group->p_name }} | ({{ $currency . number_format($group->p_amount) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 program-section" id="training_section" style="display:none;">
                                <div class="checkout__input">
                                    <p>Select Course <span>*</span></p>
                                    <select name="training_id" id="training_id" class="form-control">
                                        <option value="">-- Select --</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}" {{ ($transaction && $transaction->program_id == $training->id) ? 'selected' : '' }}>
                                                {{ $training->p_name }} | ({{ $currency . number_format($training->p_amount) }}
                                                @if(in_array($training->id, [68])), GHc 60, GMD 75 @endif)
                                            </option>
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
                                        @foreach($accounts as $account)
                                        <option value="{{$account['bank']}}">{{$account['bank']}}</option>
                                        @endforeach
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
                                    <input type="date" class="form-control" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" required>
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
@section('scripts')
<script>
$(document).ready(function () {
    function toggleProgramType() {
        let type = $('#program_type').val();
        $('.program-section').hide().find('select').prop('required', false);

        if (type === 'package') {
            $('#package_section').show().find('select').prop('required', true);
        } 
        else if (type === 'training') {
            $('#training_section').show().find('select').prop('required', true);
        }
    }

    // On load
    toggleProgramType();

    // On change
    $('#program_type').on('change', toggleProgramType);
});
</script>
@endsection