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
                        <?php if(isset($modes) && count($modes) > 0): ?>
                            <?php echo e($modes['Online'] > $modes['Offline'] ? $currency_symbol.number_format($modes['Offline']) .'/'. $currency_symbol.number_format($modes['Online']) : $currency_symbol.number_format($modes['Online']) .'/'. $currency_symbol.number_format($modes['Offline'])); ?>

                        <?php else: ?>
                            <?php if(($training->e_amount > 0 ) && $training->close_earlybird == 0 || $training->e_amount != 0): ?>
                                <?php echo e($currency_symbol); ?><?php echo e(number_format($training->e_amount)); ?>

                                <span class="discount-color">&nbsp; <?php echo e($currency_symbol); ?><span class="linethrough discount-color"><?php echo e(number_format($training->p_amount)); ?></span></span>
                            <?php else: ?>
                                <?php if(!empty($training->price_range)): ?>
                                    From <?php echo e($currency_symbol.number_format($exchange_rate * $training->price_range['from'])); ?> to <?php echo e($currency_symbol.number_format($exchange_rate * $training->price_range['to'])); ?>

                                <?php else: ?>
                                    <?php echo e($currency_symbol); ?><?php echo e(number_format($exchange_rate * $training->p_amount)); ?>

                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php if(isset($training->description) && !empty($training->description)): ?>
                    <p><?php echo e($training->description); ?></p>
                    <?php endif; ?>
                    <div class="checkout__form">
                        <form action="<?php echo e(route('trainings')); ?>" method="GET">
                            <div class="row">
                                <div class="col-lg-12 col-md-6">
                                    <?php if(isset($training->subPrograms) && $training->subPrograms->count() > 0): ?>
                                        <div class="checkout__input">
                                            <p>Select Variation<span>*</span></p>
                                            <select name="training" id="training" class="training-select" style="font-size: 12px !important" required>
                                                <option value="">Select</option>
                                                <?php $__currentLoopData = $training->subPrograms->sortBy('p_amount'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($tran->id); ?>"><?php echo e($tran->p_name); ?> (<?php echo e($currency_symbol.number_format($exchange_rate * $tran->p_amount)); ?>)</option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                
                                
                                
                            </div>
                            <input class="primary-btn" style="background: #dfa802; !important" type="submit" value="PROCEED">
                        </form>
                    </div>                        
                </div>
            </div>
            
        </div>
    </div>
</section>

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


<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/single_training_with_children.blade.php ENDPATH**/ ?>