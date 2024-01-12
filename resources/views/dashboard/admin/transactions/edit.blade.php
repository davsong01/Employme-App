@extends('dashboard.admin.index')
@section('Edit Transaction' )
@section('css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
@endsection
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
                    <form action="{{route('payments.update', ['payment' => $transaction->id, 'program_amount' => $transaction->p_amount])}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label for="name">Name of Participant:  {{ $transaction->name }}</label> <br>
                                    <strong>Account Balance:  {{ \App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->user->account_balance) }}</strong> <br>

                                    <label>Bank: {{ $transaction->t_type }} </label> <br>
                                    <label for="transaction_id">Program Amount: {{ \App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->p_amount) }}</label> <br>
                                    <label for="transaction_id">Paid: {{ \App\Settings::value('DEFAULT_CURRENCY').number_format($transaction->t_amount) }} @if($transaction->paymentthreads->count() > 0)
                                   
                                        <a class="btn btn-info btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal{{$transaction->transid }}"><i class="fa fa-eye"></i>View Payment Trail</a>
                                    @endif</label> <br> <br>

                                    <label>Balance: <span style="color:{{ $transaction->balance > 0 ? 'red' : 'green'}}">{{ \App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->balance) }}</span> </label> <br>

                                </div>
                                
                                <div class="form-group">
                                    <label>New Amount</label><span></span>
                                    <input type="number" name="amount" value="{{ old('amount') ?? 0 }}" class="form-control">
                                </div>
                                <!--Gives the first error for input name-->
                                <div><small style="color:red">{{ $errors->first('amount')}}</small></div>
                               
                                <input type="hidden" name="training_mode" value="{{ $transaction->training_mode }}">
                                @if(isset($locations) && !empty($locations))
                                <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                    <label for="location">Location </label>
                                     <select  id="location" name="location" class="form-control">
                                        <option value=""></option>
                                        @foreach ($locations as $location => $value)
                                            <option value="{{ $location }}" {{ $location == $transaction->t_location ? 'selected' :''}}>{{$location}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('location'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('location') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                @endif
                               
                                @if(isset($coupons) && $coupons->count()>0)
                                    <div class="form-group{{ $errors->has('coupon_id') ? ' has-error' : '' }}">
                                        <label for="location">Coupon Used </label>
                                        <select  id="coupon_id" name="coupon_id" class="form-control">
                                            <option value="">Select..</option>
                                            @foreach ($coupons as $coupon)
                                                <option value="{{ $coupon->id }}" {{ $coupon->id == $transaction->coupon_id ? 'selected' :''}}>{{$coupon->code}}({{ number_format($coupon->amount) }})</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('coupon_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('coupon_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="funds-source">Funds Source </label>
                                     <select  id="funds-source" name="funds_source" class="form-control" required>
                                        <option value="offline" selected>Offline Payment</option>
                                        <option value="wallet">Wallet</option>
                                    </select>
                                </div>
                            </div>
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
<div class="modal fade" id="exampleModal{{$transaction->transid}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Payment Trail for {{ $transaction->transid }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            @foreach($transaction->paymentthreads as $thread)
            <div class="row">
                <div class="col-md-6">
                    Transaction Id <br>
                    <strong>{{ $thread->transaction_id}}</strong>
                </div>
                <div class="col-md-6">
                    Date <br>
                    <strong>{{ $thread->created_at->format('d/m/Y') }}</strong>
                </div>
                
                <div class="col-md-6">
                    Amount<br>
                    <strong>{{ number_format($thread->amount) }}</strong>
                </div>
                @if(!empty($thread->admin_id))
                <div class="col-md-6" style="background: #18006f38;padding: 10px;border-radius: 10px;">
                    Transaction added by<br>
                    <strong>{{ $thread->admin->name }}</strong>
                </div>
                @else 
                <div class="col-md-6" style="background: #006f3138;padding: 10px;border-radius: 10px;">
                    Transaction added by<br>
                    <strong>{{ $thread->user->name }}</strong>
                </div>
                @endif
            </div>
            <hr>
            @endforeach
        </div>
        
        </div>
    </div>
</div>
@endsection