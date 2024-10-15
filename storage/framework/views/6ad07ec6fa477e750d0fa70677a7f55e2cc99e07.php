<?php $__env->startSection('title', 'Add study material'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4 class="card-title">Add new study Material</h4>
                    </div>
                    <form action="<?php echo e(route('materials.store')); ?>" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <!--Gives the first error for input name-->

                        <div><small><?php echo e($errors->first('title')); ?></small></div>
                        <div class="form-group">

                            <label for="class">Select Training *</label>

                            <select name="program_id" id="program_id" class="form-control" required>

                                <option value=""></option>

                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($program->id); ?>"><?php echo e($program->p_name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div><small style="color:red"><?php echo e($errors->first('program_id')); ?></small></div>
                        </div>

                        <div class="form-group">
                            <label>Select files</label>
                            <input type="file" id="file" name="file[]" value="" class="form-control" multiple>
                        </div>
                        <div><small style="color:red"><?php echo e($errors->first('file[]')); ?></small></div>
                            <div class="progress2">

                            <div class="bar2"></div >

                            <div class="percent">0%</div >

                        </div>
                        <br>
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

                        <?php echo e(csrf_field()); ?>

                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

var SITEURL = "<?php echo e(URL('/')); ?>";

$(function() {

    $(document).ready(function()

    {

        var bar = $('.bar');

        var percent = $('.percent');

          $('form').ajaxForm({

            beforeSend: function() {

                var percentVal = '0%';

                bar.width(percentVal)

                percent.html(percentVal);

            },

            uploadProgress: function(event, position, total, percentComplete) {

                var percentVal = percentComplete + '%';

                bar.width(percentVal)

                percent.html(percentVal);

            },

            complete: function(xhr) {

                alert('Study Material has Been Uploaded Successfully');

                window.location.href = SITEURL +"/"+"ajax-file-upload-progress-bar";

            }

          });

    }); 

 });

</script>

    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/materials/create.blade.php ENDPATH**/ ?>