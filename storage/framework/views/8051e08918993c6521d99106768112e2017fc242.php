<?php $__env->startSection('title', 'My Results'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>My Completed Tests <br><br> (Please note that Pre- Class Test results are NOT collated in the final results)</h4><br>
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    <?php if($results->count() < 1): ?>
            <div>
                <h5 style="color:red">No completed tests Yet! Go back and take a Post Class Test</h5>
            </div> 
        <?php else: ?>   
        <div class="row"> 
            <div class="col-md-12">
                <h5> <strong>OVERALL TEST RESULTS</strong></h5>
            </div>
        </div>
        <?php if($hasmock == 1): ?>
        <div class="row">
            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $mock_results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mock_result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>  
            <?php if($result->module->id ==  $mock_result->module->id): ?>      
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                        <h5 class="font-light text-white"> <b>Training: </b>  <?php echo e($program->p_name); ?></h5>
                        <h5 class="font-light text-white"> <b>Module: </b><?php echo e($result->module->title); ?></h5>
                            <h4 class="text-white">Test Type: <?php echo e($result->module->type); ?> </h4>
                            <p class="text-white" style="font-weight: bold">Post Class Test Score: 
                                <?php if($result->module->type == 'Class Test'): ?>
                                    <?php echo e($result->class_test_score .'/'.$result->module->noofquestions); ?> 
                                    <?php if($result->module->allow_test_retake == 1 && $result->class_test_score < $result->module->noofquestions): ?><a onclick="return confirm('This will clear all your scores for this module. Are you sure you want to do this?');" href="<?php echo e(route('user.retake.module.test', ['module' => $result->module_id, 'p_id'=>$result->program_id])); ?>" style="border-radius: 10px;" class="btn btn-danger btn-sm"><i class="fas fa-redo"></i> Retake</a><?php endif; ?>
                                <?php endif; ?>
                                <?php if($result->module->type == 'Certification Test'): ?>
                                    <?php echo e(($result->certification_test_score > 0) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'); ?>

                                <?php endif; ?>
                            </p>
                            <p class="text-white" style="font-style:italic">Pre Class Test Score: 
                                <?php if($mock_result->module->type == 'Class Test'): ?>
                                    <?php echo e($mock_result->class_test_score .'/'.$mock_result->module->noofquestions); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div> 
            <?php endif; ?>  
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       
       
            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($result->module->type == 'Certification Test'): ?>      
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                           
                        <h5 class="font-light text-white"> <b>Training: </b>  <?php echo e($program->p_name); ?></h5>
                        <h5 class="font-light text-white"> <b>Module: </b><?php echo e($result->module->title); ?></h5>
                            <h4 class="text-white">Test Type: <?php echo e($result->module->type); ?> </h4>
                            <p class="text-white" style="font-weight: bold">Post Class Test Score: 
                                <?php if($result->module->type == 'Certification Test'): ?>
                                    <?php echo e(($result->certification_test_score > 0 ) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'); ?>

                                    <?php if((isset($result->grader_comment) && !empty($result->grader_comment)) || ((isset($result->facilitator_comment) && !empty($result->facilitator_comment)))): ?>
                                        <br>
                                        <a style="width: auto;" href="<?php echo e(route('tests.results.comment', ['id'=>$result->id, 'p_id'=>$program->id])); ?>"
                                            class="btn m-t-20 btn-info btn-block waves-effect waves-light">
                                            <i class="fa fa-eye"></i>View Comments
                                        </a>
                                    <?php else: ?>
                                    <p class="text-white" style="font-style:italic;padding-bottom: 40px;">&nbsp </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div> 
            <?php endif; ?>  
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
        </div>
        <?php endif; ?>
        <?php if($hasmock == 0): ?>
        <div class="row">
            <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>          
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                           
                            <h5 class="font-light text-white"> <b>Training: </b>  <?php echo e($program->p_name); ?></h5>
                        <h5 class="font-light text-white"> <b>Module: </b><?php echo e($result->module->title); ?></h5>
                            <h4 class="text-white">Test Type: <?php echo e($result->module->type); ?> </h4>
                            <b class="text-white">My Score: 
                                <?php if($result->module->type == 'Class Test'): ?>
                                    <?php echo e($result->class_test_score .'/'.$result->module->noofquestions); ?>

                                <?php endif; ?>
                                <?php if($result->module->type == 'Certification Test'): ?>
                                    <?php echo e(isset($result->certification_test_score) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'); ?>

                                <?php endif; ?></b>
                        </div>
                    </div>
                </div>
            </div>   
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- BEGIN MODAL -->
    <div class="modal" id="comments">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>Result Comments</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <?php if(isset($result->grader_comment) && !empty($result->grader_comment)): ?>
                    <?php echo $result->grader_comment; ?>

                    <?php endif; ?>
                    <?php if(isset($result->facilitator_comment) && !empty($result->facilitator_comment)): ?>
                    <?php echo $result->facilitator_comment; ?>

                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/tests/result.blade.php ENDPATH**/ ?>