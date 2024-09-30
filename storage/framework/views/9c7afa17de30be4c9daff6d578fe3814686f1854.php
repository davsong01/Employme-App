<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('modal.css')); ?>" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #e9ecef;
        border-radius: 20px;
        padding: 0.375rem 0.75rem;
        height: calc(2.25rem + 2px);
        font-size: 0.875rem;
        color: #4F5467;
        background-color: #fff;
        line-height: 1.5;
    }

    .select2.select2-container.select2-container--default {
        width: 100% !important;
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Payment History'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment History
                <div class="badge float-right">
                    <span class="transaction-count"><?php echo e($records); ?></span>
                </div>
            </h5>
            <div class="card-body">
                <?php
                    $currentStatus = request('status');
                ?>
            
                <div class="">
                <form class="search-form" method="GET" action="<?php echo e(route('payments.index')); ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo e(request('email')); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="<?php echo e(request('phone')); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control select2">
                                    <option value="">Select Type</option>
                                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->t_type); ?>"><?php echo e($type->t_type); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="program_id">Select Training</label> <br>
                                <select name="program_id" id="program_id" class="form-control select2">
                                    <option value="">Select Training</option>
                                    <?php $__currentLoopData = $allPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($training->id); ?>"><?php echo e($training->p_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from">From</label>
                                <input type="date" class="form-control" name="from" id="from">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to">To</label>
                                <input type="date" class="form-control" name="to" id="to">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for=""><span style="color:transparent">label</span></label> <br>
                                <button type="submit" class="btn btn-primary btn-search" style="width: 100%">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer details</th>
                                <th>Training details</th>
                                <th>Payment details</th>
                                <th>Actions</th>       
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i++); ?></a>
                                <td><strong>Name: </strong><a href="<?php echo e(route('users.edit', $transaction->user_id)); ?>" target="_blank"><?php echo e($transaction->user->name ?? 'N/A'); ?> &nbsp;<img src="/external.png" alt="" style="width: 10px;"></a>
                                    <br> <strong>Phone: </strong><?php echo e($transaction->user->t_phone ?? 'N/A'); ?> <br> <strong>Email:</strong> <?php echo e($transaction->user->email ?? 'N/A'); ?> <br> <strong>Account balance: </strong><?php echo e(number_format($transaction->user->account_balance)); ?></td>
                                <td>
                                    <small class="training-details">
                                        <a href="<?php echo e(route('programs.edit', $transaction->program->id)); ?>" target="_blank"><strong>Training:</strong> <?php echo e($transaction->program->p_name ?? 'N/A'); ?></a><br>  
                                        <?php if($transaction->program->allow_preferred_timing == 'yes' && !empty($transaction->program->preferred_timing)): ?> <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;"><?php echo e($transaction->preferred_timing); ?> </span> <br> <?php endif; ?>
                                        <strong>Paid:</strong> <?php echo e($transaction->currency. number_format($transaction->t_amount)); ?>

                                        <?php if(!is_null($transaction->coupon_code)): ?>
                                        <span style="color:blue">
                                        <strong>Coupon (<?php echo e($transaction->coupon_code); ?>) Applied | <?php echo e($transaction->currency.number_format($transaction->coupon_amount)); ?>  </strong>
                                        </span>
                                        <?php endif; ?>
                                        <br>
                                        <strong>Balance:</strong>
                                            <?php if($transaction->balance > 0 ): ?>
                                                <span style="color:red"><?php echo e($transaction->currency. number_format($transaction->balance)); ?> </span>
                                            <?php else: ?>
                                                <span style="color:green"><?php echo e($transaction->currency.  number_format($transaction->balance)); ?></span>
                                            <?php endif; ?>
                                        <br>      
                                    
                                        <?php
                                            if(isset($transaction->t_location) && isset($transaction->t_location)){
                                                $locations = json_decode($transaction->locations, true);
                                                $location_address = $locations[$transaction->t_location] ?? null;
                                            }
                                        ?>
                                    
                                        <?php if(isset($transaction->t_location) && !empty($transaction->t_location) && !empty( $location_address)): ?>
                                        <strong>Location:</strong> <?php echo e($transaction->t_location); ?>(<?php echo e($location_address); ?>) <br>
                                        <?php endif; ?>
                                        <strong>Date: </strong><?php echo e($transaction->created_at); ?>

                                    
                                    </small>
                                    
                                </td>   
                                <td>
                                    <small class="id-details">
                                        <strong>Invoice ID:</strong> <?php echo e($transaction->invoice_id); ?> <br>
                                        <strong>Transaction ID:</strong> <?php echo e($transaction->transid); ?> 
                                        <?php if(isset($transaction->balance_amount_paid)): ?>
                                        <br>
                                        <strong>Last Balance Paid:</strong> <?php echo e($transaction->currency_symbol.number_format($transaction->balance_amount_paid)); ?> <br>
                                        <strong>Paid At:</strong> <?php echo e($transaction->balance_paid); ?> 
                                        <?php endif; ?>
                                        <br>
                                        <strong>Payment Type:</strong> <?php echo e($transaction->paymenttype); ?> <br>
                                        <?php if(isset($transaction->training_mode)): ?>
                                        <strong>Training Mode:</strong> <?php echo e($transaction->training_mode); ?> <br>
                                        <?php endif; ?>
                                        <strong>Type: </strong><?php echo e($transaction->t_type); ?> <br>
                                        <strong>Currency: </strong><?php echo e($transaction->currency); ?>

                                    
                                        <?php if($transaction->paymentthreads->count() > 0): ?>
                                        <br>
                                            <a class="btn btn-info btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal<?php echo e($transaction->transid); ?>"><i class="fa fa-eye"></i>View Payment Trail</a>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a data-toggle="tooltip" data-placement="top" title="Edit Transaction"
                                            class="btn btn-info btn-sm" href="<?php echo e(route('payments.edit', $transaction->id)); ?>"><i
                                                class="fa fa-edit"></i>
                                        </a>
                                        <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                            class="btn btn-warning btn-sm" href="<?php echo e(route('payments.print', $transaction->id)); ?>"><i
                                                class="fa fa-print"></i>
                                        </a>
                                        <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                            class="btn btn-primary btn-sm" href="<?php echo e(route('payments.show', $transaction->id)); ?>"><i
                                                class="far fa-envelope"></i>
                                        </a>
                                        <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                            <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                                class="btn btn-dark btn-sm" href="<?php echo e(route('impersonate', $transaction->user_id)); ?>"><i
                                                    class="fa fa-unlock"></i>
                                            </a>
                                        <?php endif; ?> 
                                        <form action="<?php echo e(route('payments.destroy', $transaction->id)); ?>" method="POST"
                                            onsubmit="return confirm('Are you really sure?');">
                                            <?php echo e(csrf_field()); ?>

                                            <?php echo e(method_field('DELETE')); ?>


                                            <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                                data-placement="top" title="Delete transaction"> <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                            <div class="modal fade" id="exampleModal<?php echo e($transaction->transid); ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Payment Trail for <?php echo e($transaction->transid); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?php $__currentLoopData = $transaction->paymentthreads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                            <?php if(!empty($thread->admin_id)): ?>
                                            <div class="col-md-6" style="background: #18006f38;padding: 10px;border-radius: 10px;">
                                                Transaction added by<br>
                                                <strong><?php echo e($thread->admin->name); ?></strong>
                                            </div>
                                            <?php else: ?> 
                                            <div class="col-md-6" style="background: #006f3138;padding: 10px;border-radius: 10px;">
                                                Transaction added by<br>
                                                <strong><?php echo e($thread->user->name); ?></strong>
                                            </div>
                                            <?php endif; ?>
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
                <?php echo e($transactions->appends($_GET)->links()); ?>


            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select Training",
            width: '100%'
            allowClear: true            
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payments/index.blade.php ENDPATH**/ ?>