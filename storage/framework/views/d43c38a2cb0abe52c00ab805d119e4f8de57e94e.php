<?php $__env->startSection('title', 'Add study material'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4 class="card-title">Add new study Material for: <strong><?php echo e($program->p_name); ?></strong></h4>
                    </div>
                    <form action="<?php echo e(route('materials.store')); ?>" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <!--Gives the first error for input name-->

                        <div><small><?php echo e($errors->first('title')); ?></small></div>

                        <div class="form-group">
                            <label>Select files</label>
                            <input type="file" id="file" name="file[]" value="" class="form-control" multiple>
                        </div>
                        <div><small style="color:red"><?php echo e($errors->first('file[]')); ?></small></div>
                        <input type="hidden" name="p_id" value="<?php echo e($program->id); ?>">
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

                        <?php echo e(csrf_field()); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/materials/create.blade.php ENDPATH**/ ?>