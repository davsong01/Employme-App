    
    <?php $__env->startSection('title', 'Add New Query'); ?>
    <?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                        <form action="<?php echo e(route('complains.store')); ?>" method="POST" class="pb-2">
                            <?php echo e(csrf_field()); ?>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <h4 style="text-align: center; color: blue">Customer Personal Details</h4>
                                </div>
                                <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) ): ?>
                                <div class="col-md-12 mb-2">
                                    <div class="form-group">
                                        <label for="program_id">Select Training*</label>
                                        <select name="program_id" id="" class="form-control">
                                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($program->program_id); ?>" <?php echo e(old('program_id') == $program->program_id ? 'selected' : ''); ?> required><?php echo e($program->p_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                               
                                <?php else: ?>
                                <input type="hidden" name="program_id" value="<?php echo e($program->id); ?>">
                                <?php endif; ?>
                                <div class="col-md-4">
                                    <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                        <label for="name">Customer Name*</label>
                                        <input id="name" type="text" class="form-control" name="name"
                                            value="<?php echo e(old('name')); ?>" autofocus>
                                        <?php if($errors->has('name')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('name')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                   
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                        <label for="email">E-Mail Address*</label>
                                        <input id="email" type="email" class="form-control" name="email"
                                            value="<?php echo e(old('email')); ?>" required>
                                        <?php if($errors->has('email')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('email')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group<?php echo e($errors->has('phone') ? ' has-error' : ''); ?>">
                                        <label for="phone">Phone*</label>
                                        <input id="phone" type="text" class="form-control" name="phone"
                                            value="<?php echo e(old('phone')); ?>" autofocus required>
                                        <?php if($errors->has('phone')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('phone')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Gender*</label>
                                        <select name="gender" id="class" class="form-control" required>
                                            <option value="" selected>Choose</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <div><small style="color:red"><?php echo e($errors->first('gender')); ?></small></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group<?php echo e($errors->has('Address') ? ' has-error' : ''); ?>">
                                        <label for="address">Customer Address*</label>
                                        <input id="address" type="text" class="form-control" name="address"
                                            value="<?php echo e(old('address')); ?>" autofocus>
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
                                        <select name="state" id="state" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value='Abia' <?php echo e(old('state') == 'Abia' ? 'selected' :''); ?>>Abia</option>
                                            <option value='Adamawa'  <?php echo e(old('state') == 'Adamawa' ? 'selected' :''); ?>>Adamawa</option>
                                            <option value='AkwaIbom'  <?php echo e(old('state') == 'AkwaIbom' ? 'selected' :''); ?>>AkwaIbom</option>
                                            <option value='Anambra'  <?php echo e(old('state') == 'Anambra' ? 'selected' :''); ?>>Anambra</option>
                                            <option value='Bauchi' <?php echo e(old('state') == 'Bauchi' ? 'selected' :''); ?>>Bauchi</option>
                                            <option value='Bayelsa <?php echo e(old('state') == 'Bayelsa' ? 'selected' :''); ?>'>Bayelsa</option>
                                            <option value='Benue' <?php echo e(old('state') == 'Benue' ? 'selected' :''); ?>>Benue</option>
                                            <option value='Borno' <?php echo e(old('state') == 'Borno' ? 'selected' :''); ?>>Borno</option>
                                            <option value='Cross River' <?php echo e(old('Cross River') == 'Abia' ? 'selected' :''); ?>>Cross River</option>
                                            <option value='Delta' <?php echo e(old('state') == 'Delta' ? 'selected' :''); ?>>Delta</option>
                                            <option value='Ebonyi' <?php echo e(old('state') == 'Ebonyi' ? 'selected' :''); ?>>Ebonyi</option>
                                            <option value='Edo' <?php echo e(old('state') == 'Edo' ? 'selected' :''); ?>>Edo</option>
                                            <option value='Ekiti <?php echo e(old('state') == 'Ekiti' ? 'selected' :''); ?>'>Ekiti</option>
                                            <option value='Enugu' <?php echo e(old('state') == 'Enugu' ? 'selected' :''); ?>>Enugu</option>
                                            <option value='FCT' <?php echo e(old('state') == 'FCT' ? 'selected' :''); ?>>FCT</option>
                                            <option value='Gombe' <?php echo e(old('state') == 'Gombe' ? 'selected' :''); ?>>Gombe</option>
                                            <option value='Imo' <?php echo e(old('state') == 'Imo' ? 'selected' :''); ?>>Imo</option>
                                            <option value='Jigawa' <?php echo e(old('state') == 'Jigawa' ? 'selected' :''); ?>>Jigawa</option>
                                            <option value='Kaduna' <?php echo e(old('state') == 'Kaduna' ? 'selected' :''); ?>>Kaduna</option>
                                            <option value='Kano' <?php echo e(old('state') == 'Kano' ? 'selected' :''); ?>>Kano</option>
                                            <option value='Katsina' <?php echo e(old('state') == 'Katsina' ? 'selected' :''); ?>>Katsina</option>
                                            <option value='Kebbi' <?php echo e(old('state') == 'Kebbi' ? 'selected' :''); ?>>Kebbi</option>
                                            <option value='Kogi' <?php echo e(old('state') == 'Kogi' ? 'selected' :''); ?>>Kogi</option>
                                            <option value='Kwara' <?php echo e(old('state') == 'Kwara' ? 'selected' :''); ?>>Kwara</option>
                                            <option value='Lagos' <?php echo e(old('state') == 'Lagos' ? 'selected' :''); ?>>Lagos</option>
                                            <option value='Nasarawa' <?php echo e(old('state') == 'Nasarawa' ? 'selected' :''); ?>>Nasarawa</option>
                                            <option value='Niger' <?php echo e(old('state') == 'Niger' ? 'selected' :''); ?>>Niger</option>
                                            <option value='Ogun' <?php echo e(old('state') == 'Ogun' ? 'selected' :''); ?>>Ogun</option>
                                            <option value='Ondo' <?php echo e(old('state') == 'Ondo' ? 'selected' :''); ?>>Ondo</option>
                                            <option value='Osun' <?php echo e(old('state') == 'Osun' ? 'selected' :''); ?>>Osun</option>
                                            <option value='Oyo' <?php echo e(old('state') == 'Oyo' ? 'selected' :''); ?>>Oyo</option>
                                            <option value='Plateau <?php echo e(old('state') == 'Plateau' ? 'selected' :''); ?>'>Plateau</option>
                                            <option value='Rivers' <?php echo e(old('state') == 'Rivers' ? 'selected' :''); ?>>Rivers</option>
                                            <option value='Sokoto' <?php echo e(old('state') == 'Sokoto' ? 'selected' :''); ?>>Sokoto</option>
                                            <option value='Taraba' <?php echo e(old('state') == 'Taraba' ? 'selected' :''); ?>>Taraba</option>
                                            <option value='Yobe' <?php echo e(old('state') == 'Yobe' ? 'selected' :''); ?>>Yobe</option>
                                            <option value='Zamfara' <?php echo e(old('state') == 'Zamfara' ? 'selected' :''); ?>>Zamafara</option>
                                        </select>

                                        <?php if($errors->has('state')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('state')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group<?php echo e($errors->has('LGA') ? ' has-error' : ''); ?>">
                                        <label for="LGA">LGA *</label>
                                        <select name="lga" id="lga" class="form-control" required>
                                        </select>

                                        <?php if($errors->has('LGA')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('LGA')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="mode">Mode *</label>
                                        <select name="mode" id="mode" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value='Phone Call'>Phone Call</option>
                                            <option value='Email'>Email</option>
                                            <option value='Whatsapp'>Whatsapp</option>
                                            <option value='Twitter'>Twitter</option>
                                            <option value='Facebook'>Facebook</option>
                                            <option value='Instagram'>Instagram</option>
                                            <option value='Other'>Other</option>
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
                                            value="<?php echo e(old('other')); ?>" autofocus>
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
                                        <label for="class">Type*</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Complain">Complain</option>
                                            <option value="Enquiry">Enquiry</option>
                                            <option value="Request">Request</option>
                                        </select>
                                        <div><small style="color:red"><?php echo e($errors->first('type')); ?></small></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="class">Issues*</label>
                                        <select name="issues" id="issues" id="class" class="form-control">
                                            <option value="" selected="selected">- Select -</option>
                                                                                
                                        </select>
                                        <div><small style="color:red"><?php echo e($errors->first('type')); ?></small></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Priority*</label>
                                        <select name="priority" id="class" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Low">High</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">Low</option>
                                        </select>
                                        <div><small style="color:red"><?php echo e($errors->first('priority')); ?></small></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Status*</label>
                                        <select name="status" id="status" class="form-control" required>
                                           
                                        </select>
                                        <div><small style="color:red"><?php echo e($errors->first('status')); ?></small></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group<?php echo e($errors->has('teamlead') ? ' has-error' : ''); ?>">
                                        <label for="teamlead">Team Lead</label>
                                        <input id="teamlead" type="text" class="form-control" name="teamlead"
                                            value="<?php echo e(old('teamlead')); ?>" autofocus>
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
                                        <label for="complain" style="color:red">Query Content*</label>
                                        <textarea id="ckeditor" type="text" class="form-control" name="complain"
                                            value="<?php echo e(old('complain')); ?>" rows="8" autofocus required></textarea>
                                        <?php if($errors->has('complain')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('complain')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group<?php echo e($errors->has('response') ? ' has-error' : ''); ?>">
                                        <label for="response" style="color:green">Your Response*</label>
                                        <textarea required id="summary-ckeditor" type="text" class="form-control" name="response"
                                            value="<?php echo e(old('response')); ?>" rows="8" autofocus></textarea>
                                        <?php if($errors->has('response')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('response')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div> 
                            </div>
                           
                            
                            <div class="row">
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    Submit
                                </button>
                            </div>
                        </form>
                       
                    </div>
                </div>
            </div>
        </div>
        <?php $__env->startSection('extra-scripts'); ?>
        <script>
            $('#type').on('change', function(){
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
                    $('#issues').append('<option value="Exchange (Size or colour)">Exchange (Size or colour)</option>');
                    
                    $("#status").html("");
                    $('#status').append('<option value="Pending" selected>Pending</option>');
                    $('#status').append('<option value="In Progress">In Progress</option>');
                    <?php if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))): ?>
                        $('#status').append('<option value="Resolved">Resolved</option>');
                    <?php endif; ?>
                }
                
            });
        </script>
        <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
        <script>
            CKEDITOR.replace('summary-ckeditor');
        </script>
        <script>
            CKEDITOR.replace('ckeditor');
        </script>
    </div>

    <?php $__env->stopSection(); ?>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make($extend, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/complains/create.blade.php ENDPATH**/ ?>