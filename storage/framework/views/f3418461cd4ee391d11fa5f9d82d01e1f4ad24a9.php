<?php $__env->startSection('title', 'View/Update Module'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                       <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <form action="<?php echo e(route('modules.update', $module->id)); ?>" method="POST" class="pb-2">
                        <?php echo e(csrf_field()); ?>

                        <?php echo e(method_field('PATCH')); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Module Details</h4>
                                </div>
                                <div class="form-group<?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
                                    <label for="title">Title</label>
                                    <input id="title" type="text" class="form-control" name="title" value="<?php echo e(old('title') ?? $module->title); ?>" autofocus required>
                                    <?php if($errors->has('title')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('title')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                        
                                <div class="form-group">
                                    <label for="class">Training</label>
                                    <input type="text" readonly  id="program" name="program_id" value="<?php echo e($program->id); ?>" placeholder= "<?php echo e($program->p_name); ?>" class="form-control" required
                                    <div><small style="color:red"><?php echo e($errors->first('program')); ?></small></div>
                                </div>
                            </div>
                        
                                <div class="form-group">
                                    <label for="class">Type</label>
                                    <?php if( $module->questions->count() <= 0 ): ?>
                                        <select name="type" id="class" class="form-control" required>
                                        <option value="0" <?php echo e($module->type == 'Class Test' ? 'selected' : ''); ?>>Class Test</option>
                                            <option value="1" <?php echo e($module->type == 'Certification Test' ? 'selected' : ''); ?>>Certification Test</option>
                                        </select>
                                    <?php else: ?>
                                        <select name="type" id="class" class="form-control" required readonly>
                                            <option value="<?php echo e($module->type == "Class Test" ?? 0); ?> <?php echo e($module->type == 'Certification Test' ?? 1); ?>"><?php echo e($module->type); ?></option>
                                        </select>
                                    <?php endif; ?>
                                    <div><small style="color:red"><?php echo e($errors->first('type')); ?></small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="class" class="form-control">
                                    <option value="0" <?php echo e($module->status == 0 ? 'selected' : ''); ?>>Disabled</option>
                                        <option value="1" <?php echo e($module->status == 1 ? 'selected' : ''); ?>>Enabled</option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('status')); ?></small></div>
                                </div>
                        
                                <div class="form-group<?php echo e($errors->has('noofquestions') ? ' has-error' : ''); ?>">
                                    <label for="noofquestions">No of Questions</label>
                                    <input id="noofquestions" type="number" class="form-control" name="noofquestions" value="<?php echo e(old('noofquestions') ?? $module->noofquestions); ?>" autofocus required>
                                    <?php if($errors->has('noofquestions')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('noofquestions')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                        
                                <div class="form-group<?php echo e($errors->has('time') ? ' has-error' : ''); ?>">
                                    <label for="time">How many minutes for Module Questions(Min: 2minutes)</label>
                                    <input id="time" type="number" class="form-control" name="time" value="<?php echo e(old('time') ?? $module->time); ?>" autofocus required min="2">
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
                                Update
                            </button>
                        </div>
                      

                </div>
            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/modules/edit.blade.php ENDPATH**/ ?>