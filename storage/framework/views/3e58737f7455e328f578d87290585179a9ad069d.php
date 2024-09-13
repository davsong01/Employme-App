<?php $__env->startSection('title'); ?>
    <?php echo e(config('app.name') .' Test Management'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <h2 style="color:#008000; text-align:center; padding:20px"><?php echo e(strtoupper($program_name->p_name)); ?> <br> MODULE MANAGEMENT</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
        <a href="<?php echo e(route('modules.index')); ?>">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> <?php echo e($modules->count()); ?> Module(s)</h6>
                </div>
            </div>
        </a>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
        <a href="<?php echo e(route('questions.show', $p_id)); ?>">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                <h6 class="text-white"><b></b> <?php echo e($questions_count); ?> Questions</h6>
                </div>
            </div>
        </a>
        </div>
        
        <div class="col-md-4 col-lg-4">
        <a href="<?php echo e(route('scoreSettings.index')); ?>">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-cog"></i></h1>
                <h6 class="text-white"><b></b> Score Settings </h6>
                </div>
            </div>
        </a>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Modules <?php if(auth()->user()->role_id == "Admin"): ?><a href="<?php echo e(route('modules.create', ['p_id' => $program_name->id] )); ?>"><button type="button" class="btn btn-outline-primary">Add New Module </button></a><?php endif; ?> </h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Title</th>                            
                            <th>Associated Training</th>
                            <th>Expected Questions</th>
                            <th>Set Questions</th>
                            <th>Question Time</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>

                            <td><?php echo e($module->created_at->format('d/m/Y')); ?></td>
                            <td><?php echo e($module->title); ?><br><span style="color: red"><?php echo e($module->type); ?></span></td>
                            <td><?php echo e($module->program->p_name); ?></td>
                            <td><?php echo e($module->noofquestions); ?></td>
                            <td><?php echo e($module->questions->count()); ?></td>
                            <td><?php echo e($module->time); ?> minutes</td>
                            <td><?php echo e($module->type); ?></td>
                            <td><?php echo e($module->status == 0 ? 'Disabled' : 'Enabled'); ?></td>
                           
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Module"
                                        class="btn btn-info" href="<?php echo e(route('modules.edit', $module->id)); ?>" onclick="return confirm('Are you really sure?');"><i
                                            class="fa fa-edit"></i>
                                    </a>

                                    <?php if($module->status == 0): ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Enable Module Questions"
                                        class="btn btn-secondary" href="<?php echo e(route('modules.enable', $module->id)); ?>" onclick="return confirm('Are you really sure?');"><i
                                            class="fa fa-check"></i>
                                    </a>
                                    <?php else: ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Disable Module Questions"
                                        class="btn btn-info" href="<?php echo e(route('modules.disable', $module->id)); ?>" ><i
                                        onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                        <?php if($module->questions->count() > 0): ?>
                                        <a data-toggle="tooltip" data-placement="top" title="Clone Module"
                                            class="btn btn-info" href="<?php echo e(route('modules.show', $module->id)); ?>"><i
                                                class="fa fa-clone"></i>
                                        </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if($module->status == 0): ?>
                                    <form action="<?php echo e(route('modules.destroy', $module->id)); ?>" method="POST"
                                        onsubmit="return confirm('Do you really want to Delete?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip" data-placement="top" title="Delete module"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/modules/show.blade.php ENDPATH**/ ?>