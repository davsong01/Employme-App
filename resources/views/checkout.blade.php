<?php
    $template = \Request::get('template');
?>

@include('layouts.'.$template.'.checkout')