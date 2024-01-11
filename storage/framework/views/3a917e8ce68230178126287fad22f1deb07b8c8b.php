<?php $__env->startSection('title', 'Proofs of Payment'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Customer details</th>
                            <th>Amount Paid</th>
                            <th>Training details</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Actions</th>      
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $transactions->sortBy('date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td><?php echo e($transaction->created_at); ?></td>
                            <td>
                                <?php echo e($transaction->name); ?> <br>
                                <?php echo e($transaction->phone); ?> <br>
                                <?php echo e($transaction->email); ?> <br>
                            </td>
                            <td>
                                <?php echo e(\App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($transaction->amount)); ?>

                            </td>
                            <td><?php echo e($transaction->program->p_name); ?> <br>(<?php echo e($transaction->program->e_amount <= 0 ? 'Amount: '.\App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$transaction->program->p_amount : 'E/Amount '. \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$transaction->program->e_amount); ?>) <br>
                            <?php if(!is_null($transaction->coupon_code)): ?>
                                <span style="color:blue">
                                    <strong>Coupon (<?php echo e($transaction->coupon); ?>) Applied | <?php echo e(\App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($transaction->coupon->coupon_amount)); ?>  </strong>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($transaction->bank); ?></td>
                            <td><?php echo e($transaction->location); ?></td>
                           
                             <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm('Are you really sure?');"
                                        class="btn btn-danger" href="<?php echo e(route('temp.destroy', $transaction->id)); ?>"><i
                                            class="fa fa-trash"></i>
                                    </a>

                                    
                                </div>
                            </td>
                        </tr>
                        <div id="myModal" class="modal">
                            <span class="close2">&times;</span>
                            <img class="modal-content" id="img01">
                            <div id="caption"></div>
                        </div>
                        <script>
                        // Get the modal
                        var modal = document.getElementById("myModal");

                        // Get the image and insert it inside the modal - use its "alt" text as a caption
                        var img = document.getElementById("myImg<?php echo e($transaction->file); ?>");
                        var modalImg = document.getElementById("img01");
                        var captionText = document.getElementById("caption");
                        img.onclick = function(){
                        modal.style.display = "block";
                        modalImg.src = this.src;
                        captionText.innerHTML = this.alt;
                        }

                        // Get the <span> element that closes the modal
                        var span = document.getElementsByClassName("close2")[0];

                        // When the user clicks on <span> (x), close the modal
                        span.onclick = function() { 
                        modal.style.display = "none";
                        }
                        </script>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payments/pop.blade.php ENDPATH**/ ?>