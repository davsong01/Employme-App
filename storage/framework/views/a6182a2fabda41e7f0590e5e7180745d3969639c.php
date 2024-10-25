<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('modal.css')); ?>" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Payment History'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <h5 class="card-title">Proof of Payment History</h5>
            <div class="">
                <table id="myTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer details</th>
                            <th>Training details</th>
                            <th>Amount Paid</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Actions</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $pops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <tr>
                            <td><?php echo e($pop->date); ?></td>
                            <td><?php echo e($pop->name); ?> <br>
                                <?php echo e($pop->phone); ?> <br>
                                <?php echo e($pop->email); ?> <br>
                                
                                <?php 
                                    $string =  "*Name:* " . $pop->name . "
                                    *Phone:* " . $pop->phone . "
                                    *Email:* " . $pop->email . "
                                    *Training:* " . $pop->program->p_name;
                                ?>
                                <a class="btn btn-dark btn-sm rounded" href="https://api.whatsapp.com/send?phone=2348037067223&text=<?php echo e(urlencode($string)); ?>" target="_blank">
                                    <i class="fab fa-whatsapp"></i> Send via WhatsApp
                                </a>
                            </td>
                            <td><?php echo e($pop->program->p_name); ?> <br>(<?php echo e($pop->program->e_amount <= 0 ? 'Amount: '.$pop->currency_symbol.$pop->program->p_amount : 'E/Amount '. $pop->currency_symbol.$pop->program->e_amount); ?>)
                            <?php if(isset($pop->is_fresh)): ?> <br>
                            <span style="margin:5px 10px;border-radius:10px" class="btn btn-info btn-sm">Fresh Payment</span>
                            <?php endif; ?>
                            </td>
                            <td><?php echo e(number_format($pop->amount)); ?></td>
                            <td><?php echo e($pop->bank); ?></td>
                            <td><?php echo e($pop->location); ?></td>
                            
                            <td>
                                <div class="btn-group">
                                    <a href="#" data-toggle="modal" data-target="#myModal<?php echo e($pop->id); ?>">
                                        <img title="View Proof of payment" id="myImg<?php echo e($pop->id); ?>" src="<?php echo e(url('/uploads/'.$pop->file)); ?>" alt="<?php echo e($pop->name); ?>" style="width:40px;max-width:300px">
                                    </a>
                                    <a href="#" class="btn btn-info" data-toggle="modal" data-target="#editpop<?php echo e($pop->id); ?>"><i class="fa fa-edit"></i>
                                    </a>
                                    <a title="Approve Payment" class="btn btn-success" href="<?php echo e(route('pop.show', $pop->id)); ?>"><i class="fa fa-check"></i></a>
                                    <form action="<?php echo e(route('pop.destroy', $pop->id)); ?>" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip" data-placement="top" title="Delete proof of payment">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade mt-5" id="myModal<?php echo e($pop->id); ?>" tabindex="-1" aria-labelledby="imageModal<?php echo e($pop->id); ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?php echo e($pop->name); ?>'s Payment Proof</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="<?php echo e(url('/uploads/'.$pop->file)); ?>" alt="<?php echo e($pop->name); ?>" class="img-fluid">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editpop<?php echo e($pop->id); ?>" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="batchModalLabel">Update <?php echo e($pop->name); ?>'s Payment Proof</h5>
                                        <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="<?php echo e(route('pop.update', $pop->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="date" class="form-label">Date</label>
                                                    <input type="date" class="form-control" id="date" name="date" value="<?php echo e($pop->date ? \Carbon\Carbon::parse($pop->date)->format('Y-m-d') : ''); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo e($pop->name); ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($pop->phone); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo e($pop->email); ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="amount" class="form-label">Amount</label>
                                                    <input type="text" class="form-control" id="amount" name="amount" value="<?php echo e($pop->amount); ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="location" class="form-label">Location</label>
                                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo e($pop->location); ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="program_id" class="form-label">Training</label>
                                                    <select name="program_id" class="form-control">
                                                        <option value="">Select</option>
                                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($program->id); ?>" <?php echo e($program->id == $pop->program->id ? 'selected' : ''); ?>>
                                                            <?php echo e($program->p_name); ?> (<?php echo e($program->p_amount); ?>)
                                                        </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
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
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payments/popfull.blade.php ENDPATH**/ ?>