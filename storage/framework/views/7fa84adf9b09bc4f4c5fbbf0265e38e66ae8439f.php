<?php $__env->startSection('title', 'Trainings'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                 <h5 class="card-title" style="color:green"> Click the eye icon to View grades for respective trainings </h5><br>
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
           
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Program Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($program->result_count > 0): ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($program->p_name); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-info" href="<?php echo e(route('results.getgrades', $program->program_id)); ?>"><i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/results/selecttraining.blade.php ENDPATH**/ ?>