<?php $__env->startSection('title', 'Add Assessment score'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div>
                            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <p style="color:red"><strong>Here you can set score parameters for individual training assesment.</strong> <br><br>
                            NOTE: You cannot edit assessment parameters for a program whose Modules have been enabled. <br>Total Scores for all assessment parameter for an individual training cannot be more or less than 100%</p>
                            <h5>Add Assessment Parameter</h5> 
                        </div>
                    </div>

                    <form action="<?php echo e(route('scoreSettings.store')); ?>" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure?');" class="pb-2">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <!--Gives the first error for input name-->
                                <div class="form-group">
                                    <label for="class">Select Training</label>
                                    <select name="program" id="program" class="form-control" required>
                                        <option value="">Select an option</option>
                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($program->scoresettings_count <= 0): ?>
                                                <option value="<?php echo e($program->id); ?>"><?php echo e($program->p_name); ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('program_id')); ?></small></div>
                                </div>

                                <div class="form-group">
                                    <label>Set Maximum score for Class Tests<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="classtests" value="<?php echo e(old('classtests')); ?>"
                                        class=" form-control" min="0" max="100">
                                    <div><small style="color:red"><?php echo e($errors->first('classtests')); ?></small></div>
                                </div>
                                <div class="form-group">
                                    <label>Set Maximum score for Role Play<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="rolepalyscore" value="<?php echo e(old('rolepalyscore')); ?>"
                                        class="form-control" min="0" max="100">
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('rolepalyscore')); ?></small></div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Set Maximum score for Email<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="emailscore" value="<?php echo e(old('emailscore')); ?>"
                                        class="form-control" min="0" max="100">
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('emailscore')); ?></small></div>

                                <div class="form-group">
                                    <label>Set Maximum score for Certification<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="certificationscore" class="form-control" min="0"
                                        max="100" required value="<?php echo e(old('certificationscore')); ?>">
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('crm_test')); ?></small></div>
                                <div class="form-group">
                                    <label>Set Maximum score for CRM Test<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="crm_test" class="form-control" min="0"
                                        max="100" required value="<?php echo e(old('crm_test')); ?>">
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('certificationscore')); ?></small></div>

                                <div class="form-group">
                                    <label style="color:red">Set Pass Mark <span style="color:green">(Max score = 100%)</span></label>
                                    <input type="number" name="passmark" value="<?php echo e(old('passmark')); ?>"
                                        class="form-control" min="0" max="100" required>
                                </div>
                                <small><small style="color:red"><?php echo e($errors->first('passmark')); ?></small></small>
                            </div>
                        </div>
                        <div class="row">

                            <input type="submit" name="submit" value="Submit" class="btn btn-primary"
                                style="width:100%">
                        </div>

                </div>
            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?> 


<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/scoresettings/create.blade.php ENDPATH**/ ?>