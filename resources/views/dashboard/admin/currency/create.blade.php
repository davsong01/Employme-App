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
                        <h4>Add new Currency</h4>
                    </div>
                    <form action="{{route('currency.store')}}" method="POST" class="pb-2">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="country_name">Country Name</label>
                                    <input type="text" class="form-control" name="country_name" value="{{ old('country_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="conversion_rate">Conversion Rate</label>
                                    <input type="number" step="0.01"  class="form-control" name="conversion_rate" value="{{ old('conversion_rate') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="symbol">Symbol</label>
                                    <input type="text" class="form-control" name="symbol" value="{{ old('symbol') }}">
                                </div>

                                <div class="form-group">
                                    <label for="symbol_native">Symbol Native</label>
                                    <input type="text" class="form-control" name="symbol_native" value="{{ old('symbol_native') }}">
                                </div>
                            
                                <div class="form-group">
                                    <label for="status">status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option> 
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option> 
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection