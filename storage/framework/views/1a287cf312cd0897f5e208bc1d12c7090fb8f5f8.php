<?php
    $template = \Request::get('template');
    $currency = \Session::get('currency');
    $currency_symbol = \Session::get('currency_symbol');
    $exchange_rate = \Session::get('exchange_rate');
?>

<?php echo $__env->make('layouts.'.$template.'.pop', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/pop.blade.php ENDPATH**/ ?>