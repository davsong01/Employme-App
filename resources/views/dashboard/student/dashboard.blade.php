<?php 
    use App\Settings;
    $accounts = getAccounts();
    $settings = Settings::first();
    $payment_modes = app('App\Http\Controllers\PaymentController')->getPaymentModes();
?>
@extends('dashboard.student.index')
@section('css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">
<style>
    a{
        text-decoration: none !important;
    }

    .accounts{
        min-height: 270px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-6">
            <a href="{{ route('profiles.edit', Auth::user()->id) }}">
                <div class="card card-hover">
                    <div class="box bg-cyan text-center">
                        <h1 class="font-light text-white"><i class="fas fa-user-edit"></i></h1>
                        <h6 class="text-white">Welcome, {{ Auth::user()->name }}</h6>
                        <p class="text-white">Edit my profile</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-6">
            <div class="card">
                <div class="box bg-dark text-center">
                    <h1 class="font-light text-white"><img src="/money-bag.png" alt="" style="width: 38px;"></h1>
                    <h6 class="text-white">Account Balance</h6>
                    <p class="text-white">{{ \App\Settings::value('DEFAULT_CURRENCY'). number_format($account_balance) }} &nbsp;&nbsp; <a style="color:white" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal">Top Up</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Top Up account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    @if (!empty($topup_programs))
                            <strong>Please note that you can only use funds in your account for the following Training(s): </strong> <br>
                            @foreach($topup_programs as $key=>$program)
                            <ol>
                                <li>
                                    {{ $program->p_name }}
                                </li>
                            </ol>
                            @endforeach
                        @else 
                            Please note that there are no Trainings that you can pay for with your account balance
                        @endif
                </div>
                <strong>Click any of the following methods to top up your account</strong> <br>
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            Manual Topup
                        </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <p><strong>1. Make payment into any of the following account</strong></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="nigeria" class="accounts" style="border-radius: 5px;background: #f2f2e8;color: black;padding: 15px;margin: 5px;">
                                            <h6 style="">Nigeria (Naira Payment)</h6>
                                            @foreach($accounts as $account)
                                                @if ($account['country'] == 'Nigeria')
                                                    <div class="inner" style="margin-bottom: 15px;">
                                                        <strong>Bank: </strong>{{$account['bank']}} <br>
                                                        <strong>Account Number: </strong>{{$account['number']}} <br>
                                                        <strong>Name: </strong>{{$account['name']}} <br>
                                                    </div> 
                                                    @if(count($accounts) > 1 && $key+1 < count($accounts)) <hr> @endif
                                                   
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="ghana" class="accounts" style="border-radius: 5px;background: #ffff7e;color: black;padding: 15px;margin: 5px;">
                                            <h6 style="">Ghana (Cedes Payment)</h6>
                                            @foreach($accounts as $key=>$account)
                                                @if ($account['country'] == 'Ghana')
                                                    <div class="inner" style="margin-bottom: 15px;">
                                                        <strong>Bank: </strong>{{$account['bank']}} <br>
                                                        <strong>Account Number: </strong>{{$account['number']}} <br>
                                                        <strong>Name: </strong>{{$account['name']}} <br>
                                                    </div>
                                                    @if(count($accounts) > 1 && $key > count($accounts)) <hr> @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <p><strong>2. Upload your proof of payment using the form below</strong></p>
                                <form action="{{route('account.topup', 'manual')}}" method="POST" class="pb-2" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                <label for="amount">Amount</label>
                                                <input id="amount" type="number" class="form-control" amount="amount" min="1" name="amount" value="{{ old('amount')}}" autofocus required>
                                                @if ($errors->has('amount'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('amount') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('pop') ? ' has-error' : '' }}">
                                                <label for="pop">Proof of payment (Images, < 2mb)</label>
                                                <input id="pop" type="file" class="form-control" pop="pop" name="pop" accept="image/*" value="{{ old('pop')}}" autofocus required>
                                                @if ($errors->has('pop'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('pop') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <button type="submit" class="btn btn-primary" style="width:100%">Upload proof of payment</button>
                                    </div>
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                            Instant Top up
                        </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            Enter Amount and Make Payment via any of the specified channels below: <br>
                            <div class="">
                            <form action="{{route('account.topup', 'virtual')}}" method="POST">
                                @csrf
                                <div class="col-md-12">
                                    <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                        <label for="amount">Amount</label>
                                        <input id="amount" type="number" class="form-control" amount="amount" min="1" name="amount" value="{{ old('amount')}}" autofocus required>
                                        @if ($errors->has('amount'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('amount') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                 <input type="hidden" name="orderID" value="{{ rand(10000,99999) }}">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="currency" value="{{ $settings->currency }}">
                                <input type="hidden" name="metadata" value="{{ json_encode(['type' => 'virtual-topup']) }}"> 
                                                 
                                @foreach($payment_modes as $mode)
                                @if($mode->type == 'card')
                                <button class="mr-1 mb-1 pay-option" name="payment_mode" value="{{  $mode->id }}"><i class="fa fa-credit-card"></i> Pay with <span style="background-image:url({{ url('/').'/paymentmodes/'.$mode->image }});background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                                @endif
                                @if($mode->type == 'crypto')
                                <button class="mr-1 mb-1 pay-option" name="payment_mode" value="{{  $mode->id }}"><i class="fa fa-bitcoin"></i> Pay with <span style="background-image:url({{ url('/').'/paymentmodes/'.$mode->image }});background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                                @endif
                                @endforeach
                            </form>
                            </div>
                            
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-12 col-lg-12">
            <div class="card-body">
                <h2 style="text-align: center; color:green">My Trainings (Click to Access)</h2>
                @foreach($thisusertransactions as $details)
                    <div class="col-md-12 col-lg-12">
                        <a href="{{ route('trainings.show', ['p_id' => $details->p_id]) }}">
                            <div class="card card-hover">
                                <div class="box bg-success text-center">
                                    <h1 class="font-light text-white"><i class="fas fa-chalkboard-teacher"></i></h1>
                                    <h6 class="text-white">{{ $details->p_name }}</h6>
                                    <p class="text-white">{{ $details->modules }} Enabled Module Tests | {{ $details->materials }} Materials</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Column -->
    </div>
</div>
@endsection