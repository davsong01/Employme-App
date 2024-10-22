
<?php if(session()->get('message')): ?>
<div class="alert alert-success" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Success!</strong> <?php echo e(session()->get('message')); ?>

</div>
<?php endif; ?>
<?php if(session()->get('error')): ?>
<div class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> <?php echo e(session()->get('error')); ?>

</div>
<?php endif; ?>
<?php if(session()->get('msg')): ?>
<div class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> <?php echo e(session()->get('msg')); ?>

</div>
<?php endif; ?>
<?php if(session()->get('fill')): ?>
<div class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> <?php echo session()->get('fill'); ?>

</div>
<?php endif; ?>
<?php if(session()->get('warning')): ?>
<script>
    alert('WARNING! Details not saved! Student cannot pay more than program fee')
</script>
<script>
    alert('Please try again')
</script>
<?php endif; ?>

<?php if($errors->any()): ?>
 <div class="alert alert-warning" role="alert">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo e($error); ?><br>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/partials/alerts.blade.php ENDPATH**/ ?>