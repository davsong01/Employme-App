<?php $__env->startSection('title', 'Download materials'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
         </div>
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Materials <a href="<?php echo e(route('creatematerials', $p_id)); ?>"><button type="button" class="btn btn-outline-primary">Add New study Material</button></a></h5> 
            </div>
        </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($material->title); ?></td>
                            <td><?php echo e($material->created_at->format('d/m/Y')); ?></td>
                            <td><?php echo e($material->program->p_name); ?></td>
                            <td>
                                <div class="btn-group">
                                    <?php if(isset($material->program->id)): ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Download Material"
                                        class="btn btn-info" href="<?php echo e(route('getmaterial', ['p_id'=>$material->program->id, 'filename'=> $material->file])); ?>"><i
                                            class="fa fa-download"></i>
                                    </a>
                                    
                                    <?php endif; ?>
                                     <form action="<?php echo e(route('materials.destroy', $material->id)); ?>" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete material"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/teacher/materials/index.blade.php ENDPATH**/ ?>