<?php
    $logo = \App\Models\Settings::first()->value('logo');
    $currency = \Session::get('currency');
    $currency_symbol = \Session::get('currency_symbol');
    $exchange_rate = \Session::get('exchange_rate');
?>
<?php $__env->startComponent('mail::message'); ?>
<strong>Dear <?php echo e($data['name']); ?></strong>,

<?php if(isset($data['type']) && $data['type'] == 'balance'): ?>
<div>
<p style="text-align:justify !important">Your balance payment of <?php echo e($data['currency_symbol']); ?><?php echo e($data['amount']); ?> for <?php echo e($data['programName']); ?> has been received.<br><br>You can now access all sections of your portal!</p>
</div>
<?php else: ?> 
<span style="text-align:justify !important">Your <?php echo e($data['message']); ?> of <?php echo e($currency_symbol.$data['amount']); ?> for the <?php echo e($data['programName']); ?> (<?php echo e($data['programAbbr']); ?>) via <?php echo e($data['t_type']); ?> has been received. <br><br></span>
<span><strong style="color:red">NOTE: </strong>Attached to this email are your E-receipt, booking form (if available) and feedback form(if available) which you are to print and bring along with you to the training center (NOT APPLICABLE FOR OUR ONLINE TRAININGS).</strong> <br><br></span>
<span>Your customized portal is where you can view/download study materials for this training, view your payment history and do much more. <br><br></span>
<span><strong>Your customized portal login details are:</strong> <br><br>
Username: <?php echo e($data['email']); ?> <br>
Password: 12345 <small> <strong>(Use existing password if you are a returning participant)</strong> </small>
</span>

<?php $__env->startComponent('mail::button', ['url' =>  config('app.url').'/login']); ?>
Login to your Portal here
<?php echo $__env->renderComponent(); ?>

</div>
<?php endif; ?>

Accept our warm regards.<br><br>
<b> <?php echo e(\App\Models\Settings::select('program_coordinator')->first()->value('program_coordinator')); ?></b><br>
Program Coordinator
<?php echo $__env->renderComponent(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/emails/welcomemail.blade.php ENDPATH**/ ?>