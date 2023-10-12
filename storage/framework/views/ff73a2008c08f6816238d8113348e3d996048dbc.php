<?php $__env->startSection('title'); ?>
<?php echo e(config('app.name')); ?> Questions
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
     <div class="card">
        <div class="card-body">
            <div class="card-header" style="color:#008000; text-align:center; padding:20px">
                <h2><?php echo e(strtoupper($p_name)); ?></h2> <h3 style="text-align:center; padding:20px"> QUESTION MANAGEMENT</h3><br>
                 <a href="<?php echo e(route('questions.add', ['p_id'=> $p_id])); ?>"><button style="align:left" type="button" class="btn btn-outline-primary" >Add New question</button></a></h5><br> <br>
                    <a href = "<?php echo e(route('questions.import', ['p_id' => $p_id])); ?>" class="btn btn-custon-four btn-success"><i class="fa fa-upload"></i> Import Questions</a>
            </div>
        </div>
    </div>
     <div class="card">
        <div class="card-body">
           <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Title</th>                            
                            <th>Associated Module</th>
                            <th>Correct Option</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($question->created_at->format('d/m/Y')); ?></td>
                            <td><?php echo $question->title; ?></td>
                            <td><?php echo e($question->module->title); ?></td>
                            <td><?php echo e($question->correct); ?></td>
                           
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit question"
                                        class="btn btn-info" href="<?php echo e(route('questions.edit', $question->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>

                                    <form action="<?php echo e(route('questions.destroy', $question->id)); ?>" method="POST"
                                        onsubmit="return confirm('Do you really want to Delete forever?');">
                                        
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-warning" data-toggle="tooltip"
                                            data-placement="top" title="Delete Questions"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                          
                            
                        </tr> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
<script>
    $(".delete").on("submit", function () {
        return confirm("Are you sure?");
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/questions/show.blade.php ENDPATH**/ ?>