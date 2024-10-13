<?php $__env->startSection('css'); ?>
<style>
    a{
        text-decoration: none !important;
    }

    .accounts{
        min-height: 270px;
    }

    .badge {
        display: inline !important;
        padding: 10px;
    }
</style>
<?php echo $__env->make('dashboard.company.partials.company_extra_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'All Participants'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-header">
                    <div class="card-title">
                        <h5>
                            All Participants
                            <span class="badge bg-success ms-2">
                                <span class="transaction-count"><?php echo e($records); ?></span>
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                            $currentStatus = request('status');
                        ?>
                    
                        <div class="mt-4">
                            <form class="row g-3 search-form" method="GET" action="<?php echo e(route('company.participants')); ?>">
                                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                                <div class="col-md-4 mb-2">
                                    <label for="program_id" class="form-label">Training</label>
                                    <select name="program_id" id="program_id" class="form-control">
                                        <option value="">Select Training</option>
                                        <?php $__currentLoopData = $allPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($training->id); ?>"><?php echo e($training->p_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo e(request('name')); ?>">
                                </div>
                            
                                <div class="col-md-2 mb-2">
                                    <label for="staffID" class="form-label">Staff ID</label>
                                    <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="<?php echo e(request('staffID')); ?>">
                                </div>
                                
                                <div class="col-md-2 mb-2">
                                    <label for="" class="form-label" style="color:transparent;display:block">Name</label>
                                    <button type="submit" class="btn btn-primary mb-2 rounded">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Trainings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($user->name); ?> <br>
                                <?php if($user->staffID): ?>
                                <strong>Staff ID: </strong><?php echo e($user->staffID); ?> <br>
                                <?php endif; ?>
                            </td>

                            <b style="display:none"><?php echo e($count = 1); ?></b>
                            <td>
                                <?php $__currentLoopData = $user->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(in_array($prog->id, $programs)): ?>
                                        <small style="color:green"><?php echo e($count ++); ?>.
                                        <?php echo e($prog->p_name); ?> <br></small>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($users->appends($_GET)->links()); ?>


        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.company.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/company/users/index.blade.php ENDPATH**/ ?>