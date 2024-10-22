<?php $__env->startSection('title', 'Settings' ); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h4 class="card-title">Update App Settings</h4>
                    </div>
                   <form action="<?php echo e(route('settings.update', 1)); ?>" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        <?php echo e(method_field('PATCH')); ?>

                         <?php echo e(csrf_field()); ?>

                        <div class="form-row">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Official Email *</label>
                                    <input type="text" name="OFFICIAL_EMAIL" value="<?php echo e(old('OFFICIAL_EMAIL') ?? $setting->OFFICIAL_EMAIL); ?>" class="form-control" required>
                                </div>

                                <!--Gives the first error for input name-->
                                <div><small style="color:red"><?php echo e($errors->first('OFFICIAL_EMAIL')); ?></small></div>
                                
                                 <div class="form-group">
                                    <label>Terms and conditions link</label>
                                    <input type="text" name="tac_link" value="<?php echo e(old('tac_link') ?? $setting->tac_link); ?>" class="form-control">
                                </div>
                                
                                <!--Gives the first error for input name-->
                                <div><small style="color:red"><?php echo e($errors->first('token')); ?></small></div>
                                <div class="form-group">
                                    <label>Currency Abbreviation *</label>
                                    <input type="text" name="CURR_ABBREVIATION" value="<?php echo e(old('CURR_ABBREVIATION') ??  $setting->CURR_ABBREVIATION); ?>"value="<?php echo e(old('CURR_ABBREVIATION') ?? $setting->CURR_ABBREVIATION); ?>"
                                        class="form-control" required>
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('CURR_ABBREVIATION')); ?></small></div>
                                <div class="form-group">
                                    <label>Default Currency code *</label>
                                    <input type="text" name="DEFAULT_CURRENCY" value="<?php echo old('DEFAULT_CURRENCY') ??  $setting->DEFAULT_CURRENCY; ?>"       class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="token" value="<?php echo e(old('phone') ?? $setting->phone); ?>" class="form-control">
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('DEFAULT_CURRENCY')); ?></small></div>             
                                <div class="form-group">
                                    <label>Address on receipt (You can use html tags)*</label>
                                    <textarea style="height: 132px;" name="ADDRESS_ON_RECEIPT" value="<?php echo e(old('ADDRESS_ON_RECEIPT') ??  $setting->ADDRESS_ON_RECEIPT); ?>"
                                        class="form-control" required><?php echo e(old('ADDRESS_ON_RECEIPT') ?? $setting->ADDRESS_ON_RECEIPT); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Allow Whatsapp Chat</label>
                                    <select name="allow_whatsapp_chat" class="form-control" id="allow_whatsapp_chat" required>
                                        <option value="" selected>Select</option>
                                        <option value="yes" <?php echo e($setting->allow_whatsapp_chat == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                        <option value="no" <?php echo e($setting->allow_whatsapp_chat == 'no' ? 'selected' : ''); ?>>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Allow Transfer button on checkout</label>
                                    <select name="allow_transfer_button" class="form-control" id="allow_transfer_button" required>
                                        <option value="" selected>Select</option>
                                        <option value="yes" <?php echo e($setting->allow_transfer_button == 'yes' ? 'selected' : ''); ?>>Yes</option>
                                        <option value="no" <?php echo e($setting->allow_transfer_button == 'no' ? 'selected' : ''); ?>>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Facebook Link</label>
                                    <input type="text" name="facebook_link" class="form-control" value="<?php echo e(old('facebook_link') ??  $setting->facebook_link); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Twitter Link</label>
                                    <input type="text" name="twitter_link" class="form-control" value="<?php echo e(old('twitter_link') ??  $setting->twitter_link); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Instagram Link</label>
                                    <input type="text" name="instagram_link" class="form-control" value="<?php echo e(old('instagram_link') ??  $setting->instagram_link); ?>">
                                </div>
                              
                                <div class="form-group">
                                    <label>Email Provider</label>
                                    <select name="email_provider" class="form-control" id="email_provider" required>
                                        <option value="" selected>Select...</option>
                                        <option value="default" <?php echo e($setting->email_provider == 'default' ? 'selected' : ''); ?>>Default</option>
                                        
                                        <option value="elastic" <?php echo e($setting->email_provider == 'elastic' ? 'selected' : ''); ?>>Elastic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Program Coordinator *</label>
                                    <input type="text" name="program_coordinator" value="<?php echo old('program_coordinator') ??  $setting->program_coordinator; ?>" 
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>WAACSP Token</label>
                                    <input type="text" name="token" value="<?php echo e(old('token') ?? $setting->token); ?>" class="form-control">
                                </div>
                                <div><small style="color:red"><?php echo e($errors->first('program_coordinator')); ?></small></div>             
                                <div class="form-group">
                                    <label for="primary_color">Primary colour</label>
                                    <input type="text" class="form-control" id="primary_color" minlength="7" maxlength="7" name="primary_color" placeholder="Eg #000000" title="accepts only color code" pattern="#[\daA-fF]{6}" value="<?php echo e(old('primary_color') ?? $setting->primary_color); ?>" required>
                                <div><small style="color:red"><?php echo e($errors->first('primary_color')); ?></small></div>
                                </div>
                                <div class="form-group">
                                    <label for="secondary_color">Secondary colour</label>
                                    <input type="text" class="form-control" id="secondary_color" minlength="7" maxlength="7" name="secondary_color" placeholder="Eg #000000" title="accepts only color code" pattern="#[\daA-fF]{6}" value="<?php echo e(old('secondary_color') ?? $setting->secondary_color); ?>" required>
                                    <div><small style="color:red"><?php echo e($errors->first('secondary_color')); ?></small></div>
                                </div>
                                <div class="form-group">
                                    <label>Frontend Template</label>
                                    <select name="frontend_template" class="form-control" id="frontend_template" required>
                                        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($template->id); ?>" <?php echo e($setting->frontend_template == $template->id ? 'selected' : ''); ?>><?php echo e($template->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>About Link</label>
                                    <input type="text" name="about_link" class="form-control" value="<?php echo e(old('about_link') ??  $setting->about_link); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Privacy Link</label>
                                    <input type="text" name="privacy_link" class="form-control" value="<?php echo e(old('privacy_link') ??  $setting->privacy_link); ?>">
                                </div>
                                <div class="col-md-12" style="display: flex;">
                                    <div class="form-group col-md-4" style="float: left;">
                                        <img src="<?php echo e(asset(\App\Models\Settings::value('favicon'))); ?>" alt="favicon" style="width: 64px;"/>
                                    </div>
                                    <div class="form-group col-md-8" style="float: right;padding-left: 0;padding-right: 0;">
                                        <label>Replace site favicon</label>
                                        <input type="file" name="favicon"  class="form-control" accept="image">
                                    </div>
                                </div>
                                <div class="form-group col-md-12" style="display: flex;">
                                    <div class="form-group col-md-4" style="float: left;">
                                        <img src="<?php echo e(asset(\App\Models\Settings::value('logo'))); ?>" alt="logo" style="width: 170px;" >
                                    </div>
                                    <div class="form-group col-md-8" style="float: right;">
                                        <label>Replace site logo (152px by 60px preferred)</label>
                                        <input type="file" name="logo"  class="form-control" accept="image">
                                    </div>
                                </div>
                                <div class="form-group col-md-12" style="display: flex;">
                                    <div class="form-group col-md-4" style="float: left;">
                                        <img src="<?php echo e(asset(\App\Models\Settings::value('banner'))); ?>" alt="banner" style="width: 95px">
                                    </div>
                                    <div class="form-group col-md-8" style="float: right;">
                                        <label>Replace site banner (1280px by 853px preferred)</label>
                                        <input type="file" name="banner"  class="form-control" accept="image">
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <div class="col-12">
                             <button class="btn btn-primary" style="width:100%">Update Settings</button>
                        </div>

                    </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/settings/edit.blade.php ENDPATH**/ ?>