<?php $__env->startSection('title', 'All Company Users'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">Company Admins <a href="<?php echo e(route('companyuser.create')); ?>"><button type="button" class="btn btn-outline-primary">Add New</button></a></h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Trainings</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td>
                                <small>
                                <strong>Name: </strong> <?php echo e($user->name); ?><br>
                                <strong>Email: </strong> <?php echo e($user->email); ?><br>
                                <strong>Phone: </strong> <?php echo e($user->phone); ?><br>
                            </td>
                            <td>
                                <small>
                                    
                                </small>
                            </td>
                            <td>
                                <?php if($user->status == 'active'): ?> <button class="btn btn-success btn-xs">Active</button> <?php else: ?> <button class="btn btn-danger btn-xs">Inactive</button> <?php endif; ?>
                            </td>
                            <td><?php echo e($user->created_at); ?></td>
                            
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit"
                                        class="btn btn-info" href="<?php echo e(route('companyuser.edit', $user->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>                                   
                                    
                                    <form action="<?php echo e(route('companyuser.destroy', $user->id)); ?>" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/company/index.blade.php ENDPATH**/ ?>