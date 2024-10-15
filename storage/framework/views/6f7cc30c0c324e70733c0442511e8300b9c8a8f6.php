<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name') .' Test Management'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
        <a href="<?php echo e(route('modules.index')); ?>">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> <?php echo e($modules->count()); ?> Module(s)</h6>
                </div>
            </div>
        </a>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
        <a href="<?php echo e(route('questions.index')); ?>">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                <h6 class="text-white"><b></b> <?php echo e($questions_count); ?> Questions</h6>
                </div>
            </div>
        </a>
        </div>
        <div class="col-md-3 col-lg-3">
            <a href="<?php echo e(route('results.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fas fa-user-graduate"></i></h1>
                    <h6 class="text-white"><b></b> Grades </h6>
                    </div>
                </div>
            </a>
            </div>
        <?php if(Auth()->user()->role_id == "Admin"): ?>
        <div class="col-md-3 col-lg-3">
        <a href="<?php echo e(route('scoreSettings.index')); ?>">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-cog"></i></h1>
                <h6 class="text-white"><b></b> Score Settings </h6>
                </div>
            </div>
        </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Select a Training to manage its modules </h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Training</th>
                            <th>Modules</th> 
                            <th>Questions</th>                           
                        </tr>
                    </thead>
                    <tbody>
                         <?php $__currentLoopData = $programs_with_modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i++); ?></td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view modules for this training"
                                    class="btn btn-info" href="<?php echo e(route( 'facilitatormodules', ['p_id'=>$programs->id] )); ?>">
                                    <?php echo e($programs->p_name); ?>

                                </a>
                                </td>
                                <td><?php echo e($programs->modules->count()); ?></td>
                                <td><?php echo e($programs->questions->count()); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/modules/index.blade.php ENDPATH**/ ?>