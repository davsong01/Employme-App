<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Email Participants'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div>
                <div>
                    <h3>Email Participants</h3>
                </div>
            </div>

            <form action="<?php echo e(route('user.sendmail')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo e(csrf_field()); ?>

                <div class="row">
                    <div class="col-md-12">
                        <p>Please select an email type and type in the content of the mail you want to send, the content and then the send button</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group type">
                            <label>Select Type</label>
                            <select name="type" id="type" class="form-control custom-select-value" required>
                                <option value="">Choose option</option>
                                <option value="bulk">Program Participants</option>
                                <option value="selected">Selected Participants</option>
                                <option value="bulkrecipients">Bulk Email</option>
                            </select>
                            <?php if($errors->has('type')): ?>
                                <span class="help-block">
                                    <strong><?php echo e($errors->first('type')); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group bulkemail">
                            <label>Choose Program</label>
                            <select name="program" id="program" class="form-control custom-select-value">
                                <option value="">Choose option</option>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($program->users_count > 0): ?>
                                    <option value="<?php echo e($program->id); ?>"><?php echo e($program->p_name); ?> (<?php echo e($program->users_count); ?>)</option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('program')): ?>
                                <span class="help-block">
                                    <strong><?php echo e($errors->first('program')); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group selectedemail">
                            <label>Select recipients</label>
                            <select name="selectedemail[]" id="selectedemail" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;">
                                <option value="">Choose option</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->email); ?>"><?php echo e($user->email); ?> ( <?php echo e($user->name); ?> )</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php if($errors->has('selectedemail')): ?>
                                <span class="help-block">
                                    <strong><?php echo e($errors->first('selectedemail')); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group bulkrecipients">
                            <textarea style="width:100%" name="bulkrecipients" id="bulkrecipients" rows="15" placeholder="Paste the emails here, each email on a new line"></textarea>
                            <?php if($errors->has('bulkrecipients')): ?>
                                <span class="help-block">
                                    <strong><?php echo e($errors->first('bulkrecipients')); ?></strong>
                                </span>
                            <?php endif; ?>
                        </div>

                        
                        <div class="form-group">

                            <label for="subject">Subject</label>

                            <input id="subject" type="text" class="form-control" name="subject"
                                value="<?php echo e(old('subject')); ?>" required autofocus>

                            <?php if($errors->has('subject')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('subject')); ?></strong>
                            </span>

                            <?php endif; ?>

                        </div>
                        <div class="form-group">
                            <label>Type Email Content (<strong style="color:red">Dear {Participant's name} is automatically added at the top of this mail</strong>)</label>

                            <textarea class="form-control" id="summary-ckeditor" name="content"></textarea>
                        </div>
                        <?php if($errors->has('content')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('content')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">

                    <button type="submit" class="btn btn-primary" style="width:100%">

                        Send Email

                    </button>

                </div>
            </form>
            <div class="row">
                <div class="card-title" style="margin-top:30px">
                    <h3>Emails History</h3>
                </div>
                <div class="">
                    <table id="zero_config" class="">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Training</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>No of Recipients</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $updateemails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($i++); ?></td>
                                    <td><?php echo e($email->created_at->format('d/m/Y')); ?></td>
                                    <td><?php echo e($email->program); ?></td>
                                    <td><?php echo e($email->sender); ?></td>
                                    <td><?php echo e($email->subject); ?></td>
                                    <td><?php echo e($email->noofemails); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a data-toggle="tooltip" data-placement="top" title="Edit email" class="btn btn-info"
                                                href="<?php echo e(route('updateemails.show', $email->id)); ?>"><i
                                                    class="fa fa-eye"></i>
                                            </a>
            
            
                                            
                                            
            
                                            
                                            
                                        </div>
            
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
   
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('extra-scripts'); ?>

<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('summary-ckeditor');
    
    var program = $('#program');
    var selectedemail = $('#selectedemail');
    var bulkrecipients = $('#bulkrecipients');

    $('#type').on('change', function(){
        console.log($('#type').val());
            
            if($('#type').val()=='bulk' ){
                $('.bulkemail').css('display','block');
                program.attr('required', true);
                $('.selectedemail').css('display','none');
                $('.bulkrecipients').css('display','none');
                selectedemail.attr('required', false);
                bulkrecipients.attr('required', false);
                
                
            }else if($('#type').val()=='selected'){
                $('.selectedemail').css('display','block');
                selectedemail.attr('required', true);
                $('.bulkemail').css('display','none');
                $('.bulkrecipients').css('display','none');
                program.attr('required', false);
                bulkrecipients.attr('required', false);

            }else if($('#type').val()=='bulkrecipients'){
                $('.bulkrecipients').css('display','block');
                bulkrecipients.attr('required', true);
                $('.bulkemail').css('display','none');
                $('.selectedemail').css('display','none');
                program.attr('required', false);
                selectedemail.attr('required', false);
               
            }
           
    });

</script> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/users/email.blade.php ENDPATH**/ ?>