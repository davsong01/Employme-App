@extends('dashboard.admin.index')
@section('title', 'Update coupon')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Coupon</h4>
                    </div>
                    <form action="{{ route('coupon.update', $coupon->id) }}" method="POST" class="pb-2">
                        {{ csrf_field() }}
                         {{ method_field('PATCH') }}
                        <div class="form-group">

                            <label for="class">Select Training *</label>

                            <select name="program_id" id="program_id" class="form-control" required>
                                <option value="">-- Select Training --</option>
                                @foreach ($programs as $program)
                                <option value="{{ $program->id }}" {{ $program->id == $coupon->program_id ? 'selected':'' }}>{{$program->p_name}} | <strong>{{ \App\Settings::value('DEFAULT_CURRENCY') . number_format($program->p_amount) }}</strong></option>
                                @endforeach
                            </select>
                           
                        </div>
                        <div class="form-group">
                            <label for="code">Coupon Code</label>
                            <input id="code" type="text" class="form-control" name="code"
                                value="{{ old('code') ?? $coupon->code }}" required>
                        </div>
                        <div class="form-group">
                            <label for="code">Coupon Amount</label>
                            <input id="amount" type="number" class="form-control" name="amount"
                                value="{{ old('amount') ?? $coupon->amount }}" required>
                        </div>
                        <input type="submit" value="Update" class="btn btn-primary" style="width:100%">
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection