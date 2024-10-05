<?php $__env->startSection('title', 'Download Certificate'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="">
                <table id="" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Training</th>
                            <th>Certificate Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <div class="text-center">
                            <h5 class="card-title">Please Download your certificate below</h5>
                        </div>
                        <tr>
                            <td><?php echo e($certificate->user->name); ?> <br>
                            </td>
                            <td><?php echo e($certificate->program->p_name); ?></td>
                            <td style="color:<?php echo e($certificate->show_certificate() == 'Disabled' ? 'red' : 'green'); ?>">
                                Certificate Status: <strong><?php echo e($certificate->show_certificate()); ?></strong>
                                <?php if($certificate->certificate_number): ?>
                                <br>Certificate No: <strong><?php echo e($certificate->certificate_number); ?></strong> <br>
                                <div class="form-group mb-3">
                                    <button id="copy-btn<?php echo e($certificate->id); ?>" class="btn btn-primary">
                                        <i class="fa fa-copy"></i> Copy Certificate Verification Link
                                    </button>
                                    <small id="copy-status<?php echo e($certificate->id); ?>" style="color: green; display: none;"></small>
                                </div>
                                <input type="text" id="verification-link<?php echo e($certificate->id); ?>" value="<?php echo e(url('/api/verify-certificate').'?certificate_number='.$certificate->certificate_number); ?>" hidden>
                                <script>
                                    $('#copy-btn<?php echo e($certificate->id); ?>').click(function() {
                                        var verificationLink = $('#verification-link<?php echo e($certificate->id); ?>').val();
                                        
                                        var tempInput = $('<input>');
                                        $('body').append(tempInput);
                                        tempInput.val(verificationLink).select();
                                        document.execCommand("copy");
                                        tempInput.remove(); 

                                        $('#copy-status<?php echo e($certificate->id); ?>').text("<?php echo e($certificate->certificate_number); ?> Copied");
                                        $('#copy-status<?php echo e($certificate->id); ?>').fadeIn().delay(2000).fadeOut();
                                    });
                                </script>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-info" href="/certificate/<?php echo e($certificate->file); ?>"><i
                                    class="fa fa-download"> Download Certificate</i></a>
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    // $('#zero_config').DataTable();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/certificates/index.blade.php ENDPATH**/ ?>