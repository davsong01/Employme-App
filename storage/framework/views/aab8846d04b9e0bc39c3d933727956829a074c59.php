<?php $__env->startSection('Edit Transaction' ); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4>Invoice Id: <?php echo e($transaction->invoice_id); ?></h4>
                        <h6>Transaction Id: <?php echo e($transaction->transid); ?></h6>
                    </div>
                    <form action="<?php echo e(route('payments.update', ['payment' => $transaction->id, 'program_amount' => $transaction->p_amount])); ?>" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>

                        <div class="row">
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label for="name">Name of Participant:  <?php echo e($transaction->name); ?></label> <br>

                                    <label>Bank: <?php echo e($transaction->t_type); ?> </label> <br>
                                    <label for="transaction_id">Program Amount: <?php echo e(\App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->p_amount)); ?></label> <br>
                                    <label for="transaction_id">Paid: <?php echo e(\App\Settings::value('DEFAULT_CURRENCY').number_format($transaction->t_amount)); ?></label> <br> <br>

                                    <label>Balance: <span style="color:<?php echo e($transaction->balance > 0 ? 'red' : 'green'); ?>"><?php echo e(\App\Settings::value('DEFAULT_CURRENCY'). number_format($transaction->balance)); ?></span> </label> <br>

                                </div>
                                
                                <div class="form-group">
                                    <label>New Amount</label><span></span>
                                    <input type="number" name="amount" value="<?php echo e(old('amount') ?? 0); ?>" class="form-control">
                                </div>
                                <!--Gives the first error for input name-->
                                <div><small style="color:red"><?php echo e($errors->first('amount')); ?></small></div>
                               
                                <input type="hidden" name="training_mode" value="<?php echo e($transaction->training_mode); ?>">
                                <?php if(isset($locations) && !empty($locations)): ?>
                                <div class="form-group<?php echo e($errors->has('location') ? ' has-error' : ''); ?>">
                                    <label for="location">Location </label>
                                     <select  id="location" name="location" class="form-control">
                                        <option value=""></option>
                                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location); ?>" <?php echo e($location == $transaction->t_location ? 'selected' :''); ?>><?php echo e($location); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    <?php if($errors->has('location')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('location')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                               
                                <?php if(isset($coupons) && $coupons->count()>0): ?>
                                <div class="form-group<?php echo e($errors->has('coupon_id') ? ' has-error' : ''); ?>">
                                    <label for="location">Coupon Used </label>
                                     <select  id="coupon_id" name="coupon_id" class="form-control">
                                        <option value="">Select..</option>
                                        <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($coupon->id); ?>" <?php echo e($coupon->id == $transaction->coupon_id ? 'selected' :''); ?>><?php echo e($coupon->code); ?>(<?php echo e(number_format($coupon->amount)); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    <?php if($errors->has('coupon_id')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('coupon_id')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                            <div class="row">
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    Submit
                                </button>
                            </div>
                        </div>
                        <?php echo e(csrf_field()); ?>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/transactions/edit.blade.php ENDPATH**/ ?>