<?php $__env->startSection('dashboard'); ?>

<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                <?php if((Auth::user()->isImpersonating()) ): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        style="color:yellow !important; font-weight:bolder"
                        href="<?php echo e(route('stop.impersonate.facilitator')); ?>" aria-expanded="false"><i
                            class="fa fa-arrow-left"></i><span class="hide-menu">BACK TO ADMIN</span></a></li>
                <?php endif; ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(url('dashboard')); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Teacher Dashboard</span></a></li>
                <!---Student management links-->
              
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-users"></i><span
                            class="hide-menu">Student Management </span></a>
                    <ul aria-expanded="false" class="collapse  first-level" style="margin-left:30px">                    
                        <li class="sidebar-item"><a href="<?php echo e(route('users.index')); ?>" class="sidebar-link"><span class="hide-menu">- View your students </span></a>
                        </li>
                    </ul>
                </li>
               
                
        <!---end of class management links-->
                 <!---Class management links-->
             <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                href="javascript:void(0)" aria-expanded="false"><i class="fas fa-download"></i><span
                    class="hide-menu">Study Materials </span></a>
            <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
              
                <li class="sidebar-item"><a href="<?php echo e(route('materials.create')); ?>" class="sidebar-link"><span class="hide-menu">- Add study materials </span></a>
                </li>
                <li class="sidebar-item"><a href="<?php echo e(route('materials.index')); ?>" class="sidebar-link"><span class="hide-menu">- View study materials </span></a>
                </li>
            </ul>
        </li>
        <!---end of class management links-->
        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
            href="javascript:void(0)" aria-expanded="false"><i class="fas fa-star-half-alt"></i><span
                class="hide-menu">Student Grades </span></a>
        <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">

            <li class="sidebar-item"><a href="<?php echo e(route('results.create')); ?>" class="sidebar-link"><span
                        class="hide-menu">- Upload Result </span></a>
            </li>
            <li class="sidebar-item"><a href="<?php echo e(route('results.index')); ?>" class="sidebar-link"><span
                        class="hide-menu">- View all results </span></a>
            </li>
                
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-cyan text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                    <h6 class="text-white">Teacher Dashboard</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white">Charts</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white">Widgets</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-danger text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-border-outside"></i></h1>
                    <h6 class="text-white">Tables</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- card -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title m-b-0">Progress Box</h4>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#add-new-event"
                                            class="btn m-t-20 btn-info btn-block waves-effect waves-light">
                                            <i class="ti-plus"></i> Add New Event
                                        </a>
                    <div class="m-t-20">
                        <div class="d-flex no-block align-items-center">
                            <span>81% Clicks</span>
                            <div class="ml-auto">
                                <span>125</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 81%"
                                aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex no-block align-items-center m-t-25">
                            <span>72% Uniquie Clicks</span>
                            <div class="ml-auto">
                                <span>120</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                                style="width: 72%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex no-block align-items-center m-t-25">
                            <span>53% Impressions</span>
                            <div class="ml-auto">
                                <span>785</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 53%"
                                aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex no-block align-items-center m-t-25">
                            <span>3% Online Users</span>
                            <div class="ml-auto">
                                <span>8</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-danger" role="progressbar"
                                style="width: 3%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- card new -->
            <p>dfasdasassa</p>
            <p>dfasdasassa</p>
            <p>dfasdasassa</p>
            <p>dfasdasassa</p>
            <p>dfasdasassa</p>
            <p>dfasdasassa</p>
            
        </div>
    </div>
    <!-- BEGIN MODAL -->
    <div class="modal none-border" id="my-event">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>Add Event</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success save-event waves-effect waves-light">Create
                        event</button>
                    <button type="button" class="btn btn-danger delete-event waves-effect waves-light"
                        data-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('dashboard.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/index.blade.php ENDPATH**/ ?>