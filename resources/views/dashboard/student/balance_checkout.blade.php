<?php 
    use App\Models\Settings;
    $balance = app('App\Http\Controllers\WalletController')->getWalletBalance(resolveAuthUser()->id);
    $currency = Settings::value('DEFAULT_CURRENCY');
?>
@extends('dashboard.student.trainingsindex')
@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Balance Payment</span>
                            <h4 class="mb-2">Pending balance payment</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @include('layouts.partials.alerts')
                </div>
                <div class="card-body text-center pb-4">
                    <h4 class="text-danger mb-4">You have a pending balance payment of {{$currency. number_format($program->checkBalance($program->id))}} for: {{$program->p_name}}</h4>
                    @if($program->checkBalance($program->id) <= $balance)
                        {{-- <a style="margin-top:15px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal" class="me-1 mb-1 pay-option" name="payment_mode" value="{{  $payment_mode->id }}"><i class="fa fa-credit-card"></i> Pay from Account Balance ({{$currency.number_format($balance)}})</a><br><br><br> --}}
                        <a style="margin-top:15px" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal" class="me-1 mb-1 pay-option" name="payment_mode" value="wallet"><i class="fa fa-credit-card"></i> Pay from Account Balance ({{$currency.number_format($balance)}})</a><br><br><br>

                        <p><a class="btn btn-success btn-sm" target="_blank" style="border-radius:5px" href="{{route('home')}}"><i class="fa fa-plus"></i>&nbsp;Top Up Account Balance</a>
                        </p>
                    @else 
                        <p>
                            <span class="balance">Your Account Balance : ({{$currency.number_format($balance)}})</span> <br>
                            <a target="_blank" class="btn btn-success btn-sm" style="border-radius:5px"href="{{route('home')}}"><i class="fa fa-plus"></i>&nbsp;Top Up Account Balance to be able to make payment</a></p>
                        </p>
                    @endif
                    
                </div>
            </div>
            
        </div>


        {{-- <div class="col-md-12 col-lg-12" style="text-align: center;">
            <form action="{{route('pay')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                <input type="hidden" name="type" value="balance">
                <input type="hidden" name="user_program" value={{ $data->id }}>

                <input type="hidden" name="email" value="{{ resolveAuthUser()->email }}">
                <input type="hidden" name="name" value="{{ resolveAuthUser()->name }}">
                <input type="hidden" name="phone" value="{{ resolveAuthUser()->phone }}">
                <input type="hidden" name="quantity" value="1">
                               
            {{ csrf_field() }}
                <h4 class="">Select payment method</h4>
                <div class="align-items-center">
                    @if($payment_mode->type == 'card')
                    <button class="me-1 mb-1 pay-option" name="payment_mode" value="{{  $payment_mode->id }}"><i class="fa fa-credit-card"></i> Pay with <span style="background-image:url({{ url('/').'/paymentmodes/'.$payment_mode->image }});background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                    @endif
                    @if($payment_mode->type == 'crypto')
                    <button class="me-1 mb-1 pay-option" name="payment_mode" value="{{  $payment_mode->id }}"><i class="fa fa-bitcoin"></i> Pay with <span style="background-image:url({{ url('/').'/paymentmodes/'.$payment_mode->image }});background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                    @endif
                </div>
            </form>
        </div> --}}
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pay balance of {{ $program->checkBalance($program->id )}} from Account Balance ({{$currency.number_format($balance)}})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('account.pay', 'wallet')}}" method="POST" onsubmit="return loader()">
                    @csrf
                    @if($program->allow_flexible_payment == 'yes')
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="amount">Amount</label>
                            <input id="amount" type="number" class="form-control" amount="amount" min="1" name="amount" value="{{ $program->checkBalance($program->id )}}" autofocus required>
                            @if ($errors->has('amount'))
                                <div class="text-danger small mt-1">{{ $errors->first('amount') }}</div>
                            @endif
                        </div>
                    </div>
                    @else 
                    <input type="hidden" class="form-control" name="amount" value="{{$program->checkBalance($program->id)}}" autofocus required>
                    @endif
                    
                    <input type="hidden" class="form-control" name="p_id" value="{{$program->id}}" required>
                    <button type="submit" class="btn btn-success">Make payment</button> <span id="spinner" style="display:none"><i style="color:red" class="fa fa-spinner fa-spin"></i> <strong style="color:red">Please wait, payment is processing</strong></span>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="s-button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
    <script>
        function loader(){
            let doc;
            let result = confirm("Are you sure you want to make payment?");
            if (result == true) {
                $("#spinner").show();
                $("s-button").hide();
                return true;
            } else {
                $('#exampleModal').modal('hide');
                return false;
            }
        }
    </script>
@endsection
