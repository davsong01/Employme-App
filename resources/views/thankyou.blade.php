<?php
    $template = \Request::get('template');
    $currency = \Session::get('currency');
    $currency_symbol = \Session::get('currency_symbol');
    $exchange_rate = \Session::get('exchange_rate');
?>

@include('layouts.'.$template.'.thankyou')