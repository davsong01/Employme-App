<?php
    $template = \Request::get('template');
    $template = \Request::get('template');
    $currency = \Session::get('currency') ;
    $currency_symbol = \Session::get('currency_symbol');
    $exchange_rate = \Session::get('exchange_rate') ?? 1;

?>

<?php echo $__env->make('layouts.'.$template.'.single_training_with_children', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/single_training_with_children.blade.php ENDPATH**/ ?>