<?php $__env->startSection('title', 'Payment History'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <h5 class="card-title">Proof of Payment History</h5>
            <div class="">
                <table id="myTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer details</th>
                            <th>Training details</th>
                            <th>Amount Paid</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Actions</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $pops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                     
                        <tr>
                            <td><?php echo e($pop->date); ?></td>
                            <td><?php echo e($pop->name); ?> <br>
                                <?php echo e($pop->phone); ?> <br>
                                <?php echo e($pop->email); ?> <br>
                            </td>
                            <td><?php echo e($pop->program->p_name); ?> <br>(<?php echo e($pop->program->e_amount <= 0 ? 'Amount: '.$pop->currency_symbol.$pop->program->p_amount : 'E/Amount '. $pop->currency_symbol.$pop->program->e_amount); ?>)
                                 <?php if($pop->program->allow_preferred_timing == 'yes'): ?> <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;"><?php echo e($pop->preferred_timing); ?> </span> <br> <?php endif; ?>
                            <?php if(isset($pop->is_fresh)): ?> <br>
                            <span style="margin:5px 10px;border-radius:10px" class="btn btn-info btn-sm">Fresh Payment</span>
                            <?php endif; ?>
                            </td>
                            <td><?php echo e(number_format($pop->amount)); ?></td>
                            <td><?php echo e($pop->bank); ?></td>
                            <td><?php echo e($pop->location); ?></td>
                           
                             <td>
                                <div class="btn-group">
                                    <a href="#"><img title="View Proof of payment" id="myImg<?php echo e($pop->file); ?>" src="view/<?php echo e($pop->file); ?>" alt="<?php echo e($pop->name); ?>" style="width:40px;max-width:300px"></a>
                                    
                                    <a  title="Approve Payment" class="btn btn-success" href="<?php echo e(route('pop.show', $pop->id)); ?>"><i class="fa fa-check"></i>
                                    </a>
                                    <form action="<?php echo e(route('pop.destroy', $pop->id)); ?>" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete proof of payment"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
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
                        var img = document.getElementById("myImg<?php echo e($pop->file); ?>");
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
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="transTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Customer details</th>
                            <th>Training details</th>
                            <th>Payment details</th>
                            <th>Actions</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><a href="<?php echo e(route('users.edit', $transaction->user_id)); ?>" target="_blank"><?php echo e($transaction->name ?? 'N/A'); ?> &nbsp;<img src="/external.png" alt="" style="width: 10px;"></a>
                                <br><?php echo e($transaction->t_phone ?? 'N/A'); ?> <br> <?php echo e($transaction->email ?? 'N/A'); ?> </td>
                            <td>
                                <small class="training-details">
                                    <a href="<?php echo e(route('programs.edit', $transaction->program_id)); ?>" target="_blank"><strong>Training:</strong> <?php echo e($transaction->p_name ?? 'N/A'); ?></a><br>  
                                    <?php if($transaction->allow_preferred_timing == 'yes'): ?> <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;"><?php echo e($transaction->preferred_timing); ?> </span> <br> <?php endif; ?>
                                    <strong>Paid:</strong> <?php echo e($transaction->currency. number_format($transaction->t_amount)); ?>

                                    <?php if(!is_null($transaction->coupon_code)): ?>
                                    <span style="color:blue">
                                       <strong>Coupon (<?php echo e($transaction->coupon_code); ?>) Applied | <?php echo e($transaction->currency.number_format($transaction->coupon_amount)); ?>  </strong>
                                    </span>
                                    <?php endif; ?>
                                    <br>
                                    <strong>Balance:</strong>
                                         <?php if($transaction->balance > 0 ): ?>
                                            <span style="color:red"><?php echo e($transaction->currency. number_format($transaction->balance)); ?> </span>
                                        <?php else: ?>
                                            <span style="color:green"><?php echo e($transaction->currency.  number_format($transaction->balance)); ?></span>
                                        <?php endif; ?>
                                    <br>      
                                   
                                    <strong>Bank: </strong><?php echo e($transaction->t_type ?? null); ?> <br>
                                    <?php
                                        if(isset($transaction->t_location) && isset($transaction->t_location)){
                                            $locations = json_decode($transaction->locations, true);
                                            $location_address = $locations[$transaction->t_location] ?? null;
                                        }
                                    ?>
                                 
                                    <?php if(isset($transaction->t_location) && !empty($transaction->t_location) && !empty( $location_address)): ?>
                                    <strong>Location:</strong> <?php echo e($transaction->t_location); ?>(<?php echo e($location_address); ?>) <br>
                                    <?php endif; ?>
                                    <strong>Date: </strong><?php echo e($transaction->created_at); ?>

                                   
                                </small>
                                
                            </td>   
                            <td>
                                <small class="id-details">
                                    <strong>Invoice ID:</strong> <?php echo e($transaction->invoice_id); ?> <br>
                                    <strong>Transaction ID:</strong> <?php echo e($transaction->transid); ?> 
                                    <?php if(isset($transaction->balance_amount_paid)): ?>
                                    <br>
                                    <strong>Last Balance Paid:</strong> <?php echo e($transaction->currency_symbol.number_format($transaction->balance_amount_paid)); ?> <br>
                                    <strong>Paid At:</strong> <?php echo e($transaction->balance_paid); ?> 
                                    <?php endif; ?>
                                    <br>
                                    <strong>Payment Type:</strong> <?php echo e($transaction->paymenttype); ?> <br>
                                    <?php if(isset($transaction->training_mode)): ?>
                                    <strong>Training Mode:</strong> <?php echo e($transaction->training_mode); ?> <br>
                                    <?php endif; ?>
                                     <strong>Type: </strong><?php echo e($transaction->t_type); ?> <br>
                                    <strong>Currency: </strong><?php echo e($transaction->currency); ?>

                                </small>
                            </td>
                             <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Transaction"
                                        class="btn btn-info btn-sm" href="<?php echo e(route('payments.edit', $transaction->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning btn-sm" href="<?php echo e(route('payments.print', $transaction->id)); ?>"><i
                                            class="fa fa-print"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                        class="btn btn-primary btn-sm" href="<?php echo e(route('payments.show', $transaction->id)); ?>"><i
                                            class="far fa-envelope"></i>
                                    </a>
                                    <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role()))): ?>
                                        <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                            class="btn btn-dark btn-sm" href="<?php echo e(route('impersonate', $transaction->user_id)); ?>"><i
                                                class="fa fa-unlock"></i>
                                        </a>
                                    <?php endif; ?> 
                                    <form action="<?php echo e(route('payments.destroy', $transaction->id)); ?>" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete transaction"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payments/index.blade.php ENDPATH**/ ?>