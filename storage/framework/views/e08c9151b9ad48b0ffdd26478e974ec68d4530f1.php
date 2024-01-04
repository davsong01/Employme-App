<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Column -->
        <div class="col-md-12 col-lg-12">
            <a href="<?php echo e(route('profiles.edit', Auth::user()->id)); ?>">
                <div class="card card-hover">
                    <div class="box bg-cyan text-center">
                        <h1 class="font-light text-white"><i class="fas fa-user-edit"></i></h1>
                        <h6 class="text-white">Welcome, <?php echo e(Auth::user()->name); ?></h6>
                        <p class="text-white">Edit my profile</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
    </div>

    <div class="row">
        <!-- Column -->
        <div class="col-md-12 col-lg-12">
            <div class="card-body">
                <h2 style="text-align: center; color:green">My Trainings (Click to Access)</h2>
                <?php $__currentLoopData = $thisusertransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-12 col-lg-12">
                        <a href="<?php echo e(route('trainings.show', ['p_id' => $details->p_id])); ?>">
                            <div class="card card-hover">
                                <div class="box bg-success text-center">
                                    <h1 class="font-light text-white"><i class="fas fa-chalkboard-teacher"></i></h1>
                                    <h6 class="text-white"><?php echo e($details->p_name); ?></h6>
                                    <p class="text-white"><?php echo e($details->modules); ?> Enabled Module Tests | <?php echo e($details->materials); ?> Materials</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <!-- Column -->
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/dashboard.blade.php ENDPATH**/ ?>