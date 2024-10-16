<?php 
    $menus = Auth::user()->permissions() ?? [];
?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php $__env->stopSection(); ?>
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
                <?php if(!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                    <?php if(in_array(1, $menus)): ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(url('dashboard')); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                                class="hide-menu">Staff Dashboard</span></a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))): ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(route('teachers.students', auth()->user()->id)); ?>" aria-expanded="false"><i
                                class="fa fa-users"></i><span class="hide-menu">My Students</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(route('teachers.programs', auth()->user()->id)); ?>" aria-expanded="false"><i
                                class="fas fa-chalkboard-teacher"></i><span class="hide-menu">My Programs</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(route('teachers.earnings', auth()->user()->id)); ?>" aria-expanded="false"><i
                                class="fas fa-wallet"></i><span class="hide-menu">My Earnings</span></a></li>
                <?php endif; ?>
               
                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) ): ?>
                <?php if(in_array(1, $menus)): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(url('dashboard')); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Admin Dashboard</span></a></li>
                <?php endif; ?>
                <?php endif; ?>

                <?php if(in_array(2, $menus)): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(route('users.index')); ?>" aria-expanded="false"><i class="fas fa-users"></i><span
                            class="hide-menu">Student Management</span></a></li>
                <?php endif; ?>
                <?php if(in_array(3, $menus)): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(route('teachers.index')); ?>" aria-expanded="false"><i class="fas fas fa-user"></i><span
                            class="hide-menu">Facilitator Management</span></a></li>
                <?php endif; ?>
                
                
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(route('companyuser.index')); ?>" aria-expanded="false"><i class="fa fa-solid fa-building"></i><span
                            class="hide-menu">Company Admin Management</span></a></li>
                
                
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-chalkboard-teacher"></i><span
                            class="hide-menu">Training Management </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level"> 
                        <?php if(in_array(4, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('programs.index')); ?>" class="sidebar-link"><span
                                    class="hide-menu">- View all Trainings </span></a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array(5, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('programs.trashed')); ?>" class="sidebar-link"><span class="hide-menu">- Trashed Trainings </span></a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                
                <?php if(in_array(6, $menus)): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(route('materials.index')); ?>" aria-expanded="false"><i class="fas fa-download"></i><span
                            class="hide-menu">View All study Materials</span></a></li>
                <?php endif; ?>
                <?php if(in_array(7, $menus)): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(route('coupon.index')); ?>" aria-expanded="false"><i class="fa fa-gift"></i><span
                            class="hide-menu">Coupons</span></a></li>
                <?php endif; ?>
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="far fa-money-bill-alt"></i><span
                            class="hide-menu">Financial</span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level"> 
                        <?php if(in_array(9, $menus)): ?>
                        <li class="sidebar-item"> <a class="sidebar-link"
                                href="<?php echo e(route('pop.index')); ?>" aria-expanded="false"><span
                            class="hide-menu">- Attempted Payments</span></a></li>
                        <li class="sidebar-item"> <a class="sidebar-link"
                                href="<?php echo e(route('proof.payment')); ?>" aria-expanded="false"><span
                            class="hide-menu">- Proof of Payment</span></a></li>
                        <?php endif; ?>

                        <?php if(in_array(8, $menus)): ?>
                        <li class="sidebar-item"> <a class="sidebar-link"
                                href="<?php echo e(route('payments.index')); ?>" aria-expanded="false"><span
                                    class="hide-menu">- Transactions</span></a></li>
                        <?php endif; ?>
                        
                        <?php if(in_array(8, $menus)): ?>
                        <li class="sidebar-item"> <a class="sidebar-link"
                                href="<?php echo e(route('payments.history')); ?>" aria-expanded="false"><span
                                    class="hide-menu">- Wallet Transactions</span></a></li>
                        <?php endif; ?>
                        

                    </ul>
                </li>
                <?php if(in_array(10, $menus)): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(route('complains.index')); ?>" aria-expanded="false"><i class="far fa-comments"></i><span
                            class="hide-menu">CRM Tool</span></a></li>
                <?php endif; ?>
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fa fa-edit"></i><span
                            class="hide-menu">LMS </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <?php if(in_array(11, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('modules.index')); ?>" class="sidebar-link"><span
                                    class="hide-menu">- Modules</span></a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array(12, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('questions.index')); ?>" class="sidebar-link"><span class="hide-menu">- Questions</span></a>
                        </li>
                        <?php endif; ?>
                        <?php if(in_array(13, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('pretest.select')); ?>" class="sidebar-link"><span class="hide-menu">- Pre Test Results</span></a>
                        <?php endif; ?>
                        <?php if(in_array(14, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('posttest.results')); ?>" class="sidebar-link"><span class="hide-menu">- Post Test Results</span></a>
                        <?php endif; ?>
                        <?php if(in_array(15, $menus)): ?>
                        <li class="sidebar-item"><a href="<?php echo e(route('certificates.index')); ?>" class="sidebar-link"><span class="hide-menu">- Certificates</span></a>
                        </li>
                        <?php endif; ?>
                        <?php if(Auth()->user()->role_id == "Admin"): ?>
                            <?php if(in_array(16, $menus)): ?>
                            <li class="sidebar-item"><a href="<?php echo e(route('scoreSettings.index')); ?>" class="sidebar-link"><span class="hide-menu">- Score Settings</span></a>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    
                    <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                    <?php if(in_array(17, $menus)): ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(route('users.mail')); ?>" aria-expanded="false"><i class="fa fa-envelope"></i><span
                                class="hide-menu">Email Participants</span></a></li>
                    <?php endif; ?>
                    <?php if(in_array(18, $menus)): ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(route('payment-modes.index')); ?>" aria-expanded="false"><i class="fa fa-credit-card"></i><span
                                class="hide-menu">Payment modes</span></a></li>
                    <?php endif; ?>
                    <?php if(in_array(19, $menus)): ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="<?php echo e(route('settings.edit', 1)); ?>" aria-expanded="false"><i class="fa fa-cog"></i><span
                            class="hide-menu">Settings</span></a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('extra-scripts'); ?>
        <?php echo $__env->yieldContent('extra-scripts'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/index.blade.php ENDPATH**/ ?>