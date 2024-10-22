<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('modal.css')); ?>" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #e9ecef;
        border-radius: 20px;
        padding: 0.375rem 0.75rem;
        height: calc(2.25rem + 2px);
        font-size: 0.875rem;
        color: #4F5467;
        background-color: #fff;
        line-height: 1.5;
    }

    .select2.select2-container.select2-container--default {
        width: 100% !important;
    }
</style>
<?php $__env->stopSection(); ?>
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
                                
                                <?php 
                                    $string =  "*Name:* " . $pop->name . "
                                    *Phone:* " . $pop->phone . "
                                    *Email:* " . $pop->email . "
                                    *Training:* " . $pop->program->p_name;
                                ?>
                                <a class="btn btn-dark btn-sm rounded" href="https://api.whatsapp.com/send?phone=2348180010243&text=<?php echo e(urlencode($string)); ?>" target="_blank">
                                    <i class="fab fa-whatsapp"></i> Send via WhatsApp
                                </a>
                            </td>
                            <td><?php echo e($pop->program->p_name); ?> <br>(<?php echo e($pop->program->e_amount <= 0 ? 'Amount: '.$pop->currency_symbol.$pop->program->p_amount : 'E/Amount '. $pop->currency_symbol.$pop->program->e_amount); ?>)
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
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select Training",
            width: '100%'
            allowClear: true            
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payments/popfull.blade.php ENDPATH**/ ?>