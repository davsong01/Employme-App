<?php $__env->startSection('title', 'Add Facilitator'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <div class="card-title">
                        <h4 class="card-title">Add new Facilitator/Grader</h4>
                    </div>
                    <form action="<?php echo e(route('teachers.store')); ?>" method="POST" enctype="multipart/form-data" class="pb-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class">Role*</label>
                                    <select name="role[]" id="role" class="select2 role form-control" multiple="multiple" >
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Facilitator" <?php echo e(old('role') == 'Facilitator' ? 'selected' : ''); ?>>Facilitator</option>
                                        <option value="Grader" <?php echo e(old('role') == 'Grader' ? 'selected' : ''); ?>>Grader</option>
                                        <option value="Admin" <?php echo e(old('role') == 'Admin' ? 'selected' : ''); ?>>Admin</option>
                                    </select>
                                    <div><small style="color:red"><?php echo e($errors->first('role')); ?></small></div>
                                </div>
                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                    <label for="license">WAACSP license</label> <br>
                                    <input id="license" type="text" style="width:79%;float:left" class="form-control" name="license" value="<?php echo e(old('license')); ?>" autofocus >
                                    <span id="verify-button"><span class="btn btn-info" style="float:left"  id="verify" onclick="myFunction()">Verify license</span></span>
                                    <span class="help-block">
                                        <strong id="result"></strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label for="waacsp_url" style="margin-top: 15px;">WAACSP url</label> <br>
                                    <input id="waacsp_url" type="text"  class="form-control" name="waacsp_url" value="<?php echo e(old('waacsp_url')); ?>" autofocus >
                                    
                                </div>
                              
                                <div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode " class="form-control" required>
                                        <?php $__currentLoopData = $payment_modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($mode->id); ?>" selected alt=""><?php echo e(ucFirst($mode->name)); ?></option> 
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?>">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" autofocus >
                                    <?php if($errors->has('name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group<?php echo e($errors->has('status') ? ' has-error' : ''); ?>">
                                    <label for="status">Status</label>
                                    <select name="status" id="type" class="form-control" required>
                                        <option value="active" selected>Active</option> 
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Profile Picture</label>
                                    <img style="display:none; width: 80px;border-radius: 50%;height: 80px;padding: 10px;" id="profile_picture" src="" alt="">
                                    <input type="file" name="file" value="<?php echo e(old('avatar')); ?>" class="form-control">
                                </div>
                                <input type="hidden" id="picture" name="picture" value="">
                                <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>">
                                    <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <div style="margin-top: 5px;" class="form-group">
                                    <label for="email">Phone</label>
                                    <input id="phone" type="phone" class="form-control" name="phone" value="<?php echo e(old('phone')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="class">Available off season?</label>
                                    <select name="off_season_availability" id="class" class="form-control">
                                        <option value="" <?php echo e(old('off_season_availability') == '' ? 'selected' : ''); ?>>No</option>
                                        <option value="1" <?php echo e(old('off_season_availability') == '1' ? 'selected' : ''); ?>>Yes</option>
                                    </select>
                                </div>
                                
                                <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                                    <label for="password">Password: </label><span class="help-block">
                                        <strong>Default: 12345</strong>
                                    </span>
                                    <input id="password" type="text" class="form-control" name="password" value="<?php echo e(old('password') ?? ''); ?>"
                                        autofocus>
                                    <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;">
                                        <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                             <option value="<?php echo e($program->id); ?>"><?php echo e($program->p_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <div>
                                        <?php if($errors->has('training')): ?>
                                        <span class="help-block">
                                            <strong><?php echo e($errors->first('training')); ?></strong>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                <div>
                            </div>
                        </div>
                        
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12">
                               <span><h6>Admin Menu Permissions</h6></span>
                               
                            </div>
                            <?php $__currentLoopData = app('app\Http\Controllers\Controller')->adminMenus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="menu_permissions[]" value="<?php echo e($menu['id']); ?>" id="<?php echo e($menu['id']); ?>">
                                    <label class="form-check-label" for="<?php echo e($menu['id']); ?>">
                                        <?php echo e($menu['name']); ?>

                                    </label>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group<?php echo e($errors->has('profile') ? ' has-error' : ''); ?>">
                                    
                                    <label for="profile" style="color:red">Profile overview</label>
                                    <textarea id="ckeditor" type="text" class="form-control" name="profile" value="<?php echo e(old('profile')); ?>" rows="8" autofocus><?php echo e(old('profile')); ?></textarea>

                                    <?php if($errors->has('profile')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('profile')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                       <?php echo e(csrf_field()); ?>

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
</div>
<?php $__env->startSection('extra-scripts'); ?>
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('ckeditor');
    var editor = CKEDITOR.instances['ckeditor'];
    src = "<?php echo e(env('ENT') == 'demo' ? 'http://localhost:8888/waacsp/public/api/verifyinstructor' : 'https://thewaacsp.com/api/verifyinstructor'); ?>";
    token = "<?php echo e(\App\Settings::value('token')); ?>";
    
    function myFunction() {
        $.ajax({
            url: src,
            headers: {
                license:  $('#license').val(),
                token: token
            },

            beforeSend: function(xhr){
                $('#result').prepend('LOADING...');
            },
            success: function(res){
                // console.log(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#result').css('display','none');
                $('#name').val(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#license').val(res.data.license);
                $('#email').val(res.data.email);
                $('#phone').val(res.data.phone);
                $('#waacsp_url').val(res.data.url);
                editor.insertText(res.data.short_profile); 
                $("#profile_picture").css("display",'block'); 
                $("#profile_picture").attr("src",res.data.avatar); 
                $("#picture").val(res.data.avatar); 
                $("#verify-button").css("bakground:green");
                $("#verify-button").html("<span class='btn btn-success' style='float:left' id='verify'>Verified!</span>"); 
                
            },

            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                // console.log(res.data.first_name + ' ' + res.data.middle_name + ' '+ res.data.last_name);
                $('#result').html(err.message);
                $('#result').css('color','red');

            }
        });
    }
   
</script>

<script>                                
    $('#role').on('change', function(){
        console.log($('#role').val());
            
            if($('#role').val()=='Facilitator' || $('#role').val()=='Grader' ){
                $('.selecttraining').css('display','block');
                
            }else if($('#role').val()=='Admin'){
                $('.selecttraining').css('display','none');
                
            }
    });


    
</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/teachers/create.blade.php ENDPATH**/ ?>