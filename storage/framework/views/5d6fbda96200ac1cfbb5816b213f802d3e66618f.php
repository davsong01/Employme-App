<?php $__env->startSection('title', 'All Users'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Students <a href="<?php echo e(route('users.create')); ?>"><button type="button"
                        class="btn btn-outline-primary">Add New Student</button></a><button class="btn btn-success" id="csv">Export Students</button></h5>
                </div>
            </div>

            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Trainings</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <tr>
                            
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                            <td> <img src="<?php echo e(asset('/avatars/'.$user->profile_picture)); ?>" alt="avatar" style="width: 80px;border-radius: 50%; height: 80px;"> </td> 

                            <b style="display:none"><?php echo e($count = 1); ?></b>
                            <td>
                                <?php echo e($user->name); ?> <br>
                                <?php $__currentLoopData = $user->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <small style="color:green"><?php echo e($count ++); ?>.
                                    <?php echo e($programs->p_name); ?> <br></small>
                                    <hr style="margin-top: 2px; margin-bottom: 2px; border-top: 1px solid rgb(34, 85, 164);">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td> 
                            <td><?php echo e($user->t_phone); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->programs()->count()); ?></td>
                           
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit User"
                                        class="btn btn-info" href="<?php echo e(route('users.edit', $user->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                        class="btn btn-warning" href="<?php echo e(route('impersonate', $user->id)); ?>"><i
                                            class="fa fa-unlock"></i>
                                    </a>
                                    
                                    <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <script type="text/javascript" src="<?php echo e(asset('src/jspdf.min.js')); ?> "></script>
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/jspdf.plugin.autotable.min.js'
                    )); ?>"></script>
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/tableHTMLExport.js')); ?>"></script>
                    
                    <script type="text/javascript">
                                           
                      $("#csv").on("click",function(){
                        $("#zero_config").tableHTMLExport({
                          type:'csv',
                          filename:'Participants.csv'
                        });
                      });
                    
                    </script>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/users/index.blade.php ENDPATH**/ ?>