<?php $__env->startSection('css'); ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    .select2-container--default .select2-selection--multiple {
        width: 100% !important; /* Force full width */
    }

    .select2-container {
        width: 50% !important; /* Force full width */
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black; /* Text color for selected items */
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: black; /* Text color for the rendered selections */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black; /* Text color for the single selected item */
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: black; /* Text color for the placeholder */
    }

    .select2-container--default .select2-results__option {
        color: black; /* Text color for the dropdown options */
    }
    

    .badge {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px;
        height: 45px;
        background-color: #4CAF50;
        border-radius: 50%;
        color: white;
        font-size: 10px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .transaction-count {
        text-align: center;
    }
    .search-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-control {
        border-radius: 20px;
    }

    .rounded {
        border-radius: 20px !important;
    }
    .btn-search {
        border-radius: 20px;
        transition: background-color 0.3s;
    }
    .btn-search:hover {
        background-color: #0056b3;
    }

    .btn.active {
        background-color: #0056b3;
        color: white;
        border: 4px solid black;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        transform: scale(1.05); 
        transition: all 0.3s;
    }

    .btn:not(.active):hover {
        transform: scale(1.05); 
    }

    .button-container .btn {
        border-radius: 8px;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease; 
    }

    .button-container .btn:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .button-container .btn:disabled {
        opacity: 0.6;
    }

    .button-container .fa-unlock {
        margin-right: 0.25rem; 
    }


    

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'All Participants'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-header">
                    <div>
                        <h5 class="card-title">All Participants</h5>
                        </h5>
                        <br>
                        <div class="card-body">
                            <?php
                                $currentStatus = request('status');
                            ?>
                            <a href="<?php echo e(route('users.create')); ?>"><button type="button" class="btn btn-outline-primary rounded">Add New Participant</button></a>
                            
                            
                            <div class="badge float-right">
                                <span class="transaction-count"><?php echo e($records); ?></span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <form class="form-inline search-form" method="GET" action="<?php echo e(route('users.index')); ?>">
                                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                                
                                <div class="form-group mx-sm-1 mb-2">
                                    <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="<?php echo e(request('staffID')); ?>">
                                </div>
                                <div class="form-group mx-sm-1 mb-2">
                                    <select name="program_id" id="" class="form-control w-75">
                                        <option value="">Select Training</option>
                                        <?php $__currentLoopData = $allPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($training->id); ?>"><?php echo e($training->p_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group mx-sm-1 mb-2">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo e(request('name')); ?>">
                                </div>
                                <div class="form-group mx-sm-1 mb-2">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo e(request('email')); ?>">
                                </div>
                                <div class="form-group mx-sm-1 mb-2">
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="<?php echo e(request('phone')); ?>">
                                </div>
                                <button type="submit" class="btn btn-primary btn-search mb-2">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Trainings</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <tr>
                            
                            <td><?php echo e($i++); ?></td>
                            <td> <strong>Name:</strong> <?php echo e($user->name); ?> <br>
                                <strong>Email: </strong><?php echo e($user->email); ?> <br>
                                <strong>Staff ID: </strong><?php echo e($user->staffID); ?> <br>
                                <strong>Phone: </strong><?php echo e($user->t_phone); ?> <br>
                                <strong>Account Balance:</strong> <?php echo e(number_format($user->account_balance)); ?> <br>
                                <strong>Date Added:</strong> <?php echo e($user->created_at->format('d/m/Y')); ?> <br>
                                <strong>Trainings count: </strong><?php echo e($user->programs()->count()); ?>

                            </td>
                            

                            <b style="display:none"><?php echo e($count = 1); ?></b>
                            <td>
                                <?php echo e($user->name); ?> <br>
                                <?php $__currentLoopData = $user->programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <small style="color:green"><?php echo e($count ++); ?>.
                                    <?php echo e($programs->p_name); ?> <br></small>
                                    <hr style="margin-top: 2px; margin-bottom: 2px; border-top: 1px solid rgb(34, 85, 164);">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td> 
                           
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit User"
                                        class="btn btn-info btn-sm" href="<?php echo e(route('users.edit', $user->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                        class="btn btn-warning btn-sm" href="<?php echo e(route('impersonate', $user->id)); ?>"><i
                                            class="fa fa-unlock"></i>
                                    </a>
                                    <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/users/index.blade.php ENDPATH**/ ?>