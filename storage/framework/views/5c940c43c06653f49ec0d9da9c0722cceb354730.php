<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name') .' Test Management'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Select a Training to manage its modules. Trainings which have no modules will not appear here </h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Training</th>
                            <th>Modules</th> 
                            <th>Questions</th>                           
                        </tr>
                    </thead>
                    <tbody>
                         <?php $__currentLoopData = $programs_with_modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view modules for this training"
                                    class="btn btn-info" href="<?php echo e(route( 'facilitatormodules', ['p_id'=>$programs->program_id] )); ?>">
                                    <?php echo e($programs->p_name); ?>

                                </a>
                                </td>
                                <td><?php echo e($programs->modules_count); ?></td>
                                <td><?php echo e($programs->questions_count); ?></td>
                            </tr>
                            
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/modules/index.blade.php ENDPATH**/ ?>