<?php 
   $accounts = getAccounts();
?>

<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?> - Upload POP
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <?php if(session()->get('data')): ?>
        <div class="checkout__form transfer">
            <div class="b_transfer" style="font-size: 20px;background: #040080;color: white;padding: 20px;">Please pay &#8358;<?php echo e(number_format(session()->get('data')['amount'])); ?> (or its equivalent in your local currency) into an account below: <br>
                <div id="nigeria" style="border-radius: 5px;background: #f2f2e8;color: black;padding: 15px;margin: 5px;">
                    <h4 style="">Nigeria (Naira Payment)</h4>
                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($account['country'] == 'Nigeria'): ?>
                            <div class="inner" style="margin-bottom: 15px;">
                                <strong>Bank: </strong><?php echo e($account['bank']); ?> <br>
                                <strong>Account Number: </strong><?php echo e($account['number']); ?> <br>
                                <strong>Name: </strong><?php echo e($account['name']); ?> <br>
                            </div> 
                            <hr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div id="ghana" style="border-radius: 5px;background: #ffff7e;color: black;padding: 15px;margin: 5px;">
                    <h4 style="">Ghana (Cedes Payment)</h4>
                    <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($account['country'] == 'Ghana'): ?>
                            <div class="inner" style="margin-bottom: 15px;">
                                <strong>Bank: </strong><?php echo e($account['bank']); ?> <br>
                                <strong>Account Number: </strong><?php echo e($account['number']); ?> <br>
                                <strong>Name: </strong><?php echo e($account['name']); ?> <br>
                            </div>
                            <hr>

                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                And then Upload your proof of payment using the form below
            </div>
        </div>
        <?php endif; ?>
        <div class="checkout__form">
            <h4>Upload Proof of Payment</h4>
            <form action="<?php echo e(route('pop.store')); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="coupon_id" value="<?php echo e(session()->get('data')['metadata']['coupon_id'] ?? null); ?>">
                <div class="row">
                    
                    <div class="col-lg-12 col-md-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Name<span>*</span></p>
                                     <input type="text" class="form-control" id="name" name="name" 
                                        <?php if(auth()->guard()->check()): ?>
                                        value="<?php echo e(auth()->user()->name); ?>"  
                                        placeholder="Full Name"
                                        <?php endif; ?>
                                        <?php if(auth()->guard()->guest()): ?> 
                                        value="<?php echo e(session()->get('data')['name'] ?? old('name')); ?>" placeholder="Full Name"  
                                        <?php endif; ?> required>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Email<span>*</span></p>
                                    <input type="email" id="email" name="email" 
                                        <?php if(auth()->guard()->check()): ?>
                                        value="<?php echo e(auth()->user()->email); ?>"  
                                        <?php endif; ?>
                                        <?php if(auth()->guard()->guest()): ?> 
                                        value="<?php echo e(session()->get('data')['email'] ?? old('email')); ?>" placeholder="Enter email"  
                                        <?php endif; ?> required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Phone<span>*</span></p>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                        <?php if(auth()->guard()->check()): ?>
                                        value="<?php echo e(auth()->user()->phone); ?>"  
                                        
                                        <?php endif; ?>

                                        <?php if(auth()->guard()->guest()): ?> 
                                        value="<?php echo e(session()->get('data')['phone'] ?? old('phone')); ?>"  
                                        <?php endif; ?> required>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Select Course<span>*</span></p>
                                    <select name="training" id="training" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <?php $__currentLoopData = $trainings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <option value="<?php echo e($training->id); ?>" <?php echo e((isset(session()->get('data')['metadata']['pid']) && session()->get('data')['metadata']['pid'] == $training->id) ? 'selected' : ''); ?>><?php echo e($training->p_name); ?> | (<?php echo e($currency . number_format($training->p_amount)); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Bank paid into<span>*</span></p>
                                    <select name="bank" id="bank" class="form-control" required>
                                        <option value="">-- Select bank --</option>
                                        <option value="Access">Access</option>
                                        <option value="GTB">GTB</option>
                                        <option value="Mobile Money (MoMo)">Mobile Money (MoMo)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Amount (Enter integers only)<span>*</span></p>
                                    <input type="number" class="form-control" name="amount" id="amount" value="<?php echo e(session()->get('data')['amount'] ??  old('amount')); ?>" min=1 required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Upload proof of payment<span>*</span> (Image files only)</p>
                                    <input type="file" class="form-control" name="file" id="file" value="<?php echo e(old('file')); ?>" required accept="image/png,image/jpeg">
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Date of payment<span>*</span></p>
                                    
                                    <input type="date" class="form-control" name="date" id="date" value="<?php echo e(date('Y/m/d') ?? old('date')); ?>" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="currency" value="<?php echo e($currency); ?>">
                        <input type="hidden" name="currency_symbol" value="<?php echo e($currency_symbol); ?>">
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="site-btn checkout-button">UPLOAD</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/pop.blade.php ENDPATH**/ ?>