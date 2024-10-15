<?php $__env->startSection('title', 'Trainings'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php if(session()->get('message')): ?>
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> <?php echo e(session()->get('message')); ?>

            </div>
            <?php endif; ?>

            <h5 class="card-title"> All Trainings</h5>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Program Title</th>
                            <th>Training Fee</th>
                            <th>Early Bird Fee</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Partly Paid</th>
                            <th>Fully Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($program->p_name); ?><br><span
                                    style="color:red">https://portal.employme.ng/paystack?id=<?php echo e($program->id); ?>&t=</span>
                            </td>
                            <td><?php echo e(\App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY'). $program->p_amount); ?></td>
                            <td><?php echo e(\App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$program->e_amount); ?></td>
                            <td><?php echo e($program->p_start); ?></td>
                            <td><?php echo e($program->p_end); ?></td>
                            <td><?php echo e($program->part_paid); ?></td>
                            <td><?php echo e($program->fully_paid); ?></td>
                            <td>
                                <div class="btn-group">
                                    <form action="<?php echo e(route('programs.destroy', $program->id)); ?>" method="POST"
                                        onsubmit="return confirm('Do you really want to delete forever?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete Training"> <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                        <a href="<?php echo e(route('programs.restore', $program->id)); ?>" type="submit" class="btn btn-success btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Restore Training"> <i class="fas fa-trash-restore"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Program Title</th>
                            <th>Training Fee</th>
                            <th>Early Bird Fee</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Partly Paid</th>
                            <th>Fully Paid</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/programs/trash.blade.php ENDPATH**/ ?>