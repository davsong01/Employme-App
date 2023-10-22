<?php $__env->startSection('title', 'Add User'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4>Add new User</h4>
                    </div>
                    <form action="<?php echo e(route('users.store')); ?>" method="POST" enctype="multipart/form-data" class="pb-2">
                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">

                                    <label for="name">Name</label>

                                    <input id="name" type="text" class="form-control" name="name"
                                        value="<?php echo e(old('name')); ?>" autofocus required>

                                    <?php if($errors->has('name')): ?>

                                    <span class="help-block">

                                        <strong><?php echo e($errors->first('name')); ?></strong>

                                    </span>

                                    <?php endif; ?>

                                </div>

                                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">

                                    <label for="email">E-Mail Address</label>

                                    <input id="email" type="email" class="form-control" name="email"
                                        value="<?php echo e(old('email')); ?>" required>

                                    <?php if($errors->has('email')): ?>

                                    <span class="help-block">

                                        <strong><?php echo e($errors->first('email')); ?></strong>

                                    </span>

                                    <?php endif; ?>

                                </div>

                                <div class="form-group<?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">

                                    <label for="phone">Phone</label>

                                    <input id="phone" type="text" class="form-control" name="phone"
                                        value="<?php echo e(old('phone')); ?>" autofocus required>

                                    <?php if($errors->has('phone')): ?>

                                    <span class="help-block">

                                        <strong><?php echo e($errors->first('phone')); ?></strong>

                                    </span>

                                    <?php endif; ?>

                                </div>

                                <div class="form-group<?php echo e($errors->has('location') ? ' has-error' : ''); ?>">

                                    <label for="location">Location </label>
                                    <select name="location" id="location" class="form-control">

                                        <option value="">-- Select Option --</option>
                                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($location->id); ?>" <?php echo e(old('location') == $location->id ? 'selected' : ''); ?>>
                                            <?php echo e($location->title); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </select>
                                    <?php if($errors->has('location')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('location')); ?></strong>
                                    </span>
                                    <?php endif; ?>

                                </div>

                                <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">

                                    <label for="password">Password</label>

                                    <input id="password" type="text" class="form-control" name="password"
                                        value="<?php echo e(old('password') ?? 12345); ?>" autofocus>

                                    <?php if($errors->has('password')): ?>

                                    <span class="help-block">

                                        <strong><?php echo e($errors->first('password')); ?></strong>

                                    </span>

                                    <?php endif; ?>

                                </div>

                                <div><small style="color:red"><?php echo e($errors->first('class')); ?></small></div>

                                <div class="form-group">

                                    <label for="training">Select Training *</label>

                                    <select name="training" id="training" class="form-control">

                                        <option value="">-- Select Option --</option>

                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <option value="<?php echo e($program->id); ?>" <?php echo e(old('training') == $program->id ? 'selected' : ''); ?> required>

                                            <?php echo e($program->p_name); ?>| <strong>(<?php echo e(\App\Settings::value('DEFAULT_CURRENCY').number_format($program->p_amount)); ?>)</strong></option>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </select>

                                    <?php if($errors->has('training')): ?>

                                    <span class="help-block">

                                        <strong><?php echo e($errors->first('training')); ?></strong>

                                    </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label>Amount Paid *</label>

                                    <input type="number" name="amount" value="<?php echo e(old('amount')); ?>" min="0"
                                        class="form-control" required>

                                </div>

                                <div><small style="color:red"><?php echo e($errors->first('amount')); ?></small></div>

                                <div class="form-group">

                                    <label>Bank *</label>

                                    <input type="text" name="bank" value="<?php echo e(old('bank')); ?>" class="form-control" required>

                                </div>

                                <?php if($errors->has('bank')): ?>

                                <span class="help-block">

                                    <strong><?php echo e($errors->first('bank')); ?></strong>

                                </span>

                                <?php endif; ?>

                                <div class="form-group">

                                    <label for="class">Role *</label>

                                    <select name="role" id="class" class="form-control">

                                        <option value="" disabled>Assign Role</option>

                                        <option value="Student">Student</option>

                                    </select>

                                    <div><small style="color:red"><?php echo e($errors->first('role')); ?></small></div>

                                </div>

                                <div class="form-group<?php echo e($errors->has('transaction_id') ? ' has-error' : ''); ?>">

                                    <label for="transaction_id">Transaction Id</label>

                                    <input id="transaction_id" type="text" class="form-control" name="transaction_id"
                                        value="<?php echo e(old('transaction_id')); ?>" autofocus>

                                    <?php if($errors->has('transaction_id')): ?>

                                    <span class="help-block">

                                        <strong><?php echo e($errors->first('transaction_id')); ?></strong>

                                    </span>

                                    <?php endif; ?>

                                </div>

                                <div class="form-group">

                                    <label for="class">Gender</label>

                                    <select name="gender" id="class" class="form-control">

                                        <option value="" disabled>Select gender</option>

                                        <option value="Male" <?php echo e(old('gender') == 'Male' ? 'selected' : ''); ?>>Male</option>

                                        <option value="Female" <?php echo e(old('gender') == 'Female' ? 'selected' : ''); ?>>Female</option>



                                    </select>

                                    <div><small style="color:red"><?php echo e($errors->first('gender')); ?></small></div>

                                </div>

                                <div class="form-group">

                                    <label for="class">Bypass EarlyBird Check</label>

                                    <input type="checkbox" name="earlybird"> <small style="color:red"> Check this only amount after Earlybird has expired</small>

                                    <div><small style="color:red"><?php echo e($errors->first('earlybird')); ?></small></div>

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
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/users/create.blade.php ENDPATH**/ ?>