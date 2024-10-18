<?php $__env->startSection('content'); ?>
<div class="col-md-7">
    <div class="card-body">
        <div class="brand-wrapper">
            <img src="<?php echo e(asset('login_files/assets/images/logo.png')); ?>" alt="logo"
                style="width: 230px !important">
        </div>
        <p>Enter your email address and we will send you a Password reset link</p>
        <form class="form-horizontal" method="POST" action="<?php echo e(route('password.email')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group mb-4<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                <label for="email" class="sr-only">Email</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>"  id="email" class="form-control"
                    placeholder="Email address">
            </div>
            <?php if($errors->has('email')): ?>
                <span class="help-block" style="font-weight: 50 !important;">
                    <strong><?php echo e($errors->first('email')); ?></strong>
                </span>
            <?php endif; ?>
            <button type="submit" class="btn btn-block login-btn mb-4">
                Send Password Reset Link
            </button>
        </form>
       <br><br>
        <span>Designed by <a href="https://techdaves.com" target="_blank">Techdaves</a></span>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>