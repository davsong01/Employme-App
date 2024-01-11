<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <h2 style="color:green; text-align:center; padding:20px"><?php echo e(strtoupper($program->p_name)); ?></h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-lg-3">
            <a href="<?php echo e(route('payments.index')); ?>">
                <div class="card card-hover">
                    <div
                        class="box bg-<?php echo e($balance > 0 ? 'danger' : 'success'); ?> text-center">
                        <h1 class="font-light text-white"><i class="far fa-money-bill-alt"></i></h1>
                        <h6 class="text-white">Payment Status <?php echo e($currency_symbol); ?><?php echo e(number_format($balance)); ?> </h6>
                        <p class="text-white">Paid: <?php echo e($paid); ?> ; Balance:
                            <?php echo e($balance); ?> </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-lg-3">
            <a href="<?php echo e(route('materials.index', ['p_id'=> $program->id])); ?>">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white"><?php echo e($materialsCount); ?></h6>
                        <p class="text-white">Study materials</p>
                    </div>
                </div>
            </a>
        </div>
        <?php if($program->hasmock == 1): ?>
        <div class="col-md-3 col-lg-3">
            <a href="<?php echo e(route('mocks.index', ['p_id' => $program->id])); ?>">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fa fa-chalkboard"></i></h1>
                        <h6 class="text-white">&nbsp;</h6>
                        <p class="text-white">Pre Class Tests</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
        <div class="col-md-3 col-lg-3">
            <a href="<?php echo e(route('tests.index', ['p_id'=>$program->id])); ?>">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fas fa-question"></i></h1>
                        <h6 class="text-white">&nbsp;</h6>
                        <p class="text-white">Post class Tests</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
  
    <?php if(!$program->off_season): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title m-b-0">Training Progress</h4>
                    <div class="m-t-20">
                        <div class="d-flex no-block align-items-center">
                            <span><?php echo e($trainingProgress); ?>%</span>
                            <div class="ml-auto">
                                <span>100</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped" role="progressbar"
                                style="width: <?php echo e($trainingProgress); ?>%" aria-valuenow="10" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if(auth()->user()->facilitator_id): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title m-b-0">Your Facilitator</h1> <br><br>
                    <div class="row pt-2">
                        <div class="col-md-2">
                            <div class="d-flex no-block align-items-center">
                                <img src="<?php echo e(asset('profiles/'. auth()->user()->facilitator->profile_picture )); ?>" alt="<?php echo e(auth()->user()->facilitator->profile_picture); ?>" class="rounded-circle" width="150"
                                height="150" style="margin: auto;">
                               
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div>
                               
                                    <b>Name: </b><?php echo e(auth()->user()->facilitator->name); ?> <br>
                                    <b>Email: </b><?php echo e(auth()->user()->facilitator->email); ?> <br>
                                    <b>Phone: </b><?php echo e(auth()->user()->facilitator->t_phone); ?> <br>
                                    <b>Profile: </b> <br> <span style="padding-right:20px"><?php echo auth()->user()->facilitator->profile; ?></span> 
                                
                            </div>
                        </div>
                        
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/trainings.blade.php ENDPATH**/ ?>