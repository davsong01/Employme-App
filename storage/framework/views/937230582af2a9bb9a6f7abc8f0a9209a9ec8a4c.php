<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?> - Login
<?php $__env->stopSection(); ?>
<?php $__env->startSection('pagetitle', 'Company Admin Login'); ?>
<?php $__env->startSection('content'); ?>
<section class="checkout spad" style="padding-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <div class="checkout__form">
            <h4>Company Admin Login</h4>
            <form action="<?php echo e(route('company_user.login.post')); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="coupon_id" value="<?php echo e(session()->get('data')['metadata']['coupon_id'] ?? null); ?>">
                <div class="row">
                    
                    <div class="col-lg-12 col-md-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Email or Staff ID<span>*</span></p>
                                    <input type="text" class="form-control" id="login" name="login" required>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Password<span>*</span></p>
                                    <input type="password" id="password" name="password" required>
                                </div>
                            </div>
                        </div>          
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="site-btn checkout-button">LOGIN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/company_users/login.blade.php ENDPATH**/ ?>