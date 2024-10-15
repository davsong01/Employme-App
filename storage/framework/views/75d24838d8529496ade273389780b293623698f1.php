<?php $__env->startSection('title', 'All Coupons'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
         </div>
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Coupons <a href="<?php echo e(route('coupon.create')); ?>"><button type="button" class="btn btn-outline-primary">Add New Coupon</button></a></h5> 
            </div>
        </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Amount</th>
                            <th>Training</th>
                            <th>Created by</th>
                            <th>Usage count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($coupon->code); ?></td>
                            <td><?php echo e(\App\Models\Settings::value('DEFAULT_CURRENCY') . number_format($coupon->amount)); ?></td>                          
                            <td><?php echo e($coupon->program->p_name  ?? 'NULL'); ?></td>                          
                            <td><?php echo e(isset($coupon->facilitator->name) ? $coupon->facilitator->name : 'Administrator'); ?></td>                          
                            <td><?php echo e($coupon->coupon_users->count()); ?></td>                          
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="edit coupon details"
                                        class="btn btn-info" href="<?php echo e(route('coupon.edit', $coupon->id)); ?>"><i class="fa fa-edit"></i>
                                    </a>
                                        <form action="<?php echo e(route('coupon.destroy', $coupon->id)); ?>" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            <?php echo e(csrf_field()); ?>

                                            <?php echo e(method_field('DELETE')); ?>


                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete coupon"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    <a data-toggle="tooltip" data-placement="top" title="View coupon usage"
                                        class="btn btn-primary" href="<?php echo e(route('coupon.show', $coupon->id)); ?>"><i class="fa fa-eye"></i>
                                    </a>
                                </div>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/coupons/index.blade.php ENDPATH**/ ?>