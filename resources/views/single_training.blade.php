<?php
    $template = \Request::get('template');
?>

@include('layouts.'.$template.'.single_training')