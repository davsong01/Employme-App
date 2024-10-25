<?php
    $template = \Request::get('template');
    $template = \Request::get('template');
    $currency = \Session::get('currency') ;
    $currency_symbol = \Session::get('currency_symbol');
    $exchange_rate = \Session::get('exchange_rate') ?? 1;

?>

@include('layouts.'.$template.'.single_training')