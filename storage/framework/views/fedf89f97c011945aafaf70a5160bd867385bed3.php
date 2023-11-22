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
                <strong>Module: <?php echo e($module_title); ?></strong><br>
                Please Type in your answer in the text boxes under each question
            </p>
          
            <form name="quiz" id="quiz_form" action="<?php echo e(route('tests.store', ['p_id' => $program->id ])); ?>" method="POST" class="pb-2">
                <?php echo e(csrf_field()); ?>


                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="form-group">
                    <label for="title"><?php echo e($i ++ .'. '); ?><?php echo $question->title; ?></label><br>

                    <label for="<?php echo e($question->id); ?>">Your answer <strong style="color:green">( Maximum words: 500 )</strong></label><br>
                    
                    <div class="form-group">
                    <textarea id="text<?php echo e($question->id); ?>" class="answer" style="max-width: 100%;" name="<?php echo e($question->id); ?>" id="<?php echo e($question->id); ?>" rows="20" cols="100"
                        placeholder="Enter your answer for question <?php echo e($i - 1); ?> here" required></textarea>
                    </div>
                    
                    <input type="hidden" name="id" value="<?php echo e($question->id); ?>">
                    <input type="hidden" name="mod_id" value="<?php echo e($question->module->id); ?>">
                </div>
                <script>
                    $('#text<?php echo e($question->id); ?>').keydown(function() {
                        var length = jQuery.trim($(this).val()).split(/\s+/).length;
                        $('#<?php echo e($question->id); ?>').text(length);
                        
                        //stop user input
                        if(length > 500){
                            $(this).prop("maxLength", 1);
                            
                        }else{
                            $(this).removeAttr("maxLength");
                        }
                    });
                    CKEDITOR.replace("text<?php echo e($question->id); ?>");
                </script>
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

    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/tests/certification.blade.php ENDPATH**/ ?>