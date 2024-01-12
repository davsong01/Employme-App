<?php $__env->startSection('title', 'Payment History'); ?>
<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('modal.css')); ?>" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">
<style>
    a{
        text-decoration: none !important;
    }

    .accounts{
        min-height: 270px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<?php $__env->stopSection(); ?>
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
                            <th>Invoice ID</th>
                            <th>Payment Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $transactiondetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong>Invoice Id:</strong> <?php echo e($details->invoice_id); ?> <br>
                                <strong>Transaction Id:</strong> <?php echo e($details->transid); ?> <br>
                                <strong>Date: </strong><?php echo e($details->created_at->format('d/m/Y')); ?> <br>
                                <strong>Channel: </strong><?php echo e($details->t_type); ?>

                                <?php if($details->paymentthreads->count() > 0): ?>
                                <br>
                                    <a class="btn btn-info btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal<?php echo e($details->transid); ?>"><i class="fa fa-eye"></i>View Payment Trail</a>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <strong>Training: </strong><?php echo e($details->p_name); ?> <br>
                                <strong>Training Fee: </strong><?php echo e($details->p_amount); ?> <br>
                                <strong>Amount Paid: </strong><?php echo e(config('custom.default_currency')); ?><?php echo e($details->t_amount); ?> <br>
                                <?php if(!empty($details->paymenttype)): ?>
                                <strong>Type: </strong><?php echo e($details->paymenttype); ?> <br>
                                <?php endif; ?>
                                <strong>Balance: </strong><span style="color:<?php echo e($details->paymentStatus == 0 ? 'red' : 'green'); ?>"><?php echo e(config('custom.default_currency')); ?><?php echo e($details->balance); ?> </span>
                            </td>
                            
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning btn-sm" href="<?php echo e(route('payments.print', $details->id)); ?>"><i
                                            class="fa fa-print"></i>
                                </a>
                            </td>
                        </tr>

                         <div class="modal fade" id="exampleModal<?php echo e($details->transid); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Payment Trail for <?php echo e($details->transid); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php $__currentLoopData = $details->paymentthreads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            Transaction Id <br>
                                            <strong><?php echo e($thread->transaction_id); ?></strong>
                                        </div>
                                        <div class="col-md-6">
                                            Date <br>
                                            <strong><?php echo e($thread->created_at->format('d/m/Y')); ?></strong>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            Amount<br>
                                            <strong><?php echo e(number_format($thread->amount)); ?></strong>
                                        </div>
                                    </div>
                                    <hr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/payments/index.blade.php ENDPATH**/ ?>