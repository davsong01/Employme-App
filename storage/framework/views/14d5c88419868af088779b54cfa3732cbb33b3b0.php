<?php $__env->startSection('title', 'Add New Module'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <form action="<?php echo e(route('modules.store')); ?>" method="POST" class="pb-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Module Details</h4>
                                </div>
                                <div class="form-group<?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
                                    <label for="title">Title</label>
                                    <input id="title" type="text" class="form-control" name="title"
                                        value="<?php echo e(old('title')); ?>" autofocus required>
                                    <?php if($errors->has('title')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('title')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="class">Training</label>
                                    <input type="text" placeholder= "<?php echo e($program->p_name); ?>"  disabled  id="program" value="<?php echo e($program->p_name); ?>" class="form-control" >

                                    <input type="hidden" id="program" name="program" value="<?php echo e($program->id); ?>" class="form-control" required>

                                    <div><small style="color:red"><?php echo e($errors->first('program')); ?></small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                         <option value="" selected>-- Select Option --</option>
                                        <option value="0">Class Test</option>
                                        <option value="1">Certification Test</option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('type')); ?></small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="0" selected>Disabled</option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('status')); ?></small></div>
                                </div>

                                <div class="form-group<?php echo e($errors->has('noofquestions') ? ' has-error' : ''); ?>">
                                    <label for="noofquestions">No of Questions<small class = "cwarning" style="color:red"> <b>(You can only add 1 question for a certification text)</b> </small> </label>
                                    <input id="noofquestions" type="number" class="form-control" name="noofquestions"
                                        value="<?php echo e(old('noofquestions')); ?>" min="0" utofocus required>
                                    <?php if($errors->has('noofquestions')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('noofquestions')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group<?php echo e($errors->has('time') ? ' has-error' : ''); ?>">
                                    <label for="time">How many minutes for Module Questions(Min: 2minutes)</label>
                                    <input id="time" type="number" class="form-control" name="time"
                                        value="<?php echo e(old('time')); ?>" autofocus required min="2">
                                    <?php if($errors->has('time')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('time')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                        <?php echo e(csrf_field()); ?>

                </div>
            </div>
        </div>
        
    </div>
   
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/modules/create.blade.php ENDPATH**/ ?>