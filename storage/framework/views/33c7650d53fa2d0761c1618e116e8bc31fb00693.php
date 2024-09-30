<?php $__env->startSection('title', 'Download Certificate'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="">
                <table id="" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                        
                            <th>Name</th>
                            <th>Program</th>
                            <th>Action</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <div class="text-center">
                            <h5 class="card-title">Please Download your certificate below</h5>
                        </div>
                        <tr>
                            <td><?php echo e($certificate->user->name); ?> <br>
                            </td>
                            <td><?php echo e($certificate->program->p_name); ?></td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-info" href="/certificate/<?php echo e($certificate->file); ?>"><i
                                    class="fa fa-download"> Download Certificate</i></a>
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    // $('#zero_config').DataTable();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/certificates/index.blade.php ENDPATH**/ ?>