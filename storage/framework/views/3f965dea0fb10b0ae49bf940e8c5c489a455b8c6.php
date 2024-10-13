<?php
    $currentStatus = request('status');
?>

<?php $__env->startSection('title', 'Test Results'); ?>
<?php $__env->startSection('css'); ?>
<?php echo $__env->make('dashboard.company.partials.company_extra_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<link rel="stylesheet" href="<?php echo e(asset('modal.css')); ?>" />
<style>
    .select2-container--default .select2-selection--multiple {
        width: 100% !important;
    }

    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: black;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: black;
    }

    .select2-container--default .select2-results__option {
        color: black;
    }

    .badge {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px !important;
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
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-header">
                    <h5 class="card-title"> 
                        <?php echo $title; ?>

                    </h5>
                    <br>
                    <div class="row">
                        <div class="card-body">
                            <div class="row mb-3">
                                <!-- Badge Display -->
                                <div class="col-md-3 col-lg-2 mb-2" style="float-left">
                                    <div class="badge bg-secondary w-100 text-center">
                                        <span class="transaction-count"><?php echo e($records); ?></span>
                                    </div>
                                </div>
                                <!-- All Tests Button -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <a href="<?php echo e(route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id])); ?>">
                                        <button class="btn btn-dark w-100 <?php echo e(is_null($currentStatus) ? 'active' : ''); ?>">All</button>
                                    </a>
                                </div>

                                <!-- Has Tests Button -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <a href="<?php echo e(route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id])); ?>?<?php echo e(http_build_query(array_merge(request()->query(), ['status' => 'yes']))); ?>">
                                        <button class="btn btn-success w-100 <?php echo e($currentStatus === 'yes' ? 'active' : ''); ?>">Has Tests</button>
                                    </a>
                                </div>

                                <!-- Pending Tests Button -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <a href="<?php echo e(route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id])); ?>?<?php echo e(http_build_query(array_merge(request()->query(), ['status' => 'no']))); ?>">
                                        <button class="btn btn-danger w-100 <?php echo e($currentStatus === 'no' ? 'active' : ''); ?>">Pending Tests</button>
                                    </a>
                                </div>

                                <!-- Export Button -->
                                <div class="col-md-3 col-lg-3 mb-2">
                                    <a class="btn btn-info w-100" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exportmodal">
                                        <i class="fa fa-download"></i> Export <?php echo e($page == 'results' ? 'Post' : 'Pre'); ?> Test Results
                                    </a>
                                </div>

                                
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mt-4 col-md-12">
                            <form class="row search-form w-100" method="GET" action="<?php echo e(route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id])); ?>">
                                <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">

                                <!-- Name Field -->
                                <div class="col-md-9 mb-3">
                                    <div class="form-group">
                                        <label for="name">Enter Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo e(request('name')); ?>">
                                    </div>
                                </div>

                                <!-- Search Button -->
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="" style="color:transparent;display:block">Searching</label>
                                        <button type="submit" class="btn btn-primary w-100">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 table-responsive">
                <table class="table table-striped table-bordered responsive"> 
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Test Scores</th>
                            <th>Passmark</th>
                            <th>Total</th>
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

                            </td>
                            
                            <td>
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
                                <?php if(isset($score_settings->certification) && $score_settings->certification > 0): ?>
                                <strong>Certification: </strong> <?php echo e(isset($user->total_cert_score ) ? $user->total_cert_score : ''); ?>% <br>
                                <?php endif; ?>
                                <?php if(isset($score_settings->role_play) && $score_settings->role_play > 0): ?>
                                <strong class="tit">Role Play: </strong> <?php echo e($user->total_role_play_score); ?>%  <br> 
                                <?php endif; ?>
                                <?php if(isset($score_settings->crm_test) && $score_settings->crm_test > 0): ?>
                                <strong class="tit">CRM Test: </strong> <?php echo e($user->total_crm_test_score); ?>%  <br> 
                                <?php endif; ?>
                                <?php if(isset($score_settings->email) && $score_settings->email > 0): ?>
                                    <strong>Email: </strong> <?php echo e($user->total_email_test_score); ?>% 
                                <?php endif; ?>
                            </td>
                            <td><strong class="tit" style="color:blue"><?php echo e($user->passmark); ?>%</strong> </td>
                            <td>
                                <strong class="tit" style="color:<?php echo e($total < $user->passmark ? 'red' : 'green'); ?>"><?php echo e($total); ?>%</strong> 
                            </td>
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id'=>$program->id])); ?>" method="POST" class="pb-2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="columns">User Data to Export</label>
                                <select name="columns[]" id="columns" class="form-control select2 w-100" multiple="multiple" required>
                                    <option value="name">Name</option>
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
    <?php $__env->stopSection(); ?>
    
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Option --",
            allowClear: true
        });
    });
</script>



<?php echo $__env->make('dashboard.company.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/company/pretests/index.blade.php ENDPATH**/ ?>