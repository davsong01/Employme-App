<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('dashboard'); ?>
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(url('company/dashboard')); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Company Admin Dashboard</span></a></li>
            </ul>
        </nav>
    </div>
</aside>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('dashboard.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/company/index.blade.php ENDPATH**/ ?>