<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php if($discounts->count() > 0): ?>
<!-- Earlybird rush -->
<section class="from-blog spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>EarlyBird Rush</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="product__discount__slider owl-carousel">
                <?php $__currentLoopData = $discounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4">
                    <div class="product__discount__item">
                        <a href="<?php echo e(route('trainings', $discount->id )); ?>" target="_blank">
                            <div class="product__discount__item__pic set-bg"
                                data-setbg="<?php echo e($discount->image); ?>">
                                <div class="product__discount__percent"><?php echo e(number_format((($discount->e_amount * 100)/$discount->p_amount) - 100, 0)); ?>%</div>
                            </div>
                        </a>
                        <div class="product__discount__item__text">
                            <a href="<?php echo e(route('trainings', $discount->id )); ?>" target="_blank">
                                <h5 style="color: #c2c2c2"><?php echo e($discount->p_name); ?></h5>
                            </a>
                            <?php if($discount->is_closed == 'no'): ?>
                            <div class="product__item__price"><?php echo e($currency_symbol. number_format($exchange_rate * $discount->e_amount )); ?><span><?php echo e($currency_symbol. number_format($exchange_rate * $discount->p_amount)); ?></span></div>
                            <?php else: ?> 
                            <div class="product__item__price" style="color:red">Closed Group Training</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<!-- End of earlybird rush -->
<?php endif; ?>
<!-- Featured Section Begin -->
<section class="">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>All Courses</h2>
                </div>
            </div>
        </div>
        <div class="row featured__filter">
            <?php $__currentLoopData = $trainings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mix oranges fresh-meat">
                    <div class="featured__item">
                        <?php if($training->p_end < date('Y-m-d') || $training->close_registration == 1): ?>
                        <?php else: ?>
                        <a href="<?php echo e(route('trainings', $training->id )); ?>" target="_blank">   
                        <?php endif; ?>
                            <div class="featured__item__pic set-bg" data-setbg="<?php echo e($training->image); ?>">
                                <?php if($training->p_end < date('Y-m-d') || $training->close_registration == 1): ?>
                                <ul class="featured__item__pic__hover">
                                    <li><a href="#" class="disabled-link">Registration closed!</a></li>
                                </ul> 
                                <?php endif; ?>
                            </div>
                        </a>
                        
                        <div class="featured__item__text">
                            <h6 style="min-height:60px">
                                <?php if($training->p_end < date('Y-m-d') || $training->close_registration == 1): ?>
                                <a href="#" class="disabled-link">
                                <span class="mobile_closed" style="display:none">Registration closed!</span>
                                
                                <?php else: ?>
                                <a href="<?php echo e(route('trainings', $training->id )); ?>" target="_blank">  
                                <?php endif; ?>
                                <?php echo e($training->p_name); ?></a>
                            </h6>
                            <h5>
                                <?php if($training->is_closed == 'no'): ?>
                                    <?php if(($training->e_amount > 0 ) && $training->close_earlybird == 0 || $training->e_amount != 0): ?>
                                        <?php echo e($currency_symbol); ?><?php echo e(number_format($exchange_rate*$training->e_amount)); ?>

                                        <span class="discount-color">&nbsp; <?php echo e($currency_symbol); ?><span class="linethrough discount-color"><?php echo e(number_format($exchange_rate * $training->p_amount)); ?></span></span>
                                    <?php else: ?>
                                        <?php if(!empty($training->price_range)): ?>
                                            From <?php echo e($currency_symbol.number_format($exchange_rate * $training->price_range['from'])); ?> to <?php echo e($currency_symbol.number_format($exchange_rate * $training->price_range['to'])); ?>

                                        <?php else: ?>
                                            <?php echo e($currency_symbol); ?><?php echo e(number_format($exchange_rate * $training->p_amount)); ?>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                <span style="color:red">Closed Group Training</span>
                                <?php endif; ?>
                            </h5> 
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <?php echo $trainings->links(); ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/welcome.blade.php ENDPATH**/ ?>