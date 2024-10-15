<?php $__env->startSection('title', 'Update Assessment settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div>
                            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <h3>Add Assessment Parameter</h3>
                            <h5>Update score parameters for: <?php echo e($scoreSetting->program->p_name); ?></h5>
                                    <p style="color:red"><br>Total Scores for all assessment parameter for an individual training cannot be
                                    more than 100%</p>
                        </div>
                    </div>

                    <form action="<?php echo e(route('scoreSettings.update', $scoreSetting->id)); ?>" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <?php echo e(csrf_field()); ?>

                        <?php echo e(method_field('PATCH')); ?>

                            <input type="hidden" name="program" value="<?php echo e(old('program') ?? $scoreSetting->program->id); ?>" placeholder="<?php echo e($scoreSetting->program->id); ?>" class=" form-control"
                                min="0" max="100" required readonly>
                            
                        <div class="form-group">
                            <label>Set Maximum score for Class Tests<span style="color:green">(Max score =
                                    100)</span></label>
                            <input type="number" name="classtests" value="<?php echo e(old('classtests') ?? $scoreSetting->class_test); ?>" class=" form-control"
                                min="0" max="100">
                            <div><small style="color:red"><?php echo e($errors->first('classtests')); ?></small></div>
                        </div>
                        <div class="form-group">
                            <label>Set Maximum score for Role Play<span style="color:green">(Max score =
                                    100)</span></label>
                            <input type="number" name="roleplayscore" value="<?php echo e(old('roleplayscore') ?? $scoreSetting->role_play); ?>"
                                class="form-control" min="0" max="100">
                        </div>
                        <div><small style="color:red"><?php echo e($errors->first('roleplayscore')); ?></small></div>
                        
                    <div class="form-group">
                        <label>Set Maximum score for Email<span style="color:green">(Max score = 100)</span></label>
                        <input type="number" name="emailscore" value="<?php echo e(old('emailscore') ?? $scoreSetting->email); ?>" class="form-control"
                            min="0" max="100">
                    </div>
                    <div><small style="color:red"><?php echo e($errors->first('emailscore')); ?></small></div>

                    <div class="form-group">
                        <label>Set Maximum score for Certification<span style="color:green">(Max score =
                                100)</span></label>
                        <input type="number" value="<?php echo e(old('certificationscore') ?? $scoreSetting->certification); ?>" name="certificationscore" class="form-control" min="0" max="100">
                    </div>
                    <div><small style="color:red"><?php echo e($errors->first('certificationscore')); ?></small></div>

                    <div class="form-group">
                        <label>Set Maximum score for CRM<span style="color:green">(Max score =
                                100)</span></label>
                        <input type="number" value="<?php echo e(old('crm_test') ?? $scoreSetting->crm_test); ?>" name="crm_test" class="form-control" min="0" max="100">
                    </div>
                    <div><small style="color:red"><?php echo e($errors->first('crm_test')); ?></small></div>


                    <div class="form-group">
                        <label>Set Pass Mark <span style="color:green">(Max score = 100)</span></label>
                        <input type="number" name="passmark" value="<?php echo e(old('passmark') ?? $scoreSetting->passmark); ?>" class="form-control" min="0"
                            max="100" required>
                    </div>
                    <small><small style="color:red"><?php echo e($errors->first('passmark')); ?></small></small>
                </div>
            </div>
            <div class="row">

                <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">
            </div>

        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/scoresettings/edit.blade.php ENDPATH**/ ?>