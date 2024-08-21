<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?> - <?php echo e($training->p_name); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
 <!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__item">
                        <img class="product__details__pic__item--large"
                            src="<?php echo e('/'.$training->image); ?>" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="product__details__text">
                    <h3><?php echo e($training->p_name); ?></h3>
                    
                    <div class="product__details__price">
                        <?php if($training->is_closed == 'no'): ?>
                            <?php if(isset($modes) && count($modes) > 0): ?>
                                <?php echo e($modes['Online'] > $modes['Offline'] ? $currency_symbol.number_format($modes['Offline']) .'/'. $currency_symbol.number_format($modes['Online']) : $currency_symbol.number_format($modes['Online']) .'/'. $currency_symbol.number_format($modes['Offline'])); ?>

                                
                            <?php else: ?>
                                <?php if(($training->e_amount > 0 ) && $training->close_earlybird == 0 || $training->e_amount != 0): ?>
                                    <?php echo e($currency_symbol); ?><?php echo e(number_format($training->e_amount)); ?>

                                    <span class="discount-color">&nbsp; <?php echo e($currency_symbol); ?><span class="linethrough discount-color"><?php echo e(number_format($training->p_amount)); ?></span></span>
                                <?php else: ?>
                                    <?php if($training->p_amount > 0): ?>
                                    <?php echo e($currency_symbol); ?><?php echo e(number_format($training->p_amount)); ?>

                                    <?php if(in_array($training->id, [68])): ?>
                                    , GHc 60, GMD 75
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <span style="color:green">FREE TRAINING</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                        <span style="color:red">Closed Group Training</span>

                        <?php endif; ?>
                        
                    </div>
                    <?php if(isset($training->description) && !empty($training->description)): ?>
                    <p><?php echo e($training->description); ?></p>
                    <?php endif; ?>
                    
                    <div class="checkout__form">
                        <form action="<?php echo e(route('checkout')); ?>" method="POST">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                    <?php if(isset($modes) && count($modes) > 0): ?>
                                        <div class="checkout__input">
                                            <p>Select Mode<span>*</span></p>
                                            <select name="modes" id="payment_modes" required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($mode); ?>"><?php echo e($mode); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <?php if(isset($locations) && count($locations) > 0 ): ?>
                                            <div class="checkout__input" id="locations_div" style="display:none">
                                                <p>Select location<span>*</span></p>
                                                <select name="location" id="locations" required>
                                                    <option value="">Select Location</option>
                                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($location); ?>" <?php echo e(old('location_name') == $location ? 'selected' : ''); ?>>
                                                            <?php echo e($location); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div> 
                                        <?php endif; ?>
                                        
                                        <div class="checkout__input" id="payment_types" style="display:none">
                                            <p>Select payment type<span>*</span></p>
                                            <div class="select-block">
                                                <select name="type" id="payments" required>
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div> 
                                        <?php if(isset($training->allow_preferred_timing) && $training->allow_preferred_timing == 'yes' ): ?>
                                            <div class="checkout__input">
                                                <p>Your preferred Off Diet month<span>*</span></p>
                                                <select name="preferred_timing" id="preferred_timing" required>
                                                    <option value="">Select...</option>
                                                    <?php $__currentLoopData = $training->programRange(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preferred_timing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($preferred_timing); ?>" <?php echo e(old('preferred_timing') == $preferred_timing ? 'selected' : ''); ?>>
                                                        <?php echo e($preferred_timing); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div> 
                                        <?php endif; ?>
                                    <?php else: ?> 
                                        <?php if(isset($locations) && count($locations) > 0 ): ?>
                                            <div class="checkout__input">
                                                <p>Select location<span>*</span></p>
                                                <select name="location" id="locations" required>
                                                    <option value="">Select Location</option>
                                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($location); ?>" <?php echo e(old('location_name') == $location ? 'selected' : ''); ?>>
                                                            <?php echo e($location); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div> 
                                        <?php endif; ?>
                                        
                                        <?php if($training->p_amount > 0): ?>
                                        <div class="checkout__input">
                                            <p>Select payment type<span>*</span></p>
                                            <select name="type" id="" required>
                                                <option value="">Select</option>
                                                <option value="full" <?php echo e(old('amount') == $training->p_amount ? 'selected' : ''); ?>>Full Payment (<?php echo e($currency_symbol.number_format($training->p_amount)); ?>  <?php if(in_array($training->id, [68])): ?>, GHc 60, GMD 75 <?php endif; ?>)</option>
                                                <?php if(($training->e_amount > 0 ) && $training->close_earlybird == 0 || $training->e_amount > 0): ?>
                                                <option value="earlybird" <?php echo e(old('amount') == $training->e_amount ? 'selected' : ''); ?>>Earlybird (<?php echo e($currency_symbol.number_format($training->e_amount)); ?>)</option>
                                                <?php endif; ?>
                                                <?php if($training->haspartpayment == 1): ?>
                                                <option value="part" <?php echo e(old('amount') == ($training->p_amount/2) ? 'selected' : ''); ?>>Part Payment (<?php echo e($currency_symbol.number_format($training->p_amount/2)); ?>)</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <?php else: ?>
                                        <input type="hidden" value="full" name="type">
                                        <?php endif; ?>
                                        
                                        <?php if(isset($training->allow_preferred_timing) && $training->allow_preferred_timing == 'yes' ): ?>
                                            <div class="checkout__input">
                                                <p>Your preferred Off Diet month<span>*</span></p>
                                                <select name="preferred_timing" id="preferred_timing" required>
                                                    <option value="">Select...</option>
                                                    <?php $__currentLoopData = $training->programRange(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $preferred_timing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($preferred_timing); ?>" <?php echo e(old('preferred_timing') == $preferred_timing ? 'selected' : ''); ?>>
                                                         <?php echo e($preferred_timing); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                </div>
                                <input type="hidden" name="training" value="<?php echo e($training); ?>"> 
                                <input type="hidden" name="facilitator" value="<?php echo e(\Session::get('facilitator')); ?>"> 
                                <input type="hidden" name="facilitator_id" value="<?php echo e(\Session::get('facilitator_id')); ?>"> 
                                <input type="hidden" name="facilitator_name" value="<?php echo e(\Session::get('facilitator_name')); ?>"> 
                                <input type="hidden" name="facilitator_license" value="<?php echo e(\Session::get('facilitator_license')); ?>">

                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"> 
                            </div>
                            <?php if($training->is_closed == 'no'): ?>
                            <?php if($training->p_amount > 0): ?>
                            <input class="primary-btn" type="submit" value="PROCEED TO CHECKOUT">
                            <?php else: ?> 
                            <input class="primary-btn" type="submit" value="PROCEED TO REGISTER">
                            <?php endif; ?>
                            <?php endif; ?>
                        </form>
                    </div>                        
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Product Details Section End -->

<!-- Related Product Section Begin -->

<!-- Related Product Section End -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
    $(document).ready(function() {
        $('payments').niceSelect();
        $("#payment_modes").on('change', function () {
            if(this.value){
                if(this.value == 'Offline'){
                    getPayment(this.value, <?php echo e($training->id); ?>);
                    $("#locations_div").show();
                    $("#locations").attr('required',true);
                    $("#payment_types").show();
                }else{
                    getPayment(this.value, <?php echo e($training->id); ?>);
                    $("#locations").attr('required',false);
                    $("#locations_div").hide();
                    $("#payment_types").show();
                }
            }else{
                $("#payment_types").hide();
                $("#locations_div").hide();
            }
        });

        function getPayment(payment_mode, training){
            // $("#payments").prepend(data.data);
            $('#payments').empty();
            
            $.post("/get-mode-payment-types", {
                payment_mode: payment_mode,
                training: training,
                currency_symbol : "<?php echo e($currency_symbol); ?>",
            },function(data, status){
                if(status == 'success'){
                    $("#payments").append(data.data);
                    $('#payments').niceSelect('update'); //destroy the plugin 
                    $('#payments').niceSelect(); //apply again
                }
            });
        }
    });

    
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/single_training.blade.php ENDPATH**/ ?>