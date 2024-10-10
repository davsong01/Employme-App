<?php $__env->startSection('title', 'Add Company Admin'); ?>
<?php $__env->startSection('css'); ?>
<style>
    .access-column {
        width: 250px;
    }

    fieldset {
        border: 1px solid green;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        background-color: #f9f9f9;
    }

    legend {
        font-weight: bold;
        color: green;
        padding: 0 10px;
        font-size: 1.1em;
    }

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <div class="card-title">
                        <h4 class="card-title">Add new Company Admin</h4>
                    </div>
                    <form action="<?php echo e(route('companyuser.store')); ?>" method="POST" enctype="multipart/form-data" class="pb-2">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Personal Information Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Personal Information</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                        <label for="name">Name</label>
                                        <input required id="name" type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                        <label for="email">Email Address</label>
                                        <input required id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
                                        <label for="phone">Phone</label>
                                        <input id="phone" type="text" class="form-control" name="phone" value="<?php echo e(old('phone')); ?>" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('job_title') ? ' has-error' : ''); ?>">
                                        <label for="job_title">Job Title</label>
                                        <input id="job_title" type="text" class="form-control" name="job_title" value="<?php echo e(old('job_title')); ?>" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="">Select...</option> 
                                            <option value="active" selected>Active</option> 
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('gender') ? ' has-error' : ''); ?>">
                                        <label for="gender">gender</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">Select...</option> 
                                            <option value="Male" selected>Male</option> 
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('profile_picture') ? ' has-error' : ''); ?>">
                                        <label for="profile_picture">Profile Picture</label>
                                        <input id="profile_picture" type="file" class="form-control" name="profile_picture" value="<?php echo e(old('profile_picture')); ?>" autofocus>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Menu Permissions Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Menu Permissions</legend>
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-md-12">
                                    <h6>Select Menu Permissions</h6>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <?php $__currentLoopData = $menuStructure; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input required class="form-check-input parent-checkbox" type="checkbox" name="permissions[]" value="<?php echo e($menu['id']); ?>" id="menu-<?php echo e($menu['id']); ?>">
                                                    <label class="form-check-label" for="menu-<?php echo e($menu['id']); ?>">
                                                        <?php echo e($menu['name']); ?>

                                                    </label>
                                                </div>
                                                
                                                <?php if(!empty($menu['children'])): ?>
                                                    <div class="ml-4">
                                                        <?php $__currentLoopData = $menu['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input child-checkbox-<?php echo e($menu['id']); ?>" type="checkbox" name="permissions[]" value="<?php echo e($child['id']); ?>" id="menu-<?php echo e($child['id']); ?>">
                                                                <label class="form-check-label" for="menu-<?php echo e($child['id']); ?>">
                                                                    <?php echo e($child['name']); ?>

                                                                </label>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Trainings and Access Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Trainings and Access</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Select Trainings</h6>
                                </div>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6 mb-3">
                                        <table class="table">
                                            <tr>
                                                <td>
                                                    <input type="checkbox" id="program<?php echo e($program->id); ?>" name="trainings[<?php echo e($program->id); ?>][selected]" value="1">
                                                </td>
                                                <td>
                                                    <label for="program<?php echo e($program->id); ?>"><?php echo e($program->p_name); ?></label>
                                                </td>
                                                <td class="access-column">
                                                    <select name="trainings[<?php echo e($program->id); ?>][access]" id="training<?php echo e($program->id); ?>" class="form-control">
                                                        <option value="">Select...</option>
                                                        <option value="all_access">All Access</option>
                                                        <option value="limited_access">Limited Access</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </fieldset>

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
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-scripts'); ?>
<script>
    document.querySelectorAll('.parent-checkbox').forEach(parentCheckbox => {
        parentCheckbox.addEventListener('change', function() {
            const parentId = this.value;
            const childCheckboxes = document.querySelectorAll(`.child-checkbox-${parentId}`);
            
            childCheckboxes.forEach(childCheckbox => {
                childCheckbox.checked = this.checked;
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/company/create.blade.php ENDPATH**/ ?>