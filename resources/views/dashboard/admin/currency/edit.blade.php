@extends('dashboard.admin.index')
@section('title', 'Update Currency')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Update Currency</h4>
                    </div>
                    <form action="{{route('currency.update', $currency->id)}}" method="POST" class="pb-2">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ?? $currency->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="country_name">Country Name</label>
                                    <input type="text" class="form-control" name="country_name" value="{{ old('country_name') ?? $currency->country_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="conversion_rate">Conversion Rate</label>
                                    <input type="number" step="0.000001" class="form-control" name="conversion_rate" value="{{ old('conversion_rate')  ?? $currency->conversion_rate }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="symbol">Symbol</label>
                                    <input type="text" class="form-control" name="symbol" value="{{ old('symbol')  ?? $currency->symbol }}">
                                </div>
                                <div class="mb-3">
                                    <label for="symbol_native">Symbol Native</label>
                                    <input type="text" class="form-control" name="symbol_native" value="{{ old('symbol_native')  ?? $currency->symbol_native }}">
                                </div>
                            
                                
                                <div class="mb-3">
                                    <label for="status">status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="1" {{ $currency->status == '1' ? 'selected' : '' }}>Active</option> 
                                        <option value="0" {{ $currency->status == '0' ? 'selected' : '' }}>Inactive</option> 
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection