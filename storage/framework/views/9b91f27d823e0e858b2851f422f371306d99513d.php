<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('dashboard'); ?>
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                <?php if(Auth::user()->isImpersonating() ): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="<?php echo e(route('stop.impersonate')); ?>" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                <?php endif; ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(url('/')); ?>" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">All Trainings</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(url('dashboard')); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Student Dashboard</span></a></li>
                <li class="sidebar-item"><a href="<?php echo e(route('payments.index', Auth::user()->id)); ?>" class="sidebar-link"><i
                    class="far fa-money-bill-alt"></i><span class="hide-menu">Payment History
                </span></a>
                </li>
               
            </ul>
        </nav>
    </div>
</aside>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('dashboard.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/index.blade.php ENDPATH**/ ?>