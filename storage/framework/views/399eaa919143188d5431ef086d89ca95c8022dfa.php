<?php 
    use App\Settings;
?>

<?php $__env->startSection('title', 'Account TopUp History'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <h5 class="card-title">Account TopUp History </h5>
            <div style="margin: 15px 0;">
                Total Balance: <span class="bal" style="padding: 5px;border-radius: 10px;background: antiquewhite;"><?php echo e(Settings::value('DEFAULT_CURRENCY'). number_format($balance)); ?></span>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Transaction ID</th>
                            <th>Method</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>Name: <?php echo e($wallet->user->name); ?> <br>Email: <?php echo e($wallet->user->email); ?> <br>Date: <?php echo e(date("M jS, Y", strtotime($wallet->created_at))); ?></td>
                            <td><?php echo e($wallet->transaction_id); ?></td>
                            <td><?php echo e(ucfirst($wallet->method)); ?></td>
                            <td style="color:<?php echo e($wallet->type == 'credit' ? 'green' : 'red'); ?>"><?php echo e($wallet->type); ?></td>
                            <td><?php echo e(Settings::value('DEFAULT_CURRENCY').number_format($wallet->amount)); ?></td>
                            <td style="color:<?php echo e($wallet->status == 'approved' ? 'green' : 'red'); ?>"><?php echo e(ucfirst($wallet->status)); ?> 
                                <?php if(isset($wallet->admin_id)): ?>
                                <br>
                                <small><strong>By: <?php echo e($wallet->admin->name); ?></strong> </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <?php if($wallet->method == 'manual' && $wallet->type == 'credit' && $wallet->status == 'pending'): ?>
                                    <a href="#"><img title="View Proof of payment" id="myImg<?php echo e($wallet->proof_of_payment); ?>" src="pop/<?php echo e($wallet->proof_of_payment); ?>" alt="View pop" style="width:29px;max-width:300px"></a>
                                    
                                    <a onclick="return(confirm('Are you sure'))" title="Approve TopUp" class="btn btn-success btn-sm" href="<?php echo e(route('approve.wallet.history', $wallet->id)); ?>"><i class="fa fa-check"></i>
                                    </a>
                                    <a onclick="return(confirm('Are you sure'))" title="Delete TopUp" class="btn btn-danger btn-sm" href="<?php echo e(route('delete.wallet.history', $wallet->id)); ?>"><i class="fa fa-trash"></i>
                                    </a>
                                    

                                    <div id="myModal" class="modal">
                                        <span class="close2">&times;</span>
                                        <img class="modal-content" id="img01">
                                        <div id="caption"></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                </div>
                            </td>
                        </tr>
                        
                        <script>
                            // Get the modal
                            var modal = document.getElementById("myModal");

                            // Get the image and insert it inside the modal - use its "alt" text as a caption
                            var img = document.getElementById("myImg<?php echo e($wallet->proof_of_payment); ?>");
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
<?php echo $__env->make('dashboard.student.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payments/wallets.blade.php ENDPATH**/ ?>