<?php
    $facilitator = \Session::get('facilitator_id');
    $price = $amount;
    $settings = \App\Settings::first();
?>


<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?> - Checkout
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <div class="checkout__form">
            <h4>Billing Details</h4>
            <form action="<?php echo e(route('pay')); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Name<span>*</span></p>
                                     <input type="text" class="form-control" id="name" name="name" 
                                        <?php if(auth()->guard()->check()): ?>
                                        value="<?php echo e(auth()->user()->name); ?>"  
                                        placeholder="Full Name"
                                        <?php endif; ?>

                                        <?php if(auth()->guard()->guest()): ?> 
                                        value="<?php echo e(old('name')); ?>" placeholder="Full Name"  
                                        <?php endif; ?> required>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Email<span>*</span></p>
                                    <input type="email" id="email" name="email" 
                                        <?php if(auth()->guard()->check()): ?>
                                        value="<?php echo e(auth()->user()->email); ?>"  
                                        <?php endif; ?>
                                        <?php if(auth()->guard()->guest()): ?> 
                                        value="<?php echo e(old('email')); ?>" placeholder="Enter email"  
                                        <?php endif; ?> required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Phone<span>*</span></p>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                        <?php if(auth()->guard()->check()): ?>
                                        value="<?php echo e(auth()->user()->t_phone); ?>"
                                        <?php endif; ?>

                                        <?php if(auth()->guard()->guest()): ?> 
                                        value="<?php echo e(old('name')); ?>" placeholder="Phone number"  
                                        <?php endif; ?> required>
                                </div>
                            </div>
                        </div>
                            
                        <div class="checkout__input__checkbox">
                            <label for="acc">
                                Agree to <a href="<?php echo e(!is_null(\App\Settings::first()->value('tac_link')) ? \App\Settings::first()->value('tac_link') : '#'); ?>">terms and conditions?</a> 
                                <input type="checkbox" id="acc" required checked>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <?php if(isset($type) && $type == 'full'): ?>
                         <div class="row">
                           <div class="col-lg-12">
                                <h6 style="margin-bottom: 10px !important;"><span class="icon_tag_alt"></span> Have a coupon? <b onclick="showCoupon()" style="text-decoration: underline; cursor: pointer;" >Click here</b> to enter your code
                                </h6>
                            </div>
                        </div>
                        <span style="color:red; display:none" id="enter-email">You must enter your email and coupon code</span>
                        <span style="color:green; display:none" id="coupon-applied"></span>
                       
                        <div class="row" id="coupon-field" style="display:none">
                            <div class="col-lg-6" style="padding-right:0px">
                                <div class="checkout__input">
                                    <input type="text" id="coupon" name="coupon" value="<?php echo e(old('coupon')); ?>">
                                </div>
                            </div>
                            <div class="col-lg-6" >
                                <div class="checkout__input">
                                <p id="validate-coupon" onclick="validateCoupon(<?php echo e(old('coupon')); ?>)" class="site-btn">Apply Coupon</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="checkout__order">
                            <div class="checkout__order__products">Order details</div>
                            <table style="width: 100%;">
                                <tr class="bor-bottom">
                                    <th class="col1">Course</th>
                                    <td class="col2"><?php echo e($training['p_name']); ?></td>
                                </tr>
                                
                                <tr class="bor-bottom">
                                    <th class="col1">Sub total</th>
                                    <td class="col2"><?php echo e($currency_symbol. number_format($amount)); ?></td>
                                </tr>

                                <tr class="bor-bottom" id="show-coupon" style="display:none">
                                    <th class="col1">Coupon Applied</th>
                                    <td class="col2"><?php echo e($currency_symbol); ?><span id="coupon_amount"></span> </td>
                                </tr>
                                <tr class="bor-bottom">
                                    <th class="col1">Total</th>
                                    <td class="col2"><?php echo e($currency_symbol); ?><span id="total"><?php echo e(number_format($amount)); ?></span> </td>
                                </tr>
                            </table>
                            
                            <input type="hidden" name="modes" value="<?php echo e($modes); ?>">
                            <input type="hidden" name="location" value="<?php echo e($location); ?>">
                            <input type="hidden" name="preferred_timing" value="<?php echo e($preferred_timing); ?>">
                            <input type="hidden" name="orderID" value="<?php echo e($training['id']); ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" class="total" id="amount" name="amount" value="<?php echo e(($amount)); ?>">
                            <input type="hidden" name="currency" value="<?php echo e($currency); ?>">
                            <input type="hidden" name="metadata" value="<?php echo e(json_encode($array = ['pid' => $training['id'], 'facilitator' => $facilitator , 'coupon_id' => $coupon_id ?? NULL, 'type'=>$type ?? NULL])); ?>"> 
                                                 
                            <div class="d-lg-flex justify-content-center align-items-start flex-column">
                            <?php if($amount > 0): ?>
                                <h4 class="">Choose payment method</h4>
                                <div class="w-100 d-flex justify-content-start align-items-center flex-wrap">
                                    <?php if($settings->allow_transfer_button == 'yes'): ?>
                                    <button class="mr-1 mb-1 pay-option" name="payment_mode" value="0"><i class="fa fa-bank"></i> Pay with Bank Transfer</button>
                                    <?php endif; ?>
                                    <?php $__currentLoopData = $payment_modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($mode->type == 'card'): ?>
                                    <button class="mr-1 mb-1 pay-option" name="payment_mode" value="<?php echo e($mode->id); ?>"><i class="fa fa-credit-card"></i> Pay with <span style="background-image:url(<?php echo e(url('/').'/paymentmodes/'.$mode->image); ?>);background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                                    <?php endif; ?>
                                    <?php if($mode->type == 'crypto'): ?>
                                    <button class="mr-1 mb-1 pay-option" name="payment_mode" value="<?php echo e($mode->id); ?>"><i class="fa fa-bitcoin"></i> Pay with <span style="background-image:url(<?php echo e(url('/').'/paymentmodes/'.$mode->image); ?>);background-position: center;background-repeat: no-repeat;background-size: cover;color:transparent;">image</span></button>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div class="w-100 d-flex justify-content-start align-items-center flex-wrap">
                            <?php else: ?>
                            <h4 class=""></h4>
                            <button class="mr-1 mb-1 pay-option btn-primary" name="payment_mode" value="register"><i class="fa fa-hand-pointer-o"></i> <span>COMPLETE REGISTRATION</span></button>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="transferModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <p>Some text in the modal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
    
</section>
<script>
    function showCoupon(){       
        $('#coupon-field').toggle();
    }
   
    function validateCoupon(id){
        email = $('#email').val();
        code = $('#coupon').val();
        pid = "<?php echo e($training['id']); ?>";
        price = "<?php echo e($amount); ?>";
       
        var total = $('#amount').val();
        var newTotal = 0;
        let dollarUSLocale = Intl.NumberFormat('en-US');

        if(email == '' || code == ''){
            return $('#enter-email').show();
        }else{
            $('#enter-email').hide()

            $.post("/validate-coupon", {
                email: email,
                price: price,
                code: code,
                pid: pid,
               
            },function(data, status){
                if(status == 'success'){
                    if(data.amount){
                        $('#coupon_amount').text(dollarUSLocale.format(parseFloat(data.amount)));
                        $('#coupon-applied').text('Coupon: '+data.code+ ' with discount of '+"<?php echo e($currency); ?>"+dollarUSLocale.format(parseFloat(data.amount))+' successfully applied');
                        $('#coupon-applied').css("color", "green");
                        $('#enter-email').hide()
                       
                        $('#amount').val(data.grand_total);
                        $('#coupon').val(code);
                        
                        $('#total').text(dollarUSLocale.format(parseFloat(data.grand_total)));

                        $('#coupon-applied').show();
                        $('#total').show();
                        $('#show-coupon').show()
                    }else{
                        $('#coupon').val("");
                        $('#coupon-applied').text('Coupon does not exist or you have used it');
                        $('#coupon-applied').css("color", "red");
                        $('#enter-email').hide()
                        $('#coupon-applied').show()
                        $('#show-coupon').hide()
                        $('#total').text(dollarUSLocale.format(parseFloat(price)));
                        $('#amount').val(price);

                    }
                    
                }
            });
        }
	}    
</script>
<?php $__env->stopSection(); ?>
    
<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/checkout.blade.php ENDPATH**/ ?>