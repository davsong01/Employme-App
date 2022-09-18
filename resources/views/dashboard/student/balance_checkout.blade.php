@extends('dashboard.student.trainingsindex')
@section('content')
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
                <h2 style="color:green; text-align:center; padding:20px">Balance payment for : {{$program->p_name}}</h2>
            </div>
        </div>
        <div class="col-md-12 col-lg-12" style="text-align: center;">
            <form action="{{route('pay')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                <input type="hidden" name="type" value="balance">
                <input type="hidden" name="user_program" value={{ $data->id }}>
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="phone" value="{{ auth()->user()->t_phone }}">
                <input type="hidden" name="quantity" value="1">
                               
            {{ csrf_field() }}
                <h4 class="">Select payment method</h4>
                <div class="align-items-center">
                    @if($payment_mode->type == 'card')
                    <button class="mr-1 mb-1 pay-option" name="payment_mode" value="{{  $payment_mode->id }}"><i class="fa fa-credit-card"></i> Pay with <span style="background-image:url({{ url('/').'/paymentmodes/'.$payment_mode->image }});background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                    @endif
                    @if($payment_mode->type == 'crypto')
                    <button class="mr-1 mb-1 pay-option" name="payment_mode" value="{{  $payment_mode->id }}"><i class="fa fa-bitcoin"></i> Pay with <span style="background-image:url({{ url('/').'/paymentmodes/'.$payment_mode->image }});background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    
@endsection
    