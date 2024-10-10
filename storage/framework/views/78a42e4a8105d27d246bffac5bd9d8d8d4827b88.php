<?php $__env->startSection('css'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">
<style>
    a{
        text-decoration: none !important;
    }

    .accounts{
        min-height: 270px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<?php $__env->stopSection(); ?>
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
        <div class="col-md-12 col-lg-12">
            <div class="card-body">
                <h2 style="text-align: center; color:green">Trainings (Click to Access)</h2>
                <div class="row">
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-6 mb-4">
                            <a href="" class="text-decoration-none">
                                <div class="card card-hover shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <div class="box d-flex flex-column justify-content-center align-items-center text-center" style="background-color: #198754; min-height: 200px; padding: 20px;">
                                        <h1 class="font-light text-white mb-3">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </h1>
                                        <h5 class="text-white font-weight-bold mb-1"><?php echo e($detail->program->p_name); ?></h5>
                                        <p class="text-white-50 mb-0" style="font-size: 0.9rem;">10+ Registered Participants</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.company.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/company/dashboard.blade.php ENDPATH**/ ?>