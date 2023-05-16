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
                        <h4>Invoice Id: {{$transaction->invoice_id}}</h4>
                        <h6>Transaction Id: {{ $transaction->transid }}</h6>
                    </div>
                    <form action="{{route('payments.update', ['id' => $transaction->id, 'program_amount' => $transaction->p_amount])}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label for="name">Name of Participant:  {{ $transaction->name }}</label> <br>

                                    <label>Bank: {{ $transaction->t_type }} </label> <br>
                                    <label for="transaction_id">Program Amount: {{ \App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->p_amount) }}</label> <br>
                                    <label for="transaction_id">Paid: {{ \App\Settings::value('DEFAULT_CURRENCY').number_format($transaction->t_amount) }}</label> <br> <br>

                                    <label>Balance: <span style="color:{{ $transaction->balance > 0 ? 'red' : 'green'}}">{{ \App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->balance) }}</span> </label> <br>

                                </div>
                                
                                <div class="form-group">
                                    <label>New Amount</label><span></span>
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