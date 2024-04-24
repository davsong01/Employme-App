<?php $__env->startSection('title', 'Earnings'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
             <div class="card-header">
                <div>
                    <h5 class="card-title">Earnings (<?php echo e($currency?? 'NGN'); ?><?php echo e(number_format($earnings->sum('facilitator_earning'))); ?>)</h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice Id</th>
                            <th>Date Earned</th>
                            <th>Student</th>
                            <th>Program</th>
                            <th>Amount</th>
                            <th>Coupon applied</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($earning->invoice_id ?? NULL); ?></td>
                            <td><?php echo e($earning->created_at ?? NULL); ?></td>
                            <td><?php echo e($earning->name ?? NULL); ?></td>  
                            <td><?php echo e($earning->p_name ?? NULL); ?></td>
                            <td><?php echo e($earning->currency_symbol); ?> <?php echo e(number_format($earning->facilitator_earning, 2) ?? NULL); ?></td>
                            <td>
                                <?php if($earning->coupon_id): ?>
                                    <strong>Coupon Code:</strong> <?php echo e($earning->coupon_code); ?>  <br>
                                   
                                    <strong>Amount: </strong> <?php echo e($earning->currency_symbol); ?> <?php echo e(number_format($earning->coupon_amount,2)); ?> <br>
                                        <?php if(isset($earning->coupon_id)): ?>
                                        <strong>Created by: </strong>
                                            <?php if(isset($earning->facilitator_id)): ?>
                                            <?php echo e(app('\App\Http\Controllers\Admin\CouponController')->getCreatedBy($earning->coupon_id)); ?>

                                            <?php endif; ?>
                                        <?php endif; ?>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/teachers/my_student_earnings.blade.php ENDPATH**/ ?>