<?php $__env->startSection('title', 'Download materials'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            <h4 style="padding:10px">Click a Training below to manage its materials</h4>
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="card-body">
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Materials</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $facilitator_programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facilitator_program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view materials for this program"
                                    class="btn btn-info" href="<?php echo e(route( 'facilitatormaterials', ['p_id'=>$facilitator_program->program_id] )); ?>">
                                    <?php echo e($facilitator_program->p_name); ?>

                                </a>
                                </td>
                                <td><?php echo e($facilitator_program->materialCount); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
            
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/materials/show.blade.php ENDPATH**/ ?>