<?php $__env->startSection('title', 'All Facilitators'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">Facilitators $ Graders <a href="<?php echo e(route('teachers.create')); ?>"><button type="button" class="btn btn-outline-primary">Add New</button></a></h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Trainings</th>
                            <th>Direct Students</th>
                            <th>Earnings</th>
                            <th>Off Season</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td>
                                <img src="<?php echo e($user->image); ?>" alt="avatar" class="rounded-circle" width="50" height="50">
                            </td>
                            <td>
                                <small>
                                <strong>Name: </strong> <?php echo e($user->name); ?><br>
                                <strong>Email: </strong> <?php echo e($user->email); ?><br>
                                <strong>Phone: </strong> <?php echo e($user->t_phone); ?><br>
                                
                                <strong>Assigned trainings: </strong><?php echo e($user->trainings->count()); ?> <br>
                                <?php if($user->license): ?>
                                <strong style="color:green">WTN License: </strong> <span style="color:green"><?php echo e($user->license); ?></span> <br>
                                <?php endif; ?>
                                </small>
                            </td>
                            
                            <td style="margin: auto;display: flex;border-bottom: none;display: grid;">
                                <?php if(!empty(array_intersect(facilitatorRoles(), $user->role()))): ?>
                                <button class="disabled btn btn-primary btn-sm">Facilitator</button> <br>
                                <?php endif; ?>

                                <?php if(!empty(array_intersect(graderRoles(), $user->role()))): ?>
                                <button class="disabled btn btn-info btn-sm">Grader</button> <br>
                                <?php endif; ?>

                                <?php if(!empty(array_intersect(adminRoles(), $user->role()))): ?>
                                <button class="disabled btn btn-success btn-sm" style="background-color: darkblue;border-color: darkblue;">Admin</button> <br>
                                <?php endif; ?>
                              
                            <?php if($user->status == 'active'): ?> <button class="btn btn-success btn-xs">Active</button> <?php else: ?> <button class="btn btn-danger btn-xs">Inactive</button> <?php endif; ?>
                            </td>
                            <td>
                                <small>
                                    <?php $__currentLoopData = $user->p_names; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$names): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <strong style="color:red"><?php if($index < count($user->p_names)): ?>|| <?php endif; ?></strong><?php echo e($names); ?> <?php if($index < count($user->p_names)-1): ?><br><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </small>
                            </td>
                            <td><?php echo e($user->students_count); ?> <br>
                                <a target="_blank" href="<?php echo e(route('teachers.students', $user->id)); ?>" class="btn btn-info btn-xs">View</a>
                            </td>
                            
                            <td><?php echo e($user->payment_modes->currency_symbol ?? 'NGN'); ?><?php echo e($user->earnings ? number_format($user->earnings) : 0); ?> <br>
                                <a target="_blank" href="<?php echo e(route('teachers.earnings', $user->id)); ?>" class="btn btn-info btn-xs">View</a>
                            </td>
                          
                            <td><?php echo e($user->off_season_availability == 1 ? 'Yes' : 'No'); ?></td>
                                          
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit facilitator"
                                        class="btn btn-info" href="<?php echo e(route('teachers.edit', $user->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>                                   
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                    class="btn btn-warning" href="<?php echo e(route('impersonate', $user->id)); ?>"><i
                                        class="fa fa-unlock"></i>
                                    </a>
                                    <form action="<?php echo e(route('teachers.destroy', $user->id)); ?>" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete facilitator"> <i class="fa fa-trash"></i>
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
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/teachers/index.blade.php ENDPATH**/ ?>