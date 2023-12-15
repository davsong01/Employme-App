<?php $__env->startSection('title', 'Trainings'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Trainings <a href="<?php echo e(route('programs.create')); ?>"><button type="button" class="btn btn-outline-primary">Add New Training</button></a></h5> 
                </div> 
                
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Banner</th>
                            <th>Title</th>
                            <th>Fee</th>
                            <th>Dates</th>
                            <th>Payment Stats</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td> <img src="<?php echo e(url('/').'/'.$program->image); ?>" alt="banner" style="width: 85px;"> </td> 
                            <td><?php echo e($program->p_name); ?><br>
                                <strong>Type:</strong> <?php if($program->off_season): ?>Off Season <?php else: ?> Normal <?php endif; ?> <br>
                                <?php if($program->e_amount > 0): ?>  <button class="btn btn-danger btn-xs">Discounted</button> <?php endif; ?>
                                <?php if($program->parent): ?>
                                <strong>Parent:</strong> <span style="color:red"> <?php echo e($program->parent->p_name); ?></span><br>
                                <?php endif; ?>
                                <a href="<?php echo e(route('program.detailsexport', $program->id)); ?>"><span style="color:brown;font-size: smaller;"><i class="fa fa-download"></i> Export Participant's details</span></a>
                                <?php if($program->status == 1): ?> <br><a style="font-size: smaller;" href="<?php echo e(url('/trainings').'/'.$program->id); ?>" target="_blank"> <i class="fa fa-eye"></i> Preview Program</a> <?php endif; ?>  <br>
                                <a data-toggle="tooltip" data-placement="top" title="Edit Training"
                                    class="btn btn-info btn-xs" href="<?php echo e(route('programs.edit', $program->id)); ?>"><i
                                        class="fa fa-edit"></i> Edit
                                </a> 
                                <?php if($program->hascrm == 0): ?>
                                    <a data-toggle="tooltip" onclick="return confirm('Are you really sure?');" data-placement="top" title="Enable CRM"
                                        class="btn btn-primary btn-xs" href="<?php echo e(route('crm.show', $program->id)); ?>" ><i
                                            class="far fa-comments"></i> Enable CRM
                                    </a>
                                    <?php else: ?>
                                    <a data-toggle="tooltip" onclick="return confirm('Are you really sure?');"  data-placement="top" title="Disable CRM"
                                        class="btn btn-primary btn-xs" href="<?php echo e(route('crm.hide', $program->id)); ?>" ><i class="fa fa-ban"> Disable CRM</i>
                                    </a>
                                    
                                <?php endif; ?>
                                <?php if($program->hasresult == 0): ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Enable User Results"
                                        class="btn btn-success btn-xs" href="<?php echo e(route('results.enable', $program->id)); ?>" onclick="return confirm('Are you really sure?');"><i class="fa fa-graduation-cap"></i> Enable result
                                    </a>
                                    <?php else: ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Disable User Results"
                                        class="btn btn-info btn-xs" href="<?php echo e(route('results.disable', $program->id)); ?>" ><i onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i> Disable Result
                                    </a>
                                <?php endif; ?>

                            </td>
                            
                            <td><strong>Normal Fee:</strong> <?php echo e(\App\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->p_amount)); ?> <br>
                               <strong>EarlyBird:</strong> <?php echo e(\App\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->e_amount)); ?>

                            
                            </td>
                            <td> <strong>Start:</strong> <?php echo e($program->p_start); ?> <br>
                                <strong>End: </strong><?php echo e($program->p_end); ?>

                            </td>
                            <td>Part: <?php echo e($program->part_paid); ?> <br>
                                Full: <?php echo e($program->fully_paid); ?>

                            </td>
                           
                            <td>
                                <?php if( $program->status == 1 ): ?>
                                <button class="btn btn-success btn-xs">Published</button> 
                                <?php else: ?>
                                <button class="btn btn-danger btn-xs">Draft</button> 
                                <?php endif; ?>
                              
                            </td>
                            <td style="vertical-align: unset;">
                                <div class="" style="margin-bottom: 5px;">
                                    
                                    <?php if($program->close_registration == 0): ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Close registration" class="btn btn-danger btn-xs" href="<?php echo e(route('registration.close', $program->id)); ?>" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i>Close registration
                                    </a>
                                    <?php else: ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Registration"
                                        class="btn btn-success" href="<?php echo e(route('registration.open', $program->id)); ?>" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-window-restore"></i>
                                    </a>
                                    <?php endif; ?>

                                   
                                </div>
                                <div class="" style="margin-bottom: 5px;">
                                    
                                    <a data-toggle="tooltip" data-placement="top" title="Clone Training"
                                        class="btn btn-success btn-xs" style="background:#183153" href="<?php echo e(route('training.clone', $program->id)); ?>" onclick="return confirm('This will clone training materials, modules, questions, settings, etc?');"><i class="fa fa-copy"></i> Clone Training
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Import Participants"
                                        class="btn btn-dark btn-xs" style="background:#183153" href="<?php echo e(route('training.import', $program->id)); ?>"><i class="fa fa-upload"></i> Bulk Import
                                    </a>
                                    <form action="<?php echo e(route('programs.destroy', $program->id)); ?>" method="POST"
                                        onsubmit="return confirm('Do you really want to trash?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-warning btn-xs" data-toggle="tooltip"
                                            data-placement="top" title="Trash Training"> <i class="fa fa-recycle"></i> Trash
                                        </button>
                                    </form>
                                </div>
                                <?php if($program->e_amount > 0): ?>
                                <div class="extra-actions" style="padding-top:10px">
                                    <?php if($program->close_earlybird == 1): ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Close Early Bird Payment"
                                            class="btn btn-info" href="<?php echo e(route('earlybird.close', $program->id)); ?>" ><i
                                            onclick="return confirm('Are you really sure?');" class="fa fa-folder-open"></i>
                                    </a>
                                    <?php else: ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Early Bird Payment"
                                            class="btn btn-info" href="<?php echo e(route('earlybird.open', $program->id)); ?>" ><i
                                            onclick="return confirm('Are you really sure?');" class="fa fa-folder"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/programs/index.blade.php ENDPATH**/ ?>