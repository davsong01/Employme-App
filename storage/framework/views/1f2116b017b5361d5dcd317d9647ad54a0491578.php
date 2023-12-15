<?php $__env->startSection('title', 'All Results'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-header">
                    <div>
                        <h5 class="card-title"> All Results for: <?php echo e($program_name); ?> </h5><br>
                        <button class="btn btn-success" id="csv">Export Results</button>
                      
                    </div>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Details</th>
                            <th>Scores</th>
                            <th>Passmark</th>
                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?><th>Total</th><?php endif; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($user->passmark): ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td>
                                <strong class="tit">Name: </strong><?php echo e($user->name); ?> 
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) ): ?> <br>
                                <strong class="tit">Email: </strong><?php echo e($user->email); ?> <br>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?><br> <strong class="tit">Marked by: </strong> <?php echo e($user->marked_by); ?><?php endif; ?>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?> <br> <strong class="tit">Graded by: </strong> <?php echo e($user->grader); ?><?php endif; ?> <br>
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
                            
                            <td>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                    <?php if(isset($score_settings->certification) && $score_settings->certification > 0): ?>
                                    <strong>Certification: </strong> <?php echo e(isset($user->total_cert_score ) ? $user->total_cert_score : ''); ?>% 
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                    <?php if(isset($score_settings->class_test) && $score_settings->class_test > 0): ?>
                                        <br><strong class="tit">Class Tests:</strong> <?php echo e($user->final_ct_score); ?>% <br> <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?>
                                        <?php if(isset($score_settings->role_play) && $score_settings->role_play > 0): ?>
                                        <strong class="tit">Role Play: </strong> <?php echo e($user->total_role_play_score); ?>%  <br> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                        <?php if(isset($score_settings->email) && $score_settings->email > 0): ?>
                                            <strong>Email: </strong> <?php echo e($user->total_email_test_score); ?>% 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php
                                    $total = $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score;
                                ?>
                            </td>
                            <td><strong class="tit" style="color:blue"><?php echo e($user->passmark); ?>%</strong> </td>
                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                            <td>
                                 <strong class="tit" style="color:<?php echo e($total < $user->passmark ? 'red' : 'green'); ?>"><?php echo e($total); ?>%</strong> 
                            </td>
                            <?php endif; ?>
                            <td>
                                <?php if( isset($user->result_id)): ?> 
                                    <?php if($user->redotest == 0): ?>
                                        <?php if(!empty($user->certification_test_details)): ?>
                                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                                    <a class="btn btn-info btn-sm" href="<?php echo e(route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id])); ?>"><i
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
                                            <div class="btn-group">
                                            <button class="btn btn-button btn-danger btn-sm" style="display: block;" disabled>Participant did not retake a resit!</button>
                                            </div>
                                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                                                <a class="btn btn-info btn-sm" href="<?php echo e(route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id])); ?>"><i
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
                                                <button type="submit" class="btn btn-danger btn-sm"> <i
                                                        class="fa fa-redo"> Enable Resit</i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions())): ?>
                                            
                                            
                                            <?php endif; ?>
                                            <?php if($user->redotest != 0): ?>
                                            <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions())): ?>
                                                <a onclick="return confirm('This will stop this this user from access to take retest certification test/ Are you sure you want to do this?');" class="btn btn-warning btn-sm" href="<?php echo e(route('stopredotest',['user_id'=>$user->user_id, 'result_id'=>$user->result_id])); ?>"><i
                                                        class="fa fa-stop"></i> End resit
                                                </a>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                <div class="btn-group">
                                   <button class="btn btn-button btn-danger btn-sm" disabled>Participant has not taken any test!</button>
                                </div>
                                <?php endif; ?>
                                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                    class="btn btn-warning btn-sm" href="<?php echo e(route('impersonate', $user->user_id)); ?>"><i
                                        class="fa fa-unlock"> Peek</i>
                                </a>
                                <?php endif; ?> 
                            </td>
                           
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
               
                    
                    
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/jspdf.min.js')); ?> "></script>
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/jspdf.plugin.autotable.min.js'
                    )); ?>"></script>
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/tableHTMLExport.js')); ?>"></script>
                    
                    <script type="text/javascript">
                      
                     
                      $("#csv").on("click",function(){
                        $("#zero_config").tableHTMLExport({
                          type:'csv',
                          filename:'Results.csv'
                        });
                      });
                    
                    </script>
            </div>

        </div>
    </div>
</div>
<script>

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/results/index.blade.php ENDPATH**/ ?>