<?php $__env->startSection('title', $user->name ); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4>Edit Profile</h4>
                    </div>
                    <form action="<?php echo e(route('profiles.update', $user->id)); ?>" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="<?php echo e(old('name') ?? $user->name); ?>" autofocus>
                                    <?php if($errors->has('name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                               
                                <div class="form-group<?php echo e($errors->has('t_phone') ? ' has-error' : ''); ?>">
                                    <label for="t_phone">Phone</label>
                                    <input id="t_phone" type="text" class="form-control" name="t_phone"
                                        value="<?php echo e(old('t_phone') ?? $user->t_phone); ?>" autofocus>
                                    <?php if($errors->has('t_phone')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('t_phone')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                                    <label for="password">Password: </label><span class="help-block">
                                        <strong>Default: 12345</strong> (Leave blank if you want to keep the default password)
                                    </span>
                                    <input id="password" type="text" class="form-control" name="password"
                                        value="<?php echo e(old('password') ?? ''); ?>" autofocus>
                                    <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group<?php echo e($errors->has('job_title') ? ' has-error' : ''); ?>">
                                    <label for="job_title">Job Title</label>
                                    <input id="job_title" type="text" class="form-control" name="job_title"
                                        value="<?php echo e(old('job_title') ?? $user->job_title); ?>" autofocus>
                                    <?php if($errors->has('job_title')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('job_title')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="<?php echo e(old('email') ?? $user->email); ?>" disabled>
                                    <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control" required>
                                        <option value="Male" <?php echo e($user->gender == 'Male' ? 'selected' : ''); ?>>Male</option>
                                        <option value="Female" <?php echo e($user->gender == 'Female' ? 'selected' : ''); ?>>Female
                                        </option>

                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('gender')); ?></small></div>
                                </div>  
                                <div class="form-group">
                                    <?php if(isset($user->profile_picture) && $user->profile_picture == "avatar.jpg"): ?>
                                    <label>Upload Profile Picture</label> <br>
                                    <img src="<?php echo e(url('/').'/profiles/avatar.jpg'); ?>" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                    <input type="file" name="image" value="<?php echo e(old('profile_picture') ??  $user->profile_picture); ?>" class="form-control">
                                    <?php else: ?>
                                    <label>Replace Profile Picture</label> <br>
                                    <img src="<?php echo e(asset('/avatars/'.$user->profile_picture)); ?>" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                        <input type="file" name="image" value="<?php echo e(old('image') ??  $user->profile_picture); ?>" class="form-control">
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
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/profiles/edit.blade.php ENDPATH**/ ?>