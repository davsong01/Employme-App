<?php $__env->startSection('title', 'Add New question'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <form action="<?php echo e(route('questions.store')); ?>" method="POST" class="pb-2">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div>
                                    <h4>Question Details</h4>
                                </div>
                                <div class="form-group">
                                    <label for="class">Associated Module</label>
                                    <select name="module" id="module" class="form-control" required>
                                        <option value="">Select Option</option>
                                        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($module->questions_count < $module->noofquestions): ?>
                                        <option value="<?php echo e($module->id); ?>"><?php echo e($module->title); ?> (<?php echo e($module->noofquestions - $module->questions_count .' question(s) left to complete'); ?>)</option>
                                      
                                        <?php endif; ?>
                                       
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('program')); ?></small></div>
                                </div>

                                <div class="form-group<?php echo e($errors->has('title') ? ' has-error' : ''); ?>">
                                    <label for="title">Title</label>
                                    <textarea id="title" type="text" class="form-control" name="title" value="<?php echo e(old('title')); ?>" autofocus required><?php echo e(old('title')); ?></textarea>
                                    <?php if($errors->has('title')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('title')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <h5>Ignore the sections below if question is an open ended question</h5>
                                <div class="form-group<?php echo e($errors->has('optionA') ? ' has-error' : ''); ?>">
                                    <label for="optionA">Option A</label>
                                    <input id="optionA" type="text" class="form-control" name="optionA"
                                        value="<?php echo e(old('optionA')); ?>" autofocus>
                                    <?php if($errors->has('optionA')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('optionA')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group<?php echo e($errors->has('optionB') ? ' has-error' : ''); ?>">
                                    <label for="optionB">Option B</label>
                                    <input id="optionB" type="text" class="form-control" name="optionB"
                                        value="<?php echo e(old('optionB')); ?>" autofocus>
                                    <?php if($errors->has('optionB')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('optionB')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group<?php echo e($errors->has('optionC') ? ' has-error' : ''); ?>">
                                    <label for="optionC">Option C</label>
                                    <input id="optionC" type="text" class="form-control" name="optionC"
                                        value="<?php echo e(old('optionC')); ?>" autofocus>
                                    <?php if($errors->has('optionC')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('optionC')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group<?php echo e($errors->has('optionD') ? ' has-error' : ''); ?>">
                                    <label for="optionD">Option D</label>
                                    <input id="optionD" type="text" class="form-control" name="optionD"
                                        value="<?php echo e(old('optionD')); ?>" autofocus>
                                    <?php if($errors->has('optionD')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('optionD')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="class" style="color:red">Which option above is the correct one?</label>
                                    <select name="correct" id="correct" class="form-control">
                                        <option value="">Slect Option</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('correct')); ?></small></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        CKEDITOR.replace('title');
    </script>
 
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/questions/create.blade.php ENDPATH**/ ?>