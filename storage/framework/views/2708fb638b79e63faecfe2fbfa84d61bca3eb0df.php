<?php $__env->startSection('title', $user->name ); ?>
<?php $__env->startSection('css'); ?>
<style>
    .select2-container--default .select2-selection--multiple {
        line-height: 27px;
        overflow: scroll;
        height: 150px;
    }
    .view{
        margin: 0 10px;
        border-radius: 10%;
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
                        <h4 class="card-title"><?php echo e($user->name); ?></h4>
                         <?php if(!empty(array_intersect(facilitatorRoles(), $user->role()))): ?>  
                        <p>Referal link: <b id="link" style="color:blue"><?php echo e(url('/') .'/'.'?facilitator='. $user->license); ?></b> <br>
                            WAACSP Profile link: <b><?php echo e($user->waaccsp_link); ?></b>
                        </p>
                        <?php endif; ?>
                    </div>
                    <form action="<?php echo e(route('teachers.update', $user->id)); ?>" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <img src="<?php echo e($user->image); ?>" alt="avatar" class="rounded-circle" width="100" height="100">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <table class="table table-bordered">
                                        <th><strong>Trainings</strong><a href="<?php echo e(route('teachers.programs', $user->id)); ?>" target="_blank" class="btn btn-info btn-sm view"> View</a></th>
                                        <?php if(!empty(array_intersect(facilitatorRoles(), $user->role()))): ?>
                                        <th><strong>Students</strong><a href="<?php echo e(route('teachers.students', $user->id)); ?>" class="btn btn-info btn-sm view" target="_blank"> View</a></th>
                                        <th><strong>WTN License</strong></th>
                                        <th><strong>Off season</strong></th>
                                        <th><strong>Total Earnings</strong> <a href="<?php echo e(route('teachers.earnings', $user->id)); ?>" class="btn btn-info btn-sm view" target="_blank"> View</a> </th>
                                        <?php endif; ?>
                                        <tr>
                                           <td><?php echo e($programs->count()); ?></td>
                                           <?php if(!empty(array_intersect(facilitatorRoles(), $user->role()))): ?>    
                                           <td><?php echo e($user->students_count); ?> </td>
                                           <td><?php echo e($user->license); ?></td>
                                           <td><?php echo e($user->off_season_availability == 1 ? 'Yes' : 'No'); ?></td>
                                           <td><?php echo e($user->payment_modes->currency_symbol ?? 'NGN'); ?><?php echo e(number_format($user->earnings)); ?></td>
                                           <?php endif; ?>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                           
                        </div>
                         <div class="row" style="margin-top:20px">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role*</label>
                                    <select name="role[]" id="role" class="select2 role form-control" multiple="multiple" style="height: 30px;width: 100%;">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Facilitator" <?php echo e(!empty(array_intersect(facilitatorRoles(), $user->role()))  ? 'selected' : ''); ?>>Facilitator</option>
                                        <option value="Grader" <?php echo e(!empty(array_intersect(graderRoles(), $user->role())) ? 'selected' : ''); ?>>Grader</option>
                                        <option value="Admin" <?php echo e(!empty(array_intersect(adminRoles(), $user->role())) ? 'selected' : ''); ?>>Admin</option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('role')); ?></small></div>
                                </div>
                                <div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
                                    <label for="status">Status</label>
                                    <select name="status" id="type" class="form-control" required>
                                        <option value="active" <?php echo e($user->status == 'active' ? 'selected':''); ?>>Active</option> 
                                        <option value="inactive" <?php echo e($user->status == 'inactive' ? 'selected':''); ?>>Inactive</option>
                                    </select>
                                </div>
                                <?php if(!empty(array_intersect(facilitatorRoles(), $user->role()))): ?>  
                                <div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode" class="form-control" required>
                                        <option value="">...Select</option>
                                        <?php $__currentLoopData = $payment_modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($mode->id); ?>" <?php echo e($user->payment_mode == $mode->id ? 'selected':''); ?>><?php echo e(ucFirst($mode->name)); ?></option> 
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="<?php echo e(old('name') ?? $user->name); ?>" autofocus >
                                    <?php if($errors->has('name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                
                            </div>
                            <div class="col-md-6">
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
                                    <input id="phone" type="phone" class="form-control" name="phone" value="<?php echo e(old('phone') ?? $user->t_phone); ?>">
                                    <?php if($errors->has('phone')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('phone')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label>Change Profile Picture</label>
                                    <input type="file" name="file" value="" class="form-control">
                                </div>
                                
                            
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
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group<?php echo e($errors->has('profile') ? ' has-error' : ''); ?>">
                                    
                                    <label for="profile" style="color:red">Profile overview</label>
                                    <textarea id="ckeditor" type="text" class="form-control" name="profile" value="<?php echo e(old('profile') ?? $user->profile); ?>" rows="8" autofocus><?php echo $user->profile; ?></textarea>

                                    <?php if($errors->has('profile')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('profile')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;">
                                        <?php $__currentLoopData = $allprograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allprogram): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                             <option value="<?php echo e($allprogram->id); ?>" <?php echo e(in_array($allprogram->id, $user->trainings->pluck('program_id')->toArray()) ? 'selected' : ''); ?>><?php echo e($allprogram->p_name); ?></option>
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
                        </div>
                         <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12">
                               <span><h6>Admin Menu Permissions</h6></span>
                            </div>
                            <?php
                                $a_menu = $user->menu_permissions ?? '';
                                $a_menu = explode(',', $a_menu);
                            ?>
                            <?php $__currentLoopData = app('app\Http\Controllers\Controller')->adminMenus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="menu_permissions[]" value="<?php echo e($menu['id']); ?>" id="<?php echo e($menu['id']); ?>" <?php echo e(in_array($menu['id'], $a_menu) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="<?php echo e($menu['id']); ?>">
                                        <?php echo e($menu['name']); ?>

                                    </label>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                       <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                      
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('ckeditor');
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/teachers/edit.blade.php ENDPATH**/ ?>