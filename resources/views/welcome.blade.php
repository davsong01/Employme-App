<?php
    $template = \Request::get('template');
?>

@include('layouts.'.$template.'.welcome')