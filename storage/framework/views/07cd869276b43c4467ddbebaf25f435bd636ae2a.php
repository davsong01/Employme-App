<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<section class="">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>Search Results (<?php echo e($trainings->count()); ?>)</h2>
                </div>
            </div>
        </div>
        <div class="row featured__filter">
            <?php if($trainings->count() > 0): ?>
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
            <?php else: ?>
            No results found
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- Featured Section End -->
<section>
    <div class="container">
        <div class="row">
            <!-- <div class="row"> -->
            <div class="col-lg-12">
                <!-- <div class="col-lg-12"> -->
                <div class="homepage-pagination">
                    <?php echo e($trainings->appends($_GET)->links()); ?>

                </div>
                <!-- </div> -->
            </div>
        <!-- </div> -->
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.contai.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/search_results.blade.php ENDPATH**/ ?>