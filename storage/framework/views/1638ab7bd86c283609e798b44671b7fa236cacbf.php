<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name') .' CRM Tool'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> <?php echo e($complains->count()); ?> Query(s)</h6>

                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                    <h6 class="text-white"><b></b><?php echo e($resolvedComplains); ?> Query(s) Resolved</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="fa fa-spinner"></i></h1>
                    <h6 class="text-white"><b> <?php echo e($InProgressComplains); ?> </b> In Progress</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-danger text-center">
                    <h1 class="font-light text-white"><i class="fa fa-clock"></i></h1>
                    <h6 class="text-white"><?php echo e($pendingComplains); ?> Query(s) Pending</h6>
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
                    <h5 class="card-title"> All Queries <a href="<?php echo e(route('complains.create')); ?>"><button type="button" class="btn btn-outline-primary">Add New Query</button></a></h5> 
                </div>
            </div>
            
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Assignee</th>                            
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>SLA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $complains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $complain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>EMPL000<?php echo e($complain->id); ?> <br>
                               
                            </td>
                            <td><?php echo e($complain->user->name ?? 'NOT SET'); ?> <span style="color:blue">(<?php echo e($complain->user->responseStatus ?? '0'); ?>% Response Rate)</span> <br>
                                <?php if(isset($complain->program)): ?>
                                 <small style="color:green"><strong>Training:</strong> <?php echo e($complain->program->p_name ?? ''); ?></small>
                                 <?php endif; ?>
                            </td>
                            <td><?php echo e($complain->created_at->format('d/m/Y')); ?></td>
                            <td><?php echo e($complain->status); ?></td>
                            <td><?php echo e($complain->sla); ?> <?php echo e($complain->sla ? 'hours' : ''); ?></td>
                            <td>
                                <div class="btn-group">

                                    <a class="btn btn-info" href="<?php echo e(route('complains.edit', $complain->id)); ?>"><i
                                            class="fa fa-eye"></i> View</a>
                                    <?php if($complain->status <> 'Resolved'): ?>
                                        <a 
                                            class="btn btn-success" href="<?php echo e(route('crm.resolved', $complain->id)); ?>"><i
                                                class="fa fa-check"></i> Resolve</a>
                                    <?php endif; ?>
                                     <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                                        <form action="<?php echo e(route('complains.destroy', $complain->id)); ?>" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            <?php echo e(csrf_field()); ?>

                                            <?php echo e(method_field('DELETE')); ?>


                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete Query"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/complains/index.blade.php ENDPATH**/ ?>