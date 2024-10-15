<?php $__env->startSection('css'); ?>

<style>
  /* The Modal (background) */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black w/ opacity */
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  /* The Close Button */
  .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Download Study materials'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
  <div class="card">
    <div class="card-body">
        <div class="card-title">
            <h2 style="color:green; text-align:center; padding:20px"><?php echo e(strtoupper($program->p_name)); ?> STUDY MATERIALS</h2>
            <h5>Study Materials</h5>
        </div>
        <div class="">
            <table id="zero_config" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Date Uploaded</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($i++); ?></td>
                        <td>
                            <a data-toggle="tooltip" data-placement="top" title="Download Material"
                            class="btn btn-info" href="<?php echo e(route('getmaterial', ['p_id'=>$program->id, 'filename'=> $material->file])); ?>"><i
                                class="fa fa-download"> <?php echo e($material->title); ?></i>
                            </a>
                        </td>
                        <td><?php echo e($material->created_at->format('d/m/Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                
            </table>
        </div>
    </div>
  </div>
</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/materials/index.blade.php ENDPATH**/ ?>