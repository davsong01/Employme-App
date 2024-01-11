<?php $__env->startSection('title', 'Payment History'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice ID</th>
                            <th>Channel</th>
                            <th>Type</th>
                            <th>Training</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $transactiondetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($details->created_at); ?></td>
                            <td><?php echo e($details->invoice_id); ?></td>
                            <td><?php echo e($details->t_type); ?></td>
                            <td><?php echo e($details->paymenttype); ?></td>
                            <td><?php echo e($details->p_name); ?></td>
                            <td><?php echo e($details->p_amount); ?></td>
                            <td><?php echo e(config('custom.default_currency')); ?><?php echo e($details->t_amount); ?></td>
                            <?php if($details->paymentStatus == 0 ): ?>
                            <td><b style="color:red"><?php echo e(config('custom.default_currency')); ?><?php echo e($details->balance); ?> </b></td>
                            <?php else: ?>
                            <td><b style="color:green"><?php echo e(config('custom.default_currency')); ?><?php echo e($details->balance); ?></b></td>
                            <?php endif; ?>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning" href="<?php echo e(route('payments.print', $details->id)); ?>"><i
                                            class="fa fa-print"></i>
                                </a>
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
<?php echo $__env->make('dashboard.student.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/payments/index.blade.php ENDPATH**/ ?>