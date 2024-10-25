<?php
    $logo = \App\Models\Settings::first()->value('logo');
    // $currency = \Session::get('currency');
    // $currency_symbol = \Session::get('currency_symbol');
    // $exchange_rate = \Session::get('exchange_rate');
    $currency_symbol = $data['currency_symbol'];

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>{{ config('app.name') }} </title>

  <!-- Font Awesome Icons -->
  <link href="{{ asset('assets/inc/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>

  <!-- Plugin CSS -->
  <link href="{{ asset('assets/inc/vendor/magnific-popup/magnific-popup.css') }}" rel="stylesheet">
  
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($logo)}}">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="{{ asset('assets/inc/css/creative.min.css') }}" rel="stylesheet">
  <style>
  	.text-white-75.font-weight-light.mb-5 {
    	margin-bottom: 5px !important;
	}
	.value{
	    color:yellow;
	}
  </style>
</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
       
      <a class="navbar-brand js-scroll-trigger" href="/"><img style="width: 25% !important;" src="{{ asset($logo)}}" alt="logo"></a>
    </div>
  </nav>

  <!-- Masthead -->
  <header class="masthead">
    <div class="container h-100">
      <div class="row h-100 align-items-center justify-content-center text-center">
        <div class="col-lg-10 align-self-end">
          <h4 class="text-uppercase text-white font-weight-bold">THANK YOU {{ $data['name'] }}</h4>
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5">Thank you for making payment. Please save the details of your payment below. Please check your email ({{ $data['email'] }}) for your E-receipt and login details </p>
          <div class="row">
              <div class="container">

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
                      <td class="value"> {{ $data['programAbbr'] }}</td>
                    </tr>
                    <tr>
                      <td>Email</td>
                      <td class="value">{{ $data['email'] }}</td>
                  
                    </tr>
                    <tr>
                      <td>Invoice ID</td>
                      <td class="value">{{ $data['invoice_id']}}</td>
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
                      <td class="value">{{ $data['message']}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
          </div>
          
        </div>
      </div>
    </div>
  </header>
  <!-- Footer -->
  <footer class="bg-light py-5">
    <div class="container">
      <div class="small text-center text-muted">©{{ date("Y") }} All Rights Reserved by {{ config('app.name') }} | <a
                href="https://techdaves.com">Designed by TechDaves</a></div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="{{ asset('assets/inc/vendor/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/inc/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Plugin JavaScript -->
  <script src="{{ asset('assets/inc/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
  <script src="{{ asset('assets/inc/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

  <!-- Custom scripts for this template -->
  <script src="{{ asset('assets/inc/js/creative.min.js') }}"></script>

</body>

</html>
