<?php $__env->startSection('title', $user->name ); ?>
<?php $__env->startSection('css'); ?>
<style>
.select2-container--default.select2-container--focus .select2-selection--multiple {
    height: 200px !important;
}
</style>
   
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4>Edit details for: <?php echo e($user->name); ?></h4>
                    </div>
                    <form action="<?php echo e(route('users.update', $user->id)); ?>" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>

                          <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="<?php echo e(old('name') ?? $user->name); ?>"
                                        autofocus>
                                    <?php if($errors->has('name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email') ?? $user->email); ?>">
                                    <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group<?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone" value="<?php echo e(old('phone') ?? $user->t_phone); ?>"
                                        autofocus>
                                    <?php if($errors->has('phone')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('phone')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>   
                            </div>
                            <div class="col-md-6">
                                 <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                                    <label for="password">Password: </label><span class="help-block">
                                        <strong>Default: 12345</strong>
                                    </span>
                                    <input id="password" type="text" class="form-control" name="password" value="<?php echo e(old('password') ?? ''); ?>"
                                        autofocus>
                                    <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('class')); ?></small></div>
                               
                                <div class="form-group">
                                    <label for="class">Role *</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Student" <?php echo e(!empty(array_intersect(studentRoles(), $user->role())) ? 'selected' : ''); ?>>Student</option>
                                        <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                                        <option value="Teacher" <?php echo e($user->role_id == 'Teacher' ? 'selected' : ''); ?>>Facilitator</option>
                                        <option value="Grader" <?php echo e(!empty(array_intersect(graderRoles(), $user->role())) ? 'selected' : ''); ?>>Grader</option>
                                        <?php endif; ?>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('role')); ?></small></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="Male" <?php echo e($user->gender == 'Male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="Female" <?php echo e($user->gender == 'Female' ? 'selected' : ''); ?>>Female</option>
                        
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('gender')); ?></small></div>
                                </div>
                            </div>
                            <?php if(isset($associated)): ?>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;" required>
                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($program->id); ?>" <?php echo e(in_array($program->id, $associated) ? 'selected' : ''); ?> ><?php echo e($program->p_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                        <div>
                                        <?php if($errors->has('training')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('training')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                        </div>
                                    <div>
                                </div>
                            <?php endif; ?>
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
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/users/edit.blade.php ENDPATH**/ ?>