<?php 
    use App\Settings;
?>

<?php $__env->startSection('title', 'Account TopUp History'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <h5 class="card-title">Account TopUp History </h5>
            <div style="margin: 15px 0;">
                Balance: <span class="bal" style="padding: 5px;border-radius: 10px;background: antiquewhite;"><?php echo e(Settings::value('DEFAULT_CURRENCY'). number_format($balance)); ?></span> 
                <a target="_blank" class="btn btn-success btn-sm" style="border-radius: 5px;" href="<?php echo e(route('home')); ?>"><i class="fa fa-plus"></i> &nbsp;Top Up</a>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Channel</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(date("M jS, Y", strtotime($wallet->created_at))); ?></td>
                            <td><?php echo e($wallet->transaction_id); ?></td>
                            <td><?php echo e(ucfirst($wallet->method)); ?></td>
                            <td style="color:<?php echo e($wallet->type == 'credit' ? 'green' : 'red'); ?>"><?php echo e($wallet->type); ?></td>
                            <td><?php echo e(Settings::value('DEFAULT_CURRENCY').number_format($wallet->amount)); ?></td>
                            <td style="color:<?php echo e($wallet->status == 'approved' ? 'green' : 'red'); ?>"><?php echo e(ucfirst($wallet->status)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/payments/wallets.blade.php ENDPATH**/ ?>