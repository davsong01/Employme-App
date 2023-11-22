<?php $__env->startSection('title', 'My Tests'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>All Tests</h4>
                <h6 style="color:red">Please read the following carefully before you proceed to take a test</h6>
                <div>
                    <ul>
                        <li>All Tests are timed, if you run out of time, the test gets submitted automatically with all
                            answered questions</li>
                        <li>Tests with Type: <strong>Certification</strong> are open ended tests, you will be required
                            to type in your input</li>
                        <li>Tests with Type: <strong>Class Test</strong> are multiple choice tests, you will be required
                            to select the correct option</li>
                        <li>When a test ends, you will be redirected to the result page where you will see your score in the corresponding card</li>
                        <li>Make you sure you have a stable internet while taking a test</li>
                        
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <h5>All Tests</h5>
            </div>
            <?php if($modules->count() < 1): ?>
            <div>
                <h6 style="color:red">No tests Available yet! Check back later</h6>
            </div>
            <?php endif; ?>
        </div>
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4 col-lg-4">
                <div class="card" style="background-color: #eeeeee; margin-bottom:5px">
                    <div class="box bg-white text-center"
                        style="color:blue !important; border: blue 1px solid;  border-radius: 5px;">
                        <h1 class="font-light text-blue"><i class=" fa fa-list-alt"></i></h1>
                        <div class="card-title">
                            <?php echo e($module->title); ?>

                            <?php if(auth()->user()->redotest == 1 && $module->type == 'Certification Test'): ?>
                                <span class="redo" style="background: red;color: white;padding: 4px;">RETAKE</span>
                            <?php endif; ?>
                        </div>
                        <h6 class="text-blue">Type: <?php echo e($module->type); ?> </h6>
                        <p class="text-blue">No of Questions: <?php echo e($module->questions->count()); ?> </p>
                        <p class="text-blue">Time: <?php echo e($module->time); ?>minutes </p>
                         
                        <?php if($module->completed == 0): ?>
                            <a href="<?php echo e(route('tests.show', ['test' => $module->id, 'p_id' => $program->id])); ?>"><button style="width:100%" type="button" class="btn btn-outline-primary" onclick="return confirm('I have read the instructions above?');">Start Now!</button></a>
                        <?php endif; ?>
                       
                        <?php if($module->completed == 1 && auth()->user()->redotest == 0): ?>
                            <a href="<?php echo e(route('tests.results', ['p_id' => $program->id])); ?>"><button style="width:100%" type="button" class="btn btn-outline-success"><b>Test Completed! View Details </b></button></a>
                        <?php endif; ?>
                        <?php if(auth()->user()->redotest != 0 && $module->redo != 0): ?>
                            <a href="<?php echo e(route('tests.results', ['p_id' => $program->id])); ?>"><button style="width:100%" type="button" class="btn btn-outline-success"><b>Test Completed! View Details </b></button></a>
                        <?php endif; ?>
                        <?php if($module->type == 'Certification Test' && auth()->user()->redotest != 0 && $module->redo == 0): ?> 
                            <a href="<?php echo e(route('tests.show', ['test' => $module->id, 'p_id' => $program->id])); ?>"><button style="width:100%" type="button" class="btn btn-outline-primary" onclick="return confirm('I have read the instructions above?');">Start Now!</button></a>
                        <?php endif; ?>
                       
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/tests/index.blade.php ENDPATH**/ ?>