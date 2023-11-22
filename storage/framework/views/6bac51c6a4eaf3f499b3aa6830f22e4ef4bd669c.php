<?php $__env->startSection('title', 'Result comments'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Result Comments</h2>
                        <?php if(isset($comments->facilitator_comment) && !empty($comments->facilitator_comment)): ?>
                        <h5>Facilitator's Comment</h5>
                       
                        <p>
                        <?php echo $comments->facilitator_comment; ?>

                        </p>
                        <hr>
                        <?php endif; ?>
                        <?php if(isset($comments->grader_comment) && !empty($comments->grader_comment)): ?>

                        <h5>Grader's Comment</h5>
                        <p>
                             <?php echo $comments->grader_comment; ?>

                        </p>
                       <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/tests/result_comments.blade.php ENDPATH**/ ?>