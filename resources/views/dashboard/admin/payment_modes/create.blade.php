@extends('dashboard.admin.index')
@section('title', 'Add Payment mode')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Add new Payment mode</h4>
                    </div>
                    <form action="{{route('payment-modes.store')}}" method="POST" class="pb-2">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="processor">Payment processor</label>
                                    <select name="processor" id="processor" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="paystack">Paystack</option>                                        
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug(Must be unique, preferably country name)</label>
                                    <input type="text" class="form-control" name="slug" value="{{ old('slug') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="public_key">Public key</label>
                                    <input type="text" class="form-control" name="public_key" value="{{ old('public_key') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Secret key</label>
                                    <input type="text" class="form-control" name="secret_key" value="{{ old('secret_key') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="merchant_email">Merchant email</label>
                                    <input type="email" class="form-control" name="merchant_email" value="{{ old('merchant_email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="currency">Currency code ( e.g. {{ $currency }})</label>
                                    <input type="text" class="form-control" name="currency" value="{{ old('currency') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="currency">Currency symbol ( e.g. {{ $currency_symbol }})</label>
                                    <input type="text" class="form-control" name="currency_symbol" value="{{ old('currency_symbol') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="exchange_rate">Exchange rate <b style="color:blue">( 1{{ $currency }} is ? )</b></label>
                                    <input type="number" class="form-control" name="exchange_rate" value="{{ old('exchange_rate') }}" step=".01" min="0.1" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                        </div>

                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
    @endsection