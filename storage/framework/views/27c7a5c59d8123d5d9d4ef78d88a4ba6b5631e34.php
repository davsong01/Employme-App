<?php $__env->startSection('title', 'All Users'); ?>
<?php $__env->startSection('content'); ?>
<?php 
    $menus = Auth::user()->menu_permissions ?? [];
    if($menus){
        $menus = explode(',',$menus);
    }else{
        $menus = [];
    }
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
                <?php if(session()->get('message')): ?>
                <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> <?php echo e(session()->get('message')); ?>

                      </div>
                <?php endif; ?>
            <h5 class="card-title">All Students</h5>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Trainings</th>
                            <?php if(in_array(20, $menus)): ?>
                            <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                            <td> <img src="<?php echo e(asset('/avatars/'.$user->user->profile_picture)); ?>" alt="avatar" style="width: 80px;border-radius: 50%; height: 80px;"> </td> 
                            <b style="display:none"><?php echo e($count = 1); ?></b>
                           
                            <td><?php echo e($user->user->name); ?><br>
                               <span style="color:blue"><?php echo e($user->user->email); ?></span> <br>
                               <?php echo e($user->user->t_phone); ?> <br>
                                <?php $__currentLoopData = $user->user->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <small style="color:green"><?php echo e($count ++); ?>.
                                    <?php echo e($programs->p_name); ?> <br></small>
                                    <hr style="margin-top: 2px; margin-bottom: 2px; border-top: 1px solid rgb(34, 85, 164);">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td><?php echo e($user->user->programs->count()); ?></td>
                            <?php if(in_array(20, $menus)): ?>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit User"
                                        class="btn btn-info" href="<?php echo e(route('users.edit', $user->user->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
<script>
        $(".delete").on("submit", function(){
            return confirm("Are you sure?");
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.teacher.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/users/index.blade.php ENDPATH**/ ?>