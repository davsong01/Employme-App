<?php $__env->startSection('title', 'My Tests'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="header" id="myHeader">
            <h4>
                <div style="font-weight: bold;" id="quiz-time-left"></div>
            </h4>
        </div>

        <div class="content">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <p><strong>Training: <?php echo e($program_name); ?></strong><br>
                <strong>Module: <?php echo e($module_title); ?></strong><br><br>
                Select the correct answer
            </p>
            <form name="quiz" id="quiz_form"
                action="<?php echo e(route('tests.store', ['p_id' => $program->id])); ?>"
                method="POST" class="pb-2">
                <?php echo e(csrf_field()); ?>

                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group">
                        <label for="name"><?php echo e($i ++ .'. '); ?><?php echo $question->title; ?>


                        </label><br>
                        <input type="radio" id="<?php echo e($question->id); ?><?php echo e($question->optionA); ?>"
                            name="<?php echo e($question->id); ?>" value="A" required>
                        <label
                            for="<?php echo e($question->id); ?><?php echo e($question->optionA); ?>"><?php echo e($question->optionA); ?></label><br>

                        <input type="radio" id="<?php echo e($question->id); ?><?php echo e($question->optionB); ?>"
                            name="<?php echo e($question->id); ?>" value="B" required>
                        <label
                            for="<?php echo e($question->id); ?><?php echo e($question->optionB); ?>"><?php echo e($question->optionB); ?></label><br>

                        <input type="radio" id="<?php echo e($question->id); ?><?php echo e($question->optionC); ?>"
                            name="<?php echo e($question->id); ?>" value="C" required>
                        <label
                            for="<?php echo e($question->id); ?><?php echo e($question->optionC); ?>"><?php echo e($question->optionC); ?></label><br>

                        <input type="radio" id="<?php echo e($question->id); ?><?php echo e($question->optionD); ?>"
                            name="<?php echo e($question->id); ?>" value="D" required>
                        <label
                            for="<?php echo e($question->id); ?><?php echo e($question->optionD); ?>"><?php echo e($question->optionD); ?></label><br>

                        <input type="hidden" name="id" value="<?php echo e($question->id); ?>">
                        <input type="hidden" name="mod_id" value="<?php echo e($question->module->id); ?>">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="row">
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.onscroll = function () {
            myFunction()
        };

        var header = document.getElementById("myHeader");
        var sticky = header.offsetTop;

        function myFunction() {
            if (window.pageYOffset > sticky) {
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }
        }

        var max_time = <?php echo e($time); ?>;
        var c_seconds = 0;
        var total_seconds = 60 * max_time;
        max_time = parseInt(total_seconds / 60);
        c_seconds = parseInt(total_seconds % 60);
        document.getElementById("quiz-time-left").innerHTML = 'Time Left: ' + max_time + ' minutes ' + c_seconds +
            ' seconds';

        function init() {
            document.getElementById("quiz-time-left").innerHTML = 'Time Left: ' + max_time + ' minutes ' + c_seconds +
                ' seconds';
            setTimeout("CheckTime()", 999);
        }

        function CheckTime() {
            document.getElementById("quiz-time-left").innerHTML = 'Time Left: ' + max_time + ' minutes ' + c_seconds +
                ' seconds';
            if (total_seconds <= 0) {
                setTimeout('document.quiz.submit()', 1);

            } else {
                total_seconds = total_seconds - 1;
                max_time = parseInt(total_seconds / 60);
                c_seconds = parseInt(total_seconds % 60);
                setTimeout("CheckTime()", 999);
            }

        }

        init();
    </script>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/tests/quizz.blade.php ENDPATH**/ ?>