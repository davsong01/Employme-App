@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }}
@endsection
@section('content')
{{-- @php
    $data = [
        'name'=> 'assa sdd',
        'amount' => 12,
        'balance' => 12
    ];
@endphp --}}
<!-- Featured Section Begin -->
<section class="">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>THANK YOU {{ ucwords($data['name']) ?? null }}</h2>

                </div>
                <div class="section-title" style="margin-bottom: 10px; !important">
                    <a href="{{ url('/').'/dashboard' }}">
                    <button class="btn btn-primary">Continue to Dashboard <i class="fa fa-arrow-right"></i></button>
                    </a>
                </div>

                <div class="section-text" style="width: 70%;margin: auto;font-size: 19px;text-align: center;">
                    Thank you for making payment. Please save the details of your payment below. Please check your email ({{ $data['email'] ?? null}}) for your E-receipt and login details 
                </div>
                <div class="container" style="text-align: left;">

                <table class="table table-dark table-hover">
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th class="value">Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Training</td>
                      <td class="value">{{ $data['programAbbr'] ?? null}}</td>
                    </tr>
                    <tr>
                      <td>Email</td>
                      <td class="value">{{ $data['email'] ?? null}}</td>
                  
                    </tr>
                    <tr>
                      <td>Invoice ID</td>
                      <td class="value">{{ $data['invoice_id'] ?? null}}</td>
                    </tr>
                     <tr>
                      <td>Amount Paid</td>
                      <td class="value">{{  $currency_symbol.number_format($data['amount']) }}</td>
                    </tr>
                    <tr>
                      <td>Balance</td>
                      <td class="value">{{ $currency_symbol. number_format($data['balance'])}}</td>
                    </tr>
                    <tr>
                      <td>Payment Type</td>
                      <td class="value">{{ $data['message'] ?? null}}</td>
                    </tr>
                    @if(isset($data['facilitator_name']))
                    <tr>
                      <td>Instructor</td>
                      <td class="value">{{ $data['facilitator_name'] ?? null}}</td>
                    </tr>
                    @endif
                    @if(isset($data['coupon_amount']))
                    <tr>
                      <td>Coupoun Applied</td>
                      <td class="value">
                        <strong>Coupon Code:</strong> {{ $data['coupon_code']}} <br>
                         <strong>Coupon Amount:</strong> {{ $data['currency_symbol']. number_format($data['coupon_amount'])}}
                    </td>
                    </tr>
                    @endif
                  </tbody>
                </table>
               
              </div>
            </div>
        </div>
       
    </div>
</section>

@endsection