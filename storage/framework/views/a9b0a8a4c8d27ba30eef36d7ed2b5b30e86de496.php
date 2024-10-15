<?php $__env->startSection('title', 'Pre class Tests'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>Pre Class Tests</h4>
                <h6 style="color:red">Please read the following carefully before you proceed to take a test</h6>
                <div>
                    <ul>
                        <li>All Tests are timed, if you run out of time, the test gets submitted automatically with all
                            answered questions</li>
                        <li>Tests with Type: <strong>Certification</strong> are open ended tests, you will be required
                            to type in your input</li>
                        <li>Tests with Type: <strong>Class Test</strong> are multiple choice tests, you will be required
                            to select the correct option</li>
                        <li>When a test ends, your answers will be saved and will be available after Post Class Tests</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <h5>All Pre Class Tests</h5>
            </div>
            <?php if($modules->count() < 1): ?>
            <div>
                <h6 style="color:red">No Pre Tests Available yet! Check back later</h6>
            </div>
            <?php endif; ?>
        </div>
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4 col-lg-4">
                <div class="card" style="background-color: #eeeeee; margin-bottom:5px">
                    <div class="box bg-white text-center"
                        style="color:indigo !important; border: indigo 1px solid;  border-radius: 5px;">
                        <h1 class="font-light text-blue"><i class=" fa fa-list-alt"></i></h1>
                        <div class="card-title">
                            <?php echo e($module->title); ?>

                        </div>
                        <h6 class="text-blue">Type: <?php echo e($module->type); ?> </h6>
                        <p class="text-blue">No of Questions: <?php echo e($module->questions->count()); ?> </p>
                        <p class="text-blue">Time: <?php echo e($module->time); ?>minutes </p>
                        <?php if($module->completed == 0): ?>
                            <a href="<?php echo e(route('mocks.show', ['mock' => $module->id, 'p_id' => $program->id])); ?>"><button style="width:100%" type="button" class="btn btn-outline-info" onclick="return confirm('I have read the instructions above?');">Start Now!</button></a>
                        <?php endif; ?>
                        <?php if($module->completed == 1): ?>
                            <a><button style="width:100%" type="button" class="btn btn-outline-success disabled"><b>Pre Test Completed!</b></button></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/pretests/index.blade.php ENDPATH**/ ?>