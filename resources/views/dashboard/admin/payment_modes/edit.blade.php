@extends('dashboard.admin.index')
@section('title', 'Update payment mode')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Update payment mode</h4>
                    </div>
                    <form action="{{route('payment-modes.update', $paymentMode->id)}}" method="POST" class="pb-2" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="card" {{ $paymentMode->type == 'card' ? 'selected' : '' }}>Card</option> 
                                        <option value="crypto" {{ $paymentMode->type == 'crypto' ? 'selected' : '' }}>Crypto</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="processor">Payment Processor</label>
                                    <select name="processor" id="processor" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="paystack" {{ $paymentMode->processor == 'paystack' ? 'selected' : '' }}>Paystack</option> 
                                        <option value="coinbase" {{ $paymentMode->processor == 'coinbase' ? 'selected' : '' }}>Coinbase</option> 
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ?? $paymentMode->name }}" required>
                                </div>
                                 <div class="form-group">
                                    <label for="secret_key">Secret key</label>
                                    <input type="text" class="form-control" name="secret_key" value="{{ old('secret_key') ?? $paymentMode->secret_key }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="public_key">Public key</label>
                                    <input type="text" class="form-control" name="public_key" value="{{ old('public_key') ?? $paymentMode->public_key }}">
                                </div>
                               
                                <div class="form-group">
                                    <label for="merchant_email">Merchant email</label>
                                    <input type="email" class="form-control" name="merchant_email" value="{{ old('merchant_email') ?? $paymentMode->merchant_email }}" required>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">Currency code ( e.g. {{ $currency }})</label>
                                    <input type="text" class="form-control" name="currency" value="{{ old('currency') ?? $paymentMode->currency }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="currency">Currency symbol ( e.g. ₦)</label>
                                    <input type="text" class="form-control" name="currency_symbol" value="{{ old('currency_symbol') ?? $paymentMode->currency_symbol }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exchange_rate">Exchange rate <b style="color:blue">( 1{{ $currency }} is {{ $paymentMode->exchange_rate .' '.$paymentMode->currency}})</b></label>
                                    <input type="number" class="form-control" name="exchange_rate" value="{{ old('exchange_rate') ?? $paymentMode->exchange_rate }}" step=".01" min="0.1" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="active" {{ $paymentMode->status == 'active' ? 'selected' : '' }}>Active</option> 
                                        <option value="inactive" {{ $paymentMode->status == 'inactive' ? 'selected' : '' }}>Inactive</option> 
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="image">Replace Image 
                                            <img src="{{ url('/paymentmodes').'/'.$paymentMode->image }}" alt="image" class="rounded-circle" width="80" height="80" style="margin: auto;display: block;"> 
                                    </label>
                                    <input type="file" class="form-control" name="image" value="">
                                </div>
                                
                            </div>
                        </div>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">Update</button>
                        </div>

                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
    @endsection