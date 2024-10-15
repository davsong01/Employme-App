<?php 
    use Illuminate\Support\Facades\Storage;
    $c_settings = $program->auto_certificate_settings;
?>
<?php $__env->startSection('css'); ?>
    <style>
        #previewModal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%); /* Center the modal */
            background-color: white; /* White background */
            padding: 20px; /* Some padding */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5); /* Add a shadow */
            border-radius: 10px; /* Optional: Rounded corners */
            max-width: 90%; /* Responsive width */
            max-height: 90%; /* Responsive height */
            overflow: auto; /* Add scroll if needed */
        }

        #certificatePreviewImage {
            width: 100%; /* Make image fit the modal */
            height: auto; /* Maintain aspect ratio */
        }

        .holder{
            padding: 0px 10px;
            margin-bottom:20px;
            border-radius: 10px;
            margin-top: 10px;
            background: antiquewhite; 
        }
        .field{
            border: 1px solid #ddd; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 5px; background-color: #f9f9f9;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('title', $program->p_name ); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4 class="card-title">Update <?php echo e($program->p_name); ?></h4>
                    </div> 
                    <form action="<?php echo e(route('programs.update', $program->id)); ?>" method="POST" enctype="multipart/form-data" class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>

                        <?php echo e(csrf_field()); ?>

                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Core Settings</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Training Title *</label>
                                        <input type="text" name="p_name" value="<?php echo e(old('p_name') ?? $program->p_name); ?>" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Training Hashtag *</label>
                                        <input type="text" name="p_abbr" value="<?php echo e(old('p_abbr') ?? $program->p_abbr); ?>" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date *</label>
                                        <input type="date" name="p_start" value="<?php echo e(old('p_start') ?? $program->p_start); ?>" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date *</label>
                                        <input type="date" name="p_end" value="<?php echo e(old('p_end') ??  $program->p_end); ?>" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Off Season Program?</label>
                                        <select name="off_season" class="form-control" id="off_season" required>
                                            <option value="1" <?php echo e($program->off_season == 1 ? 'selected' : ''); ?>>Yes</option>
                                            <option value="0" <?php echo e($program->off_season == 0 ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Closed Group Training?</label>
                                        <select name="is_closed" class="form-control" id="is_closed" required>
                                            <option value="yes" <?php echo e($program->is_closed == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->is_closed == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Show Catalogue Popup</label>
                                        <select name="show_catalogue_popup" class="form-control" id="show_catalogue_popup" required>
                                            <option value="yes" <?php echo e($program->show_catalogue_popup == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->show_catalogue_popup == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Replace Program Banner</label> <br>
                                        <img src="<?php echo e(url('/').'/'.$program->image); ?>" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                        <input type="file" name="image" value="<?php echo e(old('image') ??  $program->image); ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Flexible timing?</label>
                                        <select name="allow_preferred_timing" class="form-control" id="allow_preferred_timing" required>
                                            <option value="no" <?php echo e($program->allow_preferred_timing == 'no' ? 'selected' : ''); ?>>No</option>
                                            <option value="yes" <?php echo e($program->allow_preferred_timing == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <?php if($program->booking_form): ?>
                                        <label>Replace Booking form</label>
                                        <i data-toggle="tooltip" title="<?php echo e($program->booking_form); ?>" class="fa fa-paperclip" style="width: 70px;padding-bottom: 10px;"></i>
                                        <?php else: ?>
                                        <label>Upload Booking form</label>
                                        <?php endif; ?>
                                        <input type="file" name="booking_form" value="<?php echo e(old('booking_form')); ?>" placeholder="<?php echo e($program->booking_form); ?>" class="form-control">
                                    </div>
                                    <div><small style="color:red"><?php echo e($errors->first('booking_form')); ?></small></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Does Program have pre class tests?</label>
                                        <select name="hasmock" class="form-control" id="hasmock" required>
                                            <option value="1" <?php echo e($program->hasmock == 1 ? 'selected' : ''); ?>>Yes</option>
                                            <option value="0" <?php echo e($program->hasmock == 0 ? 'selected' : ''); ?>>No</option>
                                        </select>
                                        <small style="color:red"><?php echo e($errors->first('p_end')); ?></small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" id="status" required>
                                            <option value="1" <?php echo e($program->status == 1 ? 'selected' : ''); ?>>Published</option>
                                            <option value="0" <?php echo e($program->status == 0 ? 'selected' : ''); ?>>Draft</option>
                                        </select>
                                        <small style="color:red"><?php echo e($errors->first('status')); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Program Status <small>(Participants will not be able to access program)</small></label>
                                        <select name="program_lock" class="form-control" id="program_lock" required>
                                            <option value="1" <?php echo e($program->program_lock == 1 ? 'selected' : ''); ?>>Locked</option>
                                            <option value="0" <?php echo e($program->program_lock == 0 ? 'selected' : ''); ?>>Unlocked</option>
                                        </select>
                                        <small style="color:red"><?php echo e($errors->first('program_lock')); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="color:red">Login Without Password</label>
                                        <select name="login_without_password" class="form-control" id="login_without_password" required>
                                            <option value="1" <?php echo e($program->login_without_password == 1 ? 'selected' : ''); ?>>Yes</option>
                                            <option value="0" <?php echo e($program->login_without_password == 0 ? 'selected' : ''); ?>>No</option>
                                        </select>
                                        <small style="color:red"><?php echo e($errors->first('login_without_password')); ?></small>
                                    </div>
                                </div>
                                
                            </div>
                        </fieldset>
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Payment settings</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Training Fee *</label>
                                        <input type="number" name="p_amount" value="<?php echo e(old('p_amount') ??  $program->p_amount); ?>" min="0"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment?</label>
                                        <select name="haspartpayment" class="form-control" id="hasmock" required>
                                            <option value="1" <?php echo e($program->haspartpayment == 1 ? 'selected' : ''); ?>>Yes</option>
                                            <option value="0" <?php echo e($program->haspartpayment == 0 ? 'selected' : ''); ?>>No</option>
                                        </select>
                                        <small style="color:red"><?php echo e($errors->first('haspartpayment')); ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Early Bird Fee *</label>
                                        <input type="number" name="e_amount" value="<?php echo e(old('e_amount') ??  $program->e_amount); ?>" min="0"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Flexible payment?</label>
                                        <select name="allow_flexible_payment" class="form-control" id="allow_flexible_payment" required>
                                            <option value="no" <?php echo e($program->allow_flexible_payment == 'no' ? 'selected' : ''); ?>>No</option>
                                            <option value="yes" <?php echo e($program->allow_flexible_payment == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" style="margin-top: 20px;">  
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Show Mode (2 payment modes only)</label>
                                    <select name="show_modes" class="form-control" id="show_modes" required>
                                        <option value="no" <?php echo e($program->show_modes == 'no' ? 'selected' : ''); ?>>No</option>
                                        <option value="yes" <?php echo e($program->show_modes == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_mode" style="display:<?php echo e($program->show_modes == 'yes' ? 'block':'none'); ?>">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-mode"><i class="fa fa-plus"></i> Add Mode</button>
                                </div>
                            </div>
                            <section id="mode-holder" class="holder pt-2" style="display: none">
                                <div class="row" id="mode-0">
                                </div>
                                <?php $mode_counter = 1?>
                                <?php if($program->show_modes == 'yes' && isset($program->modes) && !empty($program->modes)): ?>
                                    <?php $__currentLoopData = json_decode($program->modes, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $counter = $mode_counter++ ?>
                                        <div class="row" id="oldmode-<?php echo e($counter); ?>">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="mode_name">Mode Name</label>
                                                
                                                    <input type="select" class="form-control" value="<?php echo e($key); ?>"
                                                        name="mode_name[]" required>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="mode_amount">Mode Amount</label>
                                                    <input type="text" class="form-control" id="unit" value="<?php echo e($value); ?>"name="mode_amount[]" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                                                    <button class="btn btn-danger removeold-mode" id="removeold-mode-<?php echo e($counter); ?>" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </section>
                        </fieldset>
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Payment Restriction Settings</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for materials?</label>
                                        <select name="allow_payment_restrictions_for_materials" class="form-control" id="allow_payment_restrictions_for_materials" required>
                                            <option value="yes" <?php echo e($program->allow_payment_restrictions_for_materials == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->allow_payment_restrictions_for_materials == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Pre class tests?</label>
                                        <select name="allow_payment_restrictions_for_pre_class_tests" class="form-control" id="allow_payment_restrictions_for_pre_class_tests" required>
                                            <option value="yes" <?php echo e($program->allow_payment_restrictions_for_pre_class_tests == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->allow_payment_restrictions_for_pre_class_tests == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Post class tests?</label>
                                        <select name="allow_payment_restrictions_for_post_class_tests" class="form-control" id="allow_payment_restrictions_for_post_class_tests" required>
                                            <option value="yes" <?php echo e($program->allow_payment_restrictions_for_post_class_tests == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->allow_payment_restrictions_for_post_class_tests == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Completed Tests?</label>
                                        <select name="allow_payment_restrictions_for_completed_tests" class="form-control" id="allow_payment_restrictions_for_completed_tests" required>
                                            <option value="yes" <?php echo e($program->allow_payment_restrictions_for_completed_tests == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->allow_payment_restrictions_for_completed_tests == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Results?</label>
                                        <select name="allow_payment_restrictions_for_results" class="form-control" id="allow_payment_restrictions_for_results" required>
                                            <option value="yes" <?php echo e($program->allow_payment_restrictions_for_results == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->allow_payment_restrictions_for_results == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Certificates?</label>
                                        <select name="allow_payment_restrictions_for_certificates" class="form-control" id="allow_payment_restrictions_for_certificates" required>
                                            <option value="yes" <?php echo e($program->allow_payment_restrictions_for_certificates == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->allow_payment_restrictions_for_certificates == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Certificate settings</legend>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Only Certified Should See Certificate</label>
                                        <select name="only_certified_should_see_certificate" class="form-control" id="only_certified_should_see_certificate" required>
                                            <option value="">Select...</option>
                                            <option value="yes" <?php echo e($program->only_certified_should_see_certificate == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                            <option value="no" <?php echo e($program->only_certified_should_see_certificate == 'no' ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </div>
                                </div>
        
                            </div>
                        </fieldset>
                        <fieldset style="" class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Sub Trainings</legend>
                            <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Sub Trainings?</label>
                                    <select name="show_sub" class="form-control" id="show_sub" required>
                                        <option value="yes" <?php echo e($program->show_sub == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                        <option value="no" <?php echo e(is_null($program->show_sub) || $program->show_sub == 'no' ? 'selected' : ''); ?>>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_sub" style="display:<?php echo e(isset($program->subPrograms) && $program->subPrograms->count() > 0 ? 'block':'none'); ?>">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-sub"><i class="fa fa-plus"></i> Add Sub Training</button>
                                </div>
                            </div>
                        </section>
                        <section id="sub-holder" class="holder pt-2" style="display: none">
                            <div class="row pt-2" id="sub-0">
                            </div>
                            <?php $sub_counter = 1?>
                            <?php if(isset($program->subPrograms) && !empty($program->subPrograms)): ?>
                            <?php $__currentLoopData = $program->subPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $counter = $sub_counter++ ?>
                                    <input type="hidden" name="sub_program_id[]" value="<?php echo e($sub->id); ?>">
                                    <div class="row p-2" id="oldsub-<?php echo e($counter); ?>">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="sub_name" style="padding-top:10px">Sub Program Name</label>
                                                <input type="text" class="form-control" value="<?php echo e($sub->p_name); ?>"
                                                    name="sub_name[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="sub_amount" style="padding-top:10px">Sub Program Amount</label>
                                                <input type="text" class="form-control" id="amount" value="<?php echo e($sub->p_amount); ?>" name="sub_amount[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mode_name" style="padding-top:10px">Status</label>
                                                <select name="sub_status[]" class="form-control" id="sub_status" required>
                                                    <option value="1" <?php echo e($sub->status == 1 ? 'selected' : ''); ?>>Published</option>
                                                    <option value="0" <?php echo e($sub->status == 0 ? 'selected' : ''); ?>>Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mark" style="color:antiquewhite; padding-top:10px">sdsdsddsdssd</label>
                                                <button class="btn btn-danger removeold-sub" data-program-id="<?php echo e($sub->id); ?>" id="removeold-sub-<?php echo e($counter); ?>" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                                <a target="_blank" class="btn btn-info" id="" type="button" style="min-width: unset;" href="<?php echo e(route('programs.edit', $sub->id)); ?>"> <i class="fa fa-edit"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </section>
                        </fieldset>
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Auto Certificate settings</legend>
                            <section>
                                <?php
                                    $c_settings['auto_certificate_status'] = $c_settings['auto_certificate_status'] ?? 'no';
                                ?>
                                <div class="row">  
                                    <div class="col-md-6" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <label>Enable Auto generate certificate</label>
                                            <select name="auto_certificate_status" class="form-control" id="auto_certificate_status" required>
                                                <option value="">Select...</option>
                                                <option value="yes" <?php echo e(isset($c_settings['auto_certificate_status']) && $c_settings['auto_certificate_status'] == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                                <option value="no" <?php echo e(isset($c_settings['auto_certificate_status']) && $c_settings['auto_certificate_status'] == 'no' ? 'selected' : ''); ?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6" style="margin-bottom:5px">
                                        <div class="form-group row">
                                            <label class="col-md-6 col-form-label">
                                                <?php if(isset($c_settings['auto_certificate_template'])): ?>
                                                    Replace Certificate Template
                                                <?php else: ?>
                                                    Upload Certificate Template
                                                <?php endif; ?>
                                            </label>
                                            
                                            <div class="col-md-12">
                                                <input type="file" name="auto_certificate_template" class="form-control" id="auto_certificate_template">
                                            </div>
                                        </div>

                                        
                                    </div>

                                    
                                </div>
                                <?php if(!empty($c_settings['settings'])): ?>
                                    <?php
                                        $certificate_counter = 1;
                                    ?>
                                    <section id="certificate-holder" class="pt-2">
                                        <div class="row" id="certificate-0">
                                        </div>
                                        <?php $__currentLoopData = $c_settings['settings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $counter = $certificate_counter++;
                                        ?>
                                        <div id="oldcertificate-<?php echo e($counter); ?>" class="row" style="border-top: black solid 1px;margin-bottom: 6px;padding-top: 15px;">  
                                            <div class="col-md-4" style="margin-bottom:5px">
                                                <div class="form-group">
                                                    <label>Text Type</label>
                                                    <select name="text_type[]" class="form-control" id="text_type" required>
                                                        <option value="">Select...</option>
                                                        <option value="certificate_number" <?php echo e((isset($setting['text_type']) && $setting['text_type'] == 'certificate_number') ? 'selected' : ''); ?>>Certificate Number</option>
                                                        <option value="name" <?php echo e((isset($setting['text_type']) && $setting['text_type'] == 'name') ? 'selected' : ''); ?>>Name</option>
                                                        <option value="email" <?php echo e((isset($setting['text_type']) && $setting['text_type'] == 'email') ? 'selected' : ''); ?>>Email</option>
                                                        <option value="staffID" <?php echo e((isset($setting['text_type']) && $setting['text_type'] == 'staffID') ? 'selected' : ''); ?>>Staff ID</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-bottom:5px">
                                                <div class="form-group">
                                                    <label>Text font size, e.g 150</label>
                                                    <input type="number" min="0" class="form-control" name="auto_certificate_name_font_size[]" value="<?php echo e($setting['auto_certificate_name_font_size'] ?? old('auto_certificate_name_font_size')); ?>" id="auto_certificate_name_font_size">
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-bottom:5px">
                                                <div class="form-group">
                                                    <label>Text font weight e.g 300</label>
                                                    <input type="number" min="0" class="form-control" name="auto_certificate_name_font_weight[]" value="<?php echo e($setting['auto_certificate_name_font_weight'] ?? old('auto_certificate_name_font_weight')); ?>" id="auto_certificate_name_font_weight">
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-bottom:5px">
                                                <div class="form-group">
                                                    <label>Text Top offset. e.g 300</label>
                                                    <input type="number" min="0" class="form-control" name="auto_certificate_top_offset[]" value="<?php echo e($setting['auto_certificate_top_offset'] ?? old('auto_certificate_top_offset')); ?>" id="auto_certificate_top_offset">
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-bottom:5px">
                                                <div class="form-group">
                                                    <label>Text Left offset. e.g 100</label>
                                                    <input type="number" min="0" class="form-control" name="auto_certificate_left_offset[]" value="<?php echo e($setting['auto_certificate_left_offset'] ?? old('auto_certificate_left_offset')); ?>" id="auto_certificate_left_offset">
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="margin-bottom:5px">
                                                <div class="form-group">
                                                    <label>Text color</label>
                                                    <input type="color" class="form-control" name="auto_certificate_color[]" value="<?php echo e($setting['auto_certificate_color'] ?? '#000000'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="mark" style="color:transparent">sdsdsddsdssd</label>
                                                    <button class="btn btn-danger remove-old-certificate" id="oldcertificate-<?php echo e($counter); ?>" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </section>
                                <?php endif; ?>
                                <!-- Container for dynamically added rows -->
                                <div id="certificateRows"></div>
                                <div class="row mt-5">
                                    <div class="col-md-4" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-success btn-sm" id="addRowButton"><i class="fa fa-plus"></i> Add New Row</button>
                                            <button type="button" class="btn-info btn-sm" id="previewButton"><i class="fa fa-eye"></i> Preview</button>
                                        </div>
                                        <div class="form-group">
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </fieldset>
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Others</legend>
                            <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px;">
                                    <label>Show Location</label>
                                    <select name="show_locations" class="form-control" id="show_locations" required>
                                        <option value="no" <?php echo e($program->show_locations == 'no' ? 'selected' : ''); ?>>No</option>
                                        <option value="yes" <?php echo e($program->show_locations == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_location" style="display:<?php echo e($program->show_locations == 'yes' ? 'block':'none'); ?>">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-course"><i class="fa fa-plus"></i> Add Location</button>
                                </div>
                            </div>
                        </section>
                        
                        <section id="course-holder" class="holder pt-2">
                            <div class="row" id="course-0">
                            </div>
                            <?php $location_counter = 1 ?>
                            <?php if($program->show_locations == 'yes' && isset($program->locations) && !empty($program->locations)): ?>
                                <?php $__currentLoopData = json_decode($program->locations, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $counter = $location_counter++ ?>
                                <div class="row" id="oldcourse-<?php echo e($counter); ?>">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="location_name">Location Name</label>
                                            <input type="text" class="form-control" value="<?php echo e($key); ?>"
                                                name="location_name[]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="location_address">Location Address</label>
                                            <input type="text" class="form-control" id="unit" value="<?php echo e($value); ?>"name="location_address[]" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                                            <button class="btn btn-danger remove-old-course" id="oldcourse-<?php echo e($counter); ?>" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </section>
                        </fieldset>
                        
                       
                        <div class="col-12">
                            <input type="submit" name="submit" value="Update" class="btn btn-primary" style="width:100%">
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <div id="previewModal" style="display:none;">
        <img id="certificatePreviewImage" src="" alt="Certificate Preview">
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#addRowButton').on('click', function(e) {
            var lastChild = $("#certificateRows").children().last();
            var lastId = $(lastChild).attr('id');

            var id = 1;
            if (lastId) {
                lastId = lastId.split('-'); 
                id = parseInt(lastId[1]) + 1; 
            }

            var newRow = `
                <div class="row added-row" style="border-top: black solid 1px;margin-bottom: 6px;padding-top: 15px;" id="certificate-` + id + `">
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text Type</label>
                            <select name="text_type[]" class="form-control" id="text_type" required>
                                <option value="">Select...</option>
                                <option value="certificate_number">Certificate Number</option>
                                <option value="name">Name</option>
                                <option value="email">Email</option>
                                <option value="staffID">Staff ID</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text font size, e.g 150</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_name_font_size[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text font weight e.g 300</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_name_font_weight[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text Top offset. e.g 300</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_top_offset[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text Left offset. e.g 100</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_left_offset[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text color</label>
                            <input type="color" class="form-control" name="auto_certificate_color[]">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-danger btn-sm removeRowButton">
                            <i class="fa fa-minus"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            
            $('#certificateRows').append(newRow);
        });

        // Remove row
        $(document).on('click', '.removeRowButton', function(e) {
            e.preventDefault();
            $(this).closest('.added-row').remove();
        });

        // Preview button handling
        $('#previewButton').on('click', function(e) {
            e.preventDefault();

            var formData = new FormData();

            $('select[name="text_type[]"]').each(function() {
                formData.append('text_type[]', $(this).val());
            });

            $('input[name="auto_certificate_name_font_size[]"]').each(function() {
                formData.append('auto_certificate_name_font_size[]', $(this).val());
            });

            $('input[name="auto_certificate_name_font_weight[]"]').each(function() {
                formData.append('auto_certificate_name_font_weight[]', $(this).val());
            });
            $('input[name="auto_certificate_top_offset[]"]').each(function() {
                formData.append('auto_certificate_top_offset[]', $(this).val());
            });
            $('input[name="auto_certificate_left_offset[]"]').each(function() {
                formData.append('auto_certificate_left_offset[]', $(this).val());
            });
            $('input[name="auto_certificate_color[]"]').each(function() {
                formData.append('auto_certificate_color[]', $(this).val());
            });

            var fileInput = $('#auto_certificate_template')[0].files[0];
            if (fileInput) {
                formData.append('auto_certificate_template', fileInput);
            }

            $.ajax({
                url: '/generate-certificate-preview/' + '<?php echo e($program->id); ?>',  // Laravel route for generating the certificate preview
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.preview_image_path) {
                        $('#certificatePreviewImage').attr('src', response.preview_image_path);
                        $('#previewModal').show(); // Display the modal
                    } else {
                        alert('Failed to generate preview. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('An error occurred. '+ error);
                }
            });
        });
    });

    $(document).on('click', function(event) {
        if ($(event.target).closest('#previewModal').length === 0 && $('#previewModal').is(':visible')) {
            $('#previewModal').fadeOut();
        }
    });

    $("#show_locations").on('change', function () {
        if($("#show_locations").val() == 'yes'){
            $("#add_location").show();
            $("#course-holder").show();
            
        }else{
            $("#add_location").hide();
            $("#course-holder").hide();

        }
    });
    
    $("#show_modes").on('change', function () {
        if($("#show_modes").val() == 'yes'){
            $("#add_mode").show();
            $("#mode-holder").show();
            
        }else{
            $("#add_mode").hide();
            $("#mode-holder").hide();
        }
    });

    $("#show_sub").on('change', function () {
    if($("#show_sub").val() == 'yes'){
        $("#add_sub").show();
        $("#sub-holder").show();
        
    }else{
        $("#add_sub").hide();
        $("#sub-holder").hide();
    }
    });

    $("#add-course").on('click', function () {
        //get last ID
        var lastChild = $("#course-holder").children().last();
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        var child = `<div class="row" id="course-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_name">Location Name</label>
                        <input type="text" class="form-control" value="<?php echo e(old('location_name')); ?>"
                            name="location_name[]" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_address">Location Address</label>
                        <input type="text" class="form-control" id="unit" value="<?php echo e(old('location_address')); ?>"name="location_address[]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-course" id="remove-course-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#course-holder").append(child);      
    });

    $("#course-holder").on('click','.remove-course', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#course-"+id).remove();
    });

    $("#course-holder").on('click','.remove-old-course', function(e) {
        var removeId = $(e.target).attr('id');
        $("#"+removeId).remove();
    });

    // $("#certificate-holder").on('click','.remove-old-certificate', function(e) {
    //     var removeId = $(e.target).attr('id');        
    //     $("#"+removeId).remove();
    //     // $("#oldcertificate-2").remove();
    // });
    $("#certificate-holder").on('click', '.remove-old-certificate', function() {
        // Get the ID of the clicked element
        var removeId = $(this).attr('id');  
    
        // Check if the ID is not empty
        if (removeId) {
            // Remove the element with the corresponding ID
            $("#" + removeId).remove();
        } else {
            console.warn("No ID found to remove.");
        }
    });


    $("#mode-holder").on('click','.removeold-mode', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#oldmode-"+id).remove();
    });

    $("#sub-holder").on('click','.removeold-sub', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var program_id =  $(e.target).attr('data-program-id');
        var id = removeId[2];
        // validate and remove program from database
        $.get("/admin-remove-sub-program/"+program_id,function(data, status){
            if(data.status == 'success'){
                $("#oldsub-"+id).remove();
            }else{
                alert(data.message);
            }
        });
    });

    $("#add-mode").on('click', function () {
        //get last ID
        var lastChild = $("#mode-holder").children().last();
        var countChildren = $("#mode-holder").children().length;
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        if(countChildren > 2){
            return alert('You can only add 2 payment modes!');
        }

        var child = `<div class="row" id="mode-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="mode_name">Mode Name</label>
                        <select name="mode_name[]" class="form-control" id="mode_name" required>
                            <option value="" selected>Select mode</option>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_address">Mode Amount</label>
                        <input type="text" class="form-control" id="unit" value="<?php echo e(old('mode_amount')); ?>"name="mode_amount[]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-mode" id="remove-mode-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#mode-holder").append(child);      
        });

    $("#mode-holder").on('click','.remove-mode', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#mode-"+id).remove();
    });

    $("#add-sub").on('click', function () {
        //get last ID
        var lastChild = $("#sub-holder").children().last();
        var countChildren = $("#sub-holder").children().length;
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        
        var child = `<div class="row" id="sub-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="sub_name" style="padding-top:10px">Sub Program Name</label>
                        <input type="text" class="form-control" id="sub_name" value="<?php echo e(old('sub_name')); ?>"name="sub_name[]" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sub_amount" style="padding-top:10px" >Sub Program Amount</label>
                        <input type="text" class="form-control" id="sub_amount" value="<?php echo e(old('sub_amount')); ?>"name="sub_amount[]" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mode_name" style="padding-top:10px">Status</label>
                        <select name="sub_status[]" class="form-control" id="sub_status" required>
                            <option value="1" selected>Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite;padding-top:10px">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-sub" id="remove-sub-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#sub-holder").append(child);      
        });

    $("#sub-holder").on('click','.remove-sub', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
       
        $("#sub-"+id).remove();
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/programs/edit.blade.php ENDPATH**/ ?>