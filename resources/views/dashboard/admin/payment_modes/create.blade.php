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
                    <form action="{{route('payment-modes.store')}}" method="POST" class="pb-2" enctype="multipart/form-data">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="card" {{ old('type') == 'card' ? 'selected' : '' }}>Card</option> 
                                        <option value="crypto" {{ old('type') == 'crypto' ? 'selected' : '' }}>Crypto</option> 
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="processor">Payment Processor</label>
                                    <select name="processor" id="processor" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="paystack" {{ old('processor') == 'paystack' ? 'selected' : '' }}>Paystack</option> 
                                        <option value="coinbase" {{ old('processor') == 'coinbase' ? 'selected' : '' }}>Coinbase</option> 
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Api Secret key</label>
                                    <input type="text" class="form-control" name="secret_key" value="{{ old('secret_key') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="public_key">Api Public key</label>
                                    <input type="text" class="form-control" name="public_key" value="{{ old('public_key') }}">
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
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control" name="image" value="">
                                </div>
                                <div class="form-group">
                                    <label for="status">status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option> 
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option> 
                                    </select>
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