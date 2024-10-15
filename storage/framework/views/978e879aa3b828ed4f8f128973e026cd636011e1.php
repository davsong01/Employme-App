<?php $__env->startSection('title'); ?>
<?php echo e(config('app.name') .' CRM Tool'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            <h5 style="color:green; text-align:center; padding:10px"><?php echo e(strtoupper($program->p_name)); ?></h5>
        </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                <h6 class="text-white"><b></b> <?php echo e($complains->count()); ?> Query(s)</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white"><b></b><?php echo e($resolvedComplains); ?> Query(s) Resolved</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white"><b> <?php echo e($InProgressComplains); ?> </b> In Progress</h6> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-6">
            <div class="card card-hover">
                <div class="box bg-danger text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white"><?php echo e($pendingComplains); ?> Query(s) Pending</h6>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white">My Response Percentage: <?php echo e(Auth::user()->responseStatus); ?>%</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Queries <a href="<?php echo e(route('complains.create', ['p_id'=>$program])); ?>"><button type="button" class="btn btn-outline-primary">Add New Query</button></a></h5> 
                </div>
            </div>
            

            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Date Created</th>
                            <th>Date Updated</th>
                            <th>Name of Complainant</th>
                            <th>Status</th>
                            <th>SLA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $complains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $complain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>EMPL000<?php echo e($complain->id); ?></td>
                            <td><?php echo e($complain->created_at->format('d/m/Y')); ?></td>
                            <td><?php echo e($complain->updated_at->format('d/m/Y')); ?></td>
                            <td><?php echo e($complain->name); ?></td>
                            <td><?php echo e($complain->status); ?></td>
                            <td><?php echo e($complain->sla); ?> <?php echo e($complain->sla ? 'hours' : ''); ?></td>
                            <td>
                                <div class="btn-group">
                                <a class="btn btn-info" href="<?php echo e(route('complains.edit', ['complain' =>$complain->id, 'p_id'=>$program])); ?>"><i class="fa fa-eye"> View</i></a>             
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
        $(".delete").on("submit", function(){
            return confirm("Are you sure?");
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/complains/index.blade.php ENDPATH**/ ?>