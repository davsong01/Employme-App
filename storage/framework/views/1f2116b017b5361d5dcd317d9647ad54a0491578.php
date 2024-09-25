<?php $__env->startSection('title', 'Test Results'); ?>
<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('modal.css')); ?>" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    .select2-container--default .select2-selection--multiple {
        width: 100% !important; /* Force full width */
    }

    .select2-container {
        width: 100% !important; /* Force full width */
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
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-header">
                    <div>
                        <h5 class="card-title"> 
                            <?php echo $title; ?>

                        </h5>
                        <br>
                        <div class="card-body">
                            <?php
                                $currentStatus = request('status');
                            ?>
                            <a href="<?php echo e(route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id])); ?>">
                                <button class="btn btn-dark rounded <?php echo e(is_null($currentStatus) ? 'active' : ''); ?>">All</button>
                            </a>
                            <a href="<?php echo e(route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id])); ?>?<?php echo e(http_build_query(array_merge(request()->query(), ['status' => 'yes']))); ?>">
                                <button class="btn btn-success rounded <?php echo e($currentStatus === 'yes' ? 'active' : ''); ?>">Has Tests</button>
                            </a>
                            <a href="<?php echo e(route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id])); ?>?<?php echo e(http_build_query(array_merge(request()->query(), ['status' => 'no']))); ?>">
                                <button class="btn btn-danger rounded <?php echo e($currentStatus === 'no' ? 'active' : ''); ?>">Pending Tests</button>
                            </a>

                            <a class="btn btn-info rounded" href="javascript:void(0)" data-toggle="modal" data-target="#exportmodal"><i class="fa fa-download"></i> Export <?php echo e($page == 'results' ? 'Post' : 'Pre'); ?> Test Results</a>
                            <div class="badge float-right">
                                <span class="transaction-count"><?php echo e($records); ?></span> <!-- Number of transactions -->
                            </div>
                        </div>
                        <div class="mt-4">
                            <form class="form-inline search-form" method="GET" action="<?php echo e(route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id])); ?>">
                                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="<?php echo e(request('staffID')); ?>">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo e(request('name')); ?>">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="<?php echo e(request('email')); ?>">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
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
                            <th>Date</th>
                            <th>Details</th>
                            <th>Test Scores</th>
                            <?php if($page == 'results'): ?>
                            <th>Admin Details</th>
                            <?php endif; ?>
                            <th>Passmark</th>
                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?><th>Total</th>
                            <?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($user->passmark): ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td>
                                <?php if($page == 'mocks'): ?>
                                <?php echo e((($user->mocks->count() > 0)) ? $user->mocks->last()->created_at->format('d/m/Y') : ''); ?>

                                <?php else: ?> 
                                <?php echo e((($user->results->count() > 0)) ? $user->results->last()->created_at->format('d/m/Y') : ''); ?>

                                <?php endif; ?>
                            </td>
                            <td><?php echo e($user->name); ?>

                                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role()))): ?>
                                    <br><b>StaffID</b>: <i><?php echo e($user->staffID); ?></i>
                                    <br><b>Email:</b> <i><?php echo e($user->email); ?></i>
                                    <?php if($user->phone): ?>
                                    <br><b>Phone</b> <i><?php echo e($user->phone); ?></i>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="button-container">
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                        <a target="_blank" data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                        class="btn btn-dark btn-sm w-50 mb-3" href="<?php echo e(route('impersonate', $user->user_id)); ?>"><i
                                            class="fa fa-unlock"> Peek</i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <td>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                    <?php
                                        $total = ((!empty($score_settings->certification) && $score_settings->certification > 0) ? $user->total_cert_score : 0 )
                                        + ((!empty($score_settings->class_test) && $score_settings->class_test > 0 ) ? $user->final_ct_score : 0)
                                        + ((!empty($score_settings->email) && $score_settings->email > 0 ) ? $user->total_email_test_score : 0)
                                        + ((!empty($score_settings->role_play) && $score_settings->role_play > 0) ? $user->total_role_play_score : 0) 
                                        + ((!empty($score_settings->crm_test) && $score_settings->crm_test > 0) ?  $user->total_crm_test_score : 0);
                                    ?>

                                    <?php if(isset($score_settings->class_test) && $score_settings->class_test > 0): ?>
                                        <strong class="tit">Class Tests:</strong> <?php echo e($user->final_ct_score); ?>% <br> <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                    <?php if(isset($score_settings->certification) && $score_settings->certification > 0): ?>
                                    <strong>Certification: </strong> <?php echo e(isset($user->total_cert_score ) ? $user->total_cert_score : ''); ?>% <br>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?>
                                        <?php if(isset($score_settings->role_play) && $score_settings->role_play > 0): ?>
                                        <strong class="tit">Role Play: </strong> <?php echo e($user->total_role_play_score); ?>%  <br> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?>
                                        <?php if(isset($score_settings->crm_test) && $score_settings->crm_test > 0): ?>
                                        <strong class="tit">CRM Test: </strong> <?php echo e($user->total_crm_test_score); ?>%  <br> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                        <?php if(isset($score_settings->email) && $score_settings->email > 0): ?>
                                            <strong>Email: </strong> <?php echo e($user->total_email_test_score); ?>% 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <?php if($page == 'results'): ?>
                            <td>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?><strong class="tit">Marked by: </strong> <?php echo e($user->marked_by ?: 'N/A'); ?><?php endif; ?>

                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?> <br> <strong class="tit">Graded by: </strong> <?php echo e($user->grader ?: 'N/A'); ?><br>

                                <small> Last updated on: <?php echo e(isset($user->updated_at) ?  \Carbon\Carbon::parse($user->updated_at)->format('jS F, Y, h:iA')  : ''); ?></small>
                                <?php endif; ?>
                                <br>
                                Certificate Access : <?php if(isset($user->cert)): ?>
                                    <?php if($user->cert->show_certificate == 1): ?>
                                    <strong style="color:green">Enabled</strong>
                                    <?php else: ?>
                                    <strong style="color:red">Disabled</strong>
                                    <?php endif; ?>
                                <?php else: ?>
                                Not Uploaded
                                <?php endif; ?> 
                            </td>
                            <?php endif; ?>
                            <td><strong class="tit" style="color:blue"><?php echo e($user->passmark); ?>%</strong> </td>
                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                            <td>
                                <strong class="tit" style="color:<?php echo e($total < $user->passmark ? 'red' : 'green'); ?>"><?php echo e($total); ?>%</strong> 
                            </td>
                            <?php endif; ?>

                            <?php if($page == 'mocks'): ?>
                            <td>
                                <?php if( $user->result_id ): ?>
                                    <div class="btn-group">
                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                            class="btn btn-info" href="<?php echo e(route('mocks.add', ['uid' => $user->user_id, 'result' => $user->result_id])); ?>"><i
                                                class="fa fa-eye"></i>
                                        </a>
                                            <form action="<?php echo e(route('mocks.destroy', $user->result_id)); ?>" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            <?php echo e(csrf_field()); ?>

                                            <?php echo e(method_field('DELETE')); ?>

                                            <input type="hidden" name="id" value="<?php echo e($user->result_id); ?>">
                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete Result"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <?php else: ?> 
                            <td>
                                <div class="button-container">
                                <?php if( isset($user->result_id)): ?> 
                                    <?php if($user->redotest == 0): ?>
                                        <?php if(!empty($user->certification_test_details)): ?>
                                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                                    <a class="btn btn-info btn-sm btn-sm w-100 mb-3" href="<?php echo e(route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id])); ?>"><i
                                                            class="fa fa-eye"> View/Update </i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions())): ?>
                                                <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="<?php echo e(route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ])); ?>" method="POST">
                                                    <?php echo e(csrf_field()); ?>

                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input type="hidden" name="uid" value="<?php echo e($user->user_id); ?>">
                                                    <input type="hidden" name="rid" value="<?php echo e($user->result_id); ?>">
                                                    <input type="hidden" name="pid" value="<?php echo e($user->program_id); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm"> <i class="fa fa-redo"> Enable Resit</i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                        <?php else: ?> 
                                            <button class="btn btn-danger btn-sm w-100 mb-3" style="display: block;" disabled>Resit In Progress!</button>
                                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                                <a class="btn btn-info btn-sm w-100 mb-3" href="<?php echo e(route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id])); ?>"><i
                                                        class="fa fa-eye"> View/Update </i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions())): ?>
                                            <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="<?php echo e(route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ])); ?>" method="POST">
                                                <?php echo e(csrf_field()); ?>

                                                <?php echo e(method_field('DELETE')); ?>

                                                <input type="hidden" name="uid" value="<?php echo e($user->user_id); ?>">
                                                <input type="hidden" name="rid" value="<?php echo e($user->result_id); ?>">
                                                <input type="hidden" name="pid" value="<?php echo e($user->program_id); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm w-100 mb-3"> <i
                                                        class="fa fa-redo"> Enable Resit</i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions())): ?>
                                            <?php if($user->redotest != 0): ?>
                                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions())): ?>
                                                    <a onclick="return confirm('This will stop this this user from access to take retest certification test/ Are you sure you want to do this?');" class="btn btn-warning btn-sm w-100 mb-3" href="<?php echo e(route('stopredotest',['user_id'=>$user->user_id, 'result_id'=>$user->result_id])); ?>"><i
                                                            class="fa fa-stop"></i> End resit
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-danger btn-sm w-100 mb-3" disabled>No Test Taken!</button>
                                <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($users->render()); ?>

        </div>
    </div>
</div>
<div class="modal fade" id="exportmodal" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export <?php echo e($page == 'results' ? 'Post' : 'Pre'); ?> test results</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id'=>$program->id])); ?>" method="POST" class="pb-2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="columns">User Columns to Export</label>
                                <select name="columns[]" id="columns" class="form-control select2 w-100" multiple="multiple" required>
                                    <option value="all" selected>All</option>
                                    <option value="name">Name</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone</option>
                                    <option value="gender">Gender</option>
                                    <option value="staffID">StaffID</option>
                                    <option value="metadata">Metadata</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary" style="width:100%">
                            Submit
                        </button>
                    </div>
                    <?php echo e(csrf_field()); ?>

                </form>
            </div>     
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Option --",
            allowClear: true
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/results/index.blade.php ENDPATH**/ ?>