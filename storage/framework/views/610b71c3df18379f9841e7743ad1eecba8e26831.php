<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<!-- Featured Section Begin -->
<section class="">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>THANK YOU <?php echo e(ucwords($data['name']) ?? null); ?></h2>

                </div>
                <div class="section-title" style="margin-bottom: 10px; !important">
                    <a href="<?php echo e(url('/').'/dashboard'); ?>">
                    <button class="btn btn-primary">Continue to Dashboard <i class="fa fa-arrow-right"></i></button>
                    </a>
                </div>

                <div class="section-text" style="width: 70%;margin: auto;font-size: 19px;text-align: center;">
                    Thank you for making payment. Please save the details of your payment below. Please check your email (<?php echo e($data['email'] ?? null); ?>) for your E-receipt and login details 
                </div>
                <div class="container" style="text-align: left;">

                <table class="table table-dark table-hover">
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th class="value">Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Training</td>
                      <td class="value"><?php echo e($data['programAbbr'] ?? null); ?></td>
                    </tr>
                    <tr>
                      <td>Email</td>
                      <td class="value"><?php echo e($data['email'] ?? null); ?></td>
                  
                    </tr>
                     <tr>
                      <td>Transaction ID</td>
                      <td class="value"><?php echo e($data['transid'] ?? null); ?></td>
                    </tr>
                    <tr>
                      <td>Invoice ID</td>
                      <td class="value"><?php echo e($data['invoice_id'] ?? null); ?></td>
                    </tr>
                     <tr>
                      <td>Amount Paid</td>
                      <td class="value"><?php echo e($currency_symbol.number_format($data['amount'])); ?></td>
                    </tr>
                    <tr>
                      <td>Balance</td>
                      <td class="value"><?php echo e($currency_symbol. number_format($data['balance'])); ?></td>
                    </tr>
                    <tr>
                      <td>Payment Type</td>
                      <td class="value"><?php echo e($data['message'] ?? null); ?></td>
                    </tr>
                    <?php if(isset($data['facilitator_name'])): ?>
                    <tr>
                      <td>Instructor</td>
                      <td class="value"><?php echo e($data['facilitator_name'] ?? null); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(isset($data['coupon_amount'])): ?>
                    <tr>
                      <td>Coupoun Applied</td>
                      <td class="value">
                        <strong>Coupon Code:</strong> <?php echo e($data['coupon_code']); ?> <br>
                         <strong>Coupon Amount:</strong> <?php echo e($data['currency_symbol']. number_format($data['coupon_amount'])); ?>

                    </td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
               
              </div>
            </div>
        </div>
       
    </div>
</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/thankyou.blade.php ENDPATH**/ ?>