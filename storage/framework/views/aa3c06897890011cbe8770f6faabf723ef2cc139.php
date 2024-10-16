<?php $__env->startSection('content'); ?>
<div class="col-md-7">
    <div class="card-body">
        <div class="brand-wrapper">
            <a href="/">
                <img src="<?php echo e(asset('login_files/assets/images/logo.png')); ?>" alt="logo"
                style="width: 230px !important">
            </a>
        </div>
        <p class="login-card-description">Login to Start learning</p>
        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form method="POST" action="<?php echo e(route('login')); ?>" method="post" style="max-width:100% !important">
            <?php echo e(csrf_field()); ?> 
            <div class="form-row">
                    <div class="form-group col-md-12 mb-4<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                        <label for="login" class="sr-only">Email or Staff ID</label>
                        <input type="text" name="login" value="<?php echo e(old('login')); ?>"  id="login" class="form-control"
                            placeholder="Email or Staff ID">
                    </div>
                    <?php if($errors->has('login')): ?>
                        <span class="help-block" style="font-weight: 50 !important;">
                            <strong><?php echo e($errors->first('login')); ?></strong>
                        </span>
                    <?php endif; ?>
                    <div class="form-group col-md-12 mb-4<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" name="password" id="password" class="form-control"
                        >
                    </div>
                    <?php if($errors->has('password')): ?>
                        <span class="help-block" style="font-weight: 50 !important;">
                            <strong><?php echo e($errors->first('password')); ?></strong>
                        </span>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-block login-btn mb-4">
                        Login
                    </button>
               
            </div>    
        </form>
        <a class="btn btn-link" href="<?php echo e(route('password.request')); ?>">
            Forgot Your Password?
        </a><br><br>
        <span>Designed by <a href="https://techdaves.com">Techdaves</a></span>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/auth/login.blade.php ENDPATH**/ ?>