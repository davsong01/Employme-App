<?php $__env->startSection('title'); ?>
<?php echo e(config('app.name')); ?> Test Management
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
     <div class="card">
        <div class="card-body">
           
            <div class="card-header">
                <div>
                    <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <p style="color:red"><strong>Here you can set score parameters for individual training assesment. Please use the default values except you know what you are doing.</strong> <br><br>
                    NOTE: You cannot edit assessment parameters for a program whose Modules have been enabled. <br>Total Scores for all assessment parameter for an individual training cannot be more than 100%</p>
                    <h5 class="card-title"> All Questions <a href="<?php echo e(route('scoreSettings.create')); ?>"><button type="button" class="btn btn-outline-primary">Add New Assessment Parameter</button></a></h5> 
                </div>
            </div>
            
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Training</th>
                            <th>Enabled Modules</th>
                            <th>Class Tests</th> 
                            <th>Role Play Score</th>
                            <th>Email Score</th>
                            <th>Certification Score</th>
                            <th>CRM Score</th>
                            <th>Total</th>
                             <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $scores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($score->program->p_name); ?></td>
                            <td><?php echo e($score->module_status_count); ?></td>
                            <td><?php echo e($score->class_test ?? 0); ?>%</td>
                            <td><?php echo e($score->role_play ?? 0); ?>%</td>
                            <td><?php echo e($score->email ?? 0); ?>%</td>
                            <td><?php echo e($score->certification ?? 0); ?>%</td>
                            <td><?php echo e($score->crm_test ?? 0); ?>%</td>
                            <td><?php echo e($score->total); ?></td>
                            <td>
                                <div class="btn-group">
                                  

                                    <form action="<?php echo e(route('scoreSettings.destroy', $score->id)); ?>" method="POST"
                                        onsubmit="return confirm('Do you really want to Delete forever?');">
                                        
                                        
                                        <a data-toggle="tooltip" data-placement="top" title="Edit"
                                            class="btn btn-info btn-sm" href="<?php echo e(route('scoreSettings.edit', $score->id)); ?>"><i
                                                class="fa fa-edit"></i>
                                        </a>
                                        
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>

                                        <?php if($score->module_status_count <= 0): ?>
                                        <button type="submit" class="btn btn-warning btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete scores"> <i class="fa fa-trash"></i>
                                        </button>
                                        <?php else: ?> N/A
                                        <?php endif; ?>
            
                                        
                                    </form>
                                </div>

                            </td>
                        </tr> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $(".delete").on("submit", function () {
        return confirm("Are you sure?");
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/scoresettings/index.blade.php ENDPATH**/ ?>