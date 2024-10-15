<ul>
    <li class="active"><a href="/">All Trainings</a></li>
    <li class=""><a href="<?php echo e(route('pop.create')); ?>">Upload Proof of Payment</a></li>
    <li class=""><a href="<?php echo e(route('reset')); ?>">Reset All</a></li>
    <?php if(auth()->guard()->guest()): ?>
    <li><a href="<?php echo e(url('/').'/login'); ?>">Login</a></li>
    <?php endif; ?>
    <?php if(auth()->guard()->check()): ?>
    <li  class=""><a href="<?php echo e(url('/').'/dashboard'); ?>">My Dashboard</a></li>
    <li  class=""><a class="" href="<?php echo e(route('logout')); ?>"
        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
            <?php echo e(__('Logout')); ?>

        </a> 
        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                <?php echo e(csrf_field()); ?>

        </form>
    
    </li>
    <?php endif; ?>
</ul><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/nav.blade.php ENDPATH**/ ?>