@extends('dashboard.admin.index')
@section('Edit Transaction' )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Invoice ID: {{$transaction->invoice_id}}</h4>
                    </div>
                    <form action="{{route('payments.update', ['id' => $transaction->id, 'program_amount' => $transaction->p_amount])}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name of Participant:  {{ $transaction->name }}</label>
                                </div>
                                
                                 <div class="form-group">
                                    <label>Bank: {{ $transaction->t_type }} </label>
                                </div>                               
                                <div class="form-group{{ $errors->has('transaction_id') ? ' has-error' : '' }}">
                                    <label for="transaction_id">Transaction Id: {{ $transaction->transid }}</label>
                                </div>
                                <div class="form-group">
                                    <label for="transaction_id">Program Amount: {{ $transaction->p_amount }}</label>
                                </div>
                                
                                <div class="form-group">
                                    <label>Amount Paid *</label><span>Already paid: <strong>{{$transaction->t_amount}}</strong></span>
                                    <input type="number" name="amount" value="{{ old('amount') ?? 0 }}" class="form-control">
                                </div>
                                <!--Gives the first error for input name-->
                                <div><small style="color:red">{{ $errors->first('amount')}}</small></div>
                                <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                    <label for="location">Location </label>
                                    <input id="location" type="text" class="form-control" name="location"
                                        value="{{ old('location') ?? $transaction->t_location }}" autofocus>
                                    @if ($errors->has('location'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('location') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            <div class="row">
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    Submit
                                </button>
                            </div>
                        </div>
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection