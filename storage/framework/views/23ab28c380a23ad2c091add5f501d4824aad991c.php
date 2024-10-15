<?php $__env->startSection('title', 'Edit Query'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <form action="<?php echo e(route('complains.update',  ['complain'=>$complain->id] )); ?>" method="POST" class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>


                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <h4 style="text-align: center; color: blue">Customer Personal Details</h4>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                    <label for="name">Customer Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="<?php echo e($complain->name); ?>" disabled " autofocus>
                                    <?php if($errors->has('name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                               
                            </div>
                            <div class="col-md-4">
                                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                    value="<?php echo e($complain->email); ?>" disabled>
                                    <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group<?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                    value="<?php echo e($complain->phone); ?>" disabled autofocus>
                                    <?php if($errors->has('phone')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('phone')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <input id="gender" type="text" class="form-control" name="gender"
                                        value="<?php echo e(old('gender') ?? $complain->gender); ?>" disabled autofocus>
                                    <div><small style="color:red"><?php echo e($errors->first('gender')); ?></small></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group<?php echo e($errors->has('Address') ? ' has-error' : ''); ?>">
                                    <label for="address">Customer Address</label>
                                    <input id="address" type="text" class="form-control" name="address"
                                    value="<?php echo e($complain->address); ?>" disabled autofocus>
                                    <?php if($errors->has('address')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('address')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group<?php echo e($errors->has('state') ? ' has-error' : ''); ?>">
                                    <label for="state">State *</label>
                                    <input id="state" type="text" class="form-control" name="state"
                                    value="<?php echo e($complain->state); ?>" autofocus disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group<?php echo e($errors->has('LGA') ? ' has-error' : ''); ?>">
                                    <label for="LGA">LGA *</label>
                                    <input id="state" type="text" class="form-control" name="state"
                                    value="<?php echo e($complain->lga); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="mode">Mode *</label>
                                    <select name="mode" id="class" class="form-control">
                                        <option value="Phone Call"
                                            <?php echo e($complain->mode == 'Phone Call' ? 'selected' : ''); ?>>Phone Call</option>
                                        <option value="Email" <?php echo e($complain->mode == 'Email' ? 'selected' : ''); ?>>Email
                                        </option>
                                        <option value="Whatsapp" <?php echo e($complain->mode == 'Whatsapp' ? 'selected' : ''); ?>>
                                            Whatsapp</option>
                                        <option value=Twitter <?php echo e($complain->mode == 'Twitter' ? 'selected' : ''); ?>>
                                            Twitter</option>
                                        <option value="Facebook" <?php echo e($complain->mode == 'Facebook' ? 'selected' : ''); ?>>
                                            Facebook</option>
                                        <option value="Instagram" <?php echo e($complain->mode == 'Instagram' ? 'selected' : ''); ?>>
                                            Instagram</option>
                                        <option value="Other" <?php echo e($complain->mode == 'other' ? 'selected' : ''); ?>>Other
                                        </option>
                                    </select>
                                    <?php if($errors->has('mode')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('mode')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group<?php echo e($errors->has('other') ? ' has-error' : ''); ?>">
                                    <label for="other">Other Details</label>
                                    <input id="other" type="text" class="form-control" name="other"
                                        value="<?php echo e($complain->other); ?>" autofocus>
                                    <?php if($errors->has('other')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('class')); ?></small></div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <h4 style="text-align: center; color: blue">Query Details</h4>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="Complain" <?php echo e($complain->type == 'Complain' ? 'selected' : ''); ?>>Complain</option>
                                        <option value="Enquiry" <?php echo e($complain->type == 'Enquiry' ? 'selected' : ''); ?>>Enquiry</option>
                                        <option value="Request" <?php echo e($complain->type == 'Request' ? 'selected' : ''); ?>>Request</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="class">Issues</label>
                                    <select name="issues" id="issues" id="class" class="form-control">
                                        <option value="<?php echo e($complain->issues); ?>" selected="selected"><?php echo e($complain->issues); ?></option>
                                        
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('type')); ?></small></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Priority</label>
                                    <select name="priority" id="class" class="form-control" required>
                                        <option value="Low" <?php echo e($complain->priority == 'Low' ? 'selected' : ''); ?>>Low
                                        </option>
                                        <option value="Medium" <?php echo e($complain->priority == 'Medium' ? 'selected' : ''); ?>>
                                            Medium</option>
                                        <option value="High" <?php echo e($complain->priority == 'Hight' ? 'selected' : ''); ?>>High
                                        </option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('priority')); ?></small></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="<?php echo e($complain->status); ?>" selected="selected"><?php echo e($complain->status); ?></option>
                                        <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))): ?>
                                        <option value="Resolved" <?php echo e($complain->status == 'Resolved' ? 'selected' : ''); ?>>
                                            Resolved</option>
                                        <?php endif; ?>

                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('status')); ?></small></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group<?php echo e($errors->has('teamlead') ? ' has-error' : ''); ?>">
                                    <label for="teamlead">Team Lead</label>
                                    <input id="teamlead" type="text" class="form-control" name="teamlead"
                                    value="<?php echo e(old('teamlead') ?? $complain->teamlead); ?>" autofocus>
                                    <?php if($errors->has('teamlead')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('teamlead')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">            
                            <div class="col-md-6">
                                <div class="form-group<?php echo e($errors->has('complain') ? ' has-error' : ''); ?>">
                                    
                                    <label for="complain" style="color:red">Query Content</label>
                                    <textarea id="ckeditor" type="text" class="form-control" name="content" value="<?php echo e(old('complain') ?? $complain->content); ?>" rows="8" autofocus><?php echo $complain->content; ?></textarea>

                                    <?php if($errors->has('complain')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('complain')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group<?php echo e($errors->has('response') ? ' has-error' : ''); ?>">
                                    <label for="response" style="color:green">Your Response</label>
                                    <textarea  id="summary-ckeditor" type="text" class="form-control" name="response"
                                        value="<?php echo e(old('response') ?? $complain->response); ?>" rows="8" autofocus><?php echo $complain->response; ?></textarea>
                                    <?php if($errors->has('response')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('response')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div> 
                        </div>
                        <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) ): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group<?php echo e($errors->has('notes') ? ' has-error' : ''); ?>">
                                    <label for="notes" style="color:green">Supervisor's Note</label>
                                    <textarea class="form-control" name="notes"
                                        value="<?php echo e(old('notes') ?? $complain->notes); ?>" rows="8" autofocus ><?php echo e($complain->notes); ?></textarea>
                                    <?php if($errors->has('notes')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('notes')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty(array_intersect(studentRoles(), Auth::user()->role()))): ?>
                        <?php if($complain->notes): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group<?php echo e($errors->has('notes') ? ' has-error' : ''); ?>">
                                    <label for="notes" style="color:green">Supervisor's Note</label>
                                    <textarea class="form-control" rows="8" readonly ><?php echo e($complain->notes); ?></textarea>
                                    <?php if($errors->has('notes')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('notes')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Update
                            </button>
                        </div>
                        <?php echo e(csrf_field()); ?>

                </div>
            </div>
        </div>
    </div>
    <?php $__env->startSection('extra-scripts'); ?>
    <script>
        $('#type').on('click', function(){
        console.log($('#type').val());
            $('#issues').html('');
            if($('#type').val()=='Complain'){
                
                $('#issues').append('<option value="Drop Balance">Drop Balance</option>');
                $('#issues').append('<option value="Network Issues">Network Issues</option>');
                $('#issues').append('<option value="Recharge Issues">Recharge Issues</option>');
                $('#issues').append('<option value="Data Issues">Data Issues</option>');
                $('#issues').append('<option value="Late delivery">Late delivery</option>');
                $('#issues').append('<option value="Damages">Damages</option>');
                
                $("#status").html("");
               
                $('#status').append('<option value="Pending">Pending</option>');
                $('#status').append('<option value="In Progress">In Progress</option>');
                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))): ?>
                    $('#status').append('<option value="Resolved">Resolved</option>');
                <?php endif; ?>
            }if($('#type').val()=='Enquiry'){
                $('#issues').append('<option value="Product Enquires">Product Enquires</option>');
                $('#issues').append('<option value="Recharge Enquires">Recharge Enquires</option>');
                $('#issues').append('<option value="Opening hours">Opening hours</option>');
                $('#issues').append('<option value="Office location">Office location</option>');
                $('#issues').append('<option value="Cost of product">Cost of product</option>');
                
                $("#status").html("");
                $('#status').append('<option value="Resolved">Resolved</option>');
            }if($('#type').val()=='Request'){
                $('#issues').append('<option value="Product Request">Product Request</option>');
                $('#issues').append('<option value="Recharge Request">Recharge Request</option>');
                $('#issues').append('<option value="Home delivery">Home delivery</option>');
                $('#issues').append('<option value="Exchange ( Size or colour )">Exchange (Size or colour)</option>');
                
                $("#status").html("");
                $('#status').append('<option value="Pending" selected>Pending</option>');
                $('#status').append('<option value="In Progress">In Progress</option>');
                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))): ?>
                    $('#status').append('<option value="Resolved">Resolved</option>');
                <?php endif; ?>
            }
            
        });
    </script>
    
    
    <script>
        CKEDITOR.replace('summary-ckeditor');
    </script>
    <script>
        CKEDITOR.replace('ckeditor');
    </script>
    <?php $__env->stopSection(); ?>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make($extend, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/complains/edit.blade.php ENDPATH**/ ?>