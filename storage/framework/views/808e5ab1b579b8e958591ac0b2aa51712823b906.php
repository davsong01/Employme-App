<?php $__env->startSection('title', 'Trainings'); ?>
<?php $__env->startSection('css'); ?>
    <style>
       .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table-image {
            width: 85px;
            border-radius: 5px;
            object-fit: cover;
        }
        .btn {
            border-radius: 5px;
            margin: 2px 0;
        }

        .actions-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .export-link {
            color: brown;
            font-weight: bold;
        }

        .export-link:hover {
            text-decoration: underline;
            color: darkred;
        }

        .dropdown {
            position: relative;
            display: block;
        }
        .dropdown-button {
            background-color: #17a2b8;
            color: white;
            padding: 4px 4px;
            font-size: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dropdown-button:hover {
            background-color: #138496; /* Slightly darker shade for hover */
        }
        /* Dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        /* Links inside the dropdown */
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show the dropdown content when the button is clicked */
        .dropdown:hover .dropdown-content {
            display: block;
        }

    </style>
<?php $__env->stopSection(); ?>
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
                            <th>#</th>
                            <th>Banner</th>
                            <th>Title</th>
                            <th>Fee</th>
                            <th>Dates</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                            <th>Actions</th>
                            <?php endif; ?>
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
                                <span class="child-parent-details" style="font-size:10px">
                                    <?php if($program->parent): ?>
                                    <span style="color:blue"> <strong>Parent:</strong><a target="_blank" href="<?php echo e(route('programs.edit', $program->parent->id)); ?>"><?php echo e($program->parent->p_name); ?></span></a><br>
                                    <?php endif; ?>
                                    <?php if($program->subPrograms->count() > 0): ?>
                                    <div class="dropdown">
                                        <button class="dropdown-button">View Children</button>
                                        <div class="dropdown-content">
                                            <?php $__currentLoopData = $program->subPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a target="_blank" href="<?php echo e(route('programs.edit', $p->id)); ?>"><?php echo e($p->p_name); ?></a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                   
                                    
                                    <?php endif; ?>
                                </span>
                                <a href="<?php echo e(route('program.detailsexport', $program->id)); ?>"><span style="color:brown;"><i class="fa fa-download"></i> Export Participant's details</span></a>

                                <?php if($program->status == 1): ?> <br><a  href="<?php echo e(url('/trainings').'/'.$program->id); ?>" target="_blank"> <i class="fa fa-eye"></i> Preview Program</a> <?php endif; ?>  <br>
                                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                                <a data-toggle="tooltip" data-placement="top" title="Edit Training"
                                    class="btn btn-info btn-xs" href="<?php echo e(route('programs.edit', $program->id)); ?>"><i
                                        class="fa fa-edit"></i> Edit
                                </a> 
                                <?php endif; ?>
                                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
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
                            
                            <td><strong>Normal Fee:</strong> <?php echo e(\App\Models\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->p_amount)); ?> <br>
                               <strong>EarlyBird:</strong> <?php echo e(\App\Models\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->e_amount)); ?>

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
                            <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                            <td style="vertical-align: unset;">
                                <div class="" style="margin-bottom: 5px;">
                                    <a data-toggle="tooltip" data-placement="top" title="Reset Participant's password" class="btn btn-dark btn-xs" href="<?php echo e(route('password.reset', $program->id)); ?>" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i> Reset Password
                                    
                                    <?php if($program->close_registration == 0): ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Close registration" class="btn btn-danger btn-xs" href="<?php echo e(route('registration.close', $program->id)); ?>" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i> Close registration
                                    </a>
                                    <?php else: ?>
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Registration"
                                        class="btn btn-success" href="<?php echo e(route('registration.open', $program->id)); ?>" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-window-restore"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php endif; ?>                                   
                                </div>
                                
                                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
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
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('extra-scripts'); ?>
    <script>
        document.querySelector('.dropdown-button').addEventListener('click', function() {
            const dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/programs/index.blade.php ENDPATH**/ ?>