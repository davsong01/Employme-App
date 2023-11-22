<?php $__env->startSection('title', 'My Students'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">My Students</h5>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date Joined</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <?php if (!empty(array_intersect(adminRoles(), auth()->user()->role()))) : ?>
                                <th>Email</th>
                            <?php endif; ?>
                            <th>Phone</th>
                            <th>Training</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users;
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $user) : $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                <td><?php echo e($user->created_at); ?></td>
                                <td><img src="<?php echo e(asset('profiles/' . $user->profile_picture)); ?>" alt="<?php echo e($user->profile_picture); ?>" class="rounded-circle" width="50" height="50"></td>
                                <td><?php echo e($user->name ?? NULL); ?></td>
                                <?php if (!empty(array_intersect(adminRoles(), auth()->user()->role()))) : ?>
                                    <td><?php echo e($user->email ?? NULL); ?></td>
                                <?php endif; ?>
                                <td><?php echo e($user->t_phone ?? NULL); ?></td>

                                <td><?php echo e($user->p_name ?? NULL); ?></td>
                            <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/teachers/my_students.blade.php ENDPATH**/ ?>