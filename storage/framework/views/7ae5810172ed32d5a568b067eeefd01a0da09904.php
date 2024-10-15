<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <?php if(Auth()->user()->role_id == "Admin"): ?>
        <div class="row">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        
        <div class="row">
            <!-- Column -->
            
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('programs.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-cyan text-center">
                        <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                        <h6 class="text-white"><b><?php echo e($programCount); ?></b> Training(s)</h6>
                    </div>
                </div>
                </a>
            </div>
        
            <!-- Column -->
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('materials.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white"><b><?php echo e(isset($materialCount) ? $materialCount : ''); ?></b> Material(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <!-- Column -->
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('payments.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-danger text-center">
                        <h1 class="font-light text-white"><i class="fas fa-dollar-sign"></i></h1>
                        <h6 class="text-white"><b> <?php echo e(isset($userowing) ? $userowing : ''); ?> </b> Owing</h6>
                    </div>
                </div>
                </a>
            </div>
            
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('users.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fa fa-users"></i></h1>
                        <h6 class="text-white"><?php echo e(isset($userCount) ? $userCount : ''); ?> Student(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('pop.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-danger text-center">
                        <h1 class="font-light text-white"><i class="fa fa-check-circle"></i></h1>
                        <h6 class="text-white"><b> <?php echo e(isset($pending_payments) ? $pending_payments : ''); ?> </b> Pending Payments</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('complains.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="far fa-comments"></i></h1>
                        <h6 class="text-white">CRM Tool</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('modules.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fa fa-edit"></i></h1>
                        <h6 class="text-white">LMS Management</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('users.mail')); ?>">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-envelope"></i></h1>
                        <h6 class="text-white">Send Emails</h6>
                    </div>
                </div>
                </a>
            </div>
        </div> 
    <?php endif; ?>   
    <div class="row">     
        <!-- Column -->
       
        <?php if(!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('teachers.students', Auth()->user()->id)); ?>">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fa fa-users"></i></h1>
                        <h6 class="text-white"><?php echo e($user->students_count ?? ''); ?> Student(s)</h6>
                    </div>
                </div>
                </a>
            </div>
             <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('teachers.programs', Auth()->user()->id)); ?>"">
                <div class="card card-hover">
                    <div class="box bg-primary text-center">
                        <h1 class="font-light text-white"><i class="fa fa-link"></i></h1>
                        <h6 class="text-white"><b><?php echo e($user->programCount); ?></b> Trainings(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('materials.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white"><b><?php echo e($materialCount); ?></b> Material(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('complains.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="far fa-comments"></i></h1>
                        <h6 class="text-white">CRM Tool</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="<?php echo e(route('modules.index')); ?>">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fa fa-edit"></i></h1>
                        <h6 class="text-white">LMS Management</h6>
                    </div>
                </div>
                </a>
            </div>
        <?php endif; ?>
        <!-- Column -->
        
    </div>
 
    
</div>
    <!-- BEGIN MODAL -->


<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/dashboard.blade.php ENDPATH**/ ?>