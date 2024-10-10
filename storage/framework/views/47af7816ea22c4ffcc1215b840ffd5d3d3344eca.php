<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?> - Page Expired
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <div class="checkout__form text-center">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Page Expired</h1>
                        <p>Sorry, your session has expired. Please click the button below to refresh.</p>
                        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-primary">REFRESH</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/errors/419.blade.php ENDPATH**/ ?>