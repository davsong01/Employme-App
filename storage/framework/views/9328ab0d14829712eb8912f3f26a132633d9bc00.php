<?php 
    $menus = Auth::user()->permissions ?? [];
?>

<?php $__env->startSection('css'); ?>
<?php echo $__env->make('dashboard.company.partials.company_extra_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('dashboard'); ?>
<aside class="left-sidebar" data-sidebarbg="skin5">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="pt-3">
                <?php if(in_array(1, $menus)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="<?php echo e(route('company_user.dashboard')); ?>" aria-expanded="false">
                        <i class="fa fa-desktop"></i>
                        <span class="hide-menu"> Dashboard</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(in_array(2, $menus)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="<?php echo e(route('company.participants')); ?>" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span class="hide-menu"> Participants</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(in_array(4, $menus)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="<?php echo e(route('company.pretest.select')); ?>" aria-expanded="false">
                        <i class="fa fa-list-alt"></i>
                        <span class="hide-menu"> Pre Test Results</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if(in_array(5, $menus)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="<?php echo e(route('company.posttest.results')); ?>" aria-expanded="false">
                        <i class="fa fa-graduation-cap"></i>
                        <span class="hide-menu"> Post Test Results</span>
                    </a>
                </li>
                <?php endif; ?>
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="<?php echo e(route('company.logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false">
                        <i class="fa fa-sign-out-alt"></i>
                        <span class="hide-menu"> Logout</span>
                    </a>
                </li>
                    
                    <form id="logout-form" action="<?php echo e(route('company.logout')); ?>" method="POST" style="display: none;">
                        <?php echo e(csrf_field()); ?>

                    </form>

            </ul>
        </nav>
    </div>
</aside>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('extra-scripts'); ?>
<?php echo $__env->make('dashboard.company.partials.company_extra_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('extra-scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dashboard.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/company/index.blade.php ENDPATH**/ ?>