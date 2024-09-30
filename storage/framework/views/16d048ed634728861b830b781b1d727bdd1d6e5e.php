<?php if($paginator->hasPages()): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            
            <li class="page-item <?php echo e($paginator->onFirstPage() ? 'disabled' : ''); ?>">
                <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" aria-disabled="<?php echo e($paginator->onFirstPage()); ?>">
                    &laquo;
                </a>
            </li>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <li class="page-item disabled"><span class="page-link"><?php echo e($element); ?></span></li>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="page-item <?php echo e($page == $paginator->currentPage() ? 'active' : ''); ?>">
                            <a class="page-link" href="<?php echo e($page == $paginator->currentPage() ? '#' : $url); ?>">
                                <?php echo e($page); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <li class="page-item <?php echo e($paginator->hasMorePages() ? '' : 'disabled'); ?>">
                <a class="page-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" aria-disabled="<?php echo e(!$paginator->hasMorePages()); ?>">
                    &raquo;
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/vendor/pagination/tailwind.blade.php ENDPATH**/ ?>