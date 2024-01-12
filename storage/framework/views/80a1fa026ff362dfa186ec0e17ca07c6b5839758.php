<?php $__env->startSection('title', 'Add New question'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <form action="<?php echo e(route('users.import')); ?>" method="POST" name="importform" class="pb-2"  enctype="multipart/form-data">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="form-group">
                                    <label for="class">Upload File </label> <a class="pull-right" style="font-size:smaller;" href="<?php echo e(route('user-bulk-sample', 'bulk_users.xlsx')); ?>"><i class="fa fa-download"></i> Download sample</a>
                                   <input type="file" name="file" class="form-control" accept=".csv, .xlsv, .xls, .xlsx" required>
                                    <br>
                                    <?php if($errors->has('file')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('file')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" value="<?php echo e($p_id); ?>" name="p_id">
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/users/import.blade.php ENDPATH**/ ?>