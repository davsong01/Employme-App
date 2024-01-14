<?php 
    use App\Settings;
    $balance = app('App\Http\Controllers\WalletController')->getWalletBalance(auth()->user()->id);
    $currency = Settings::value('DEFAULT_CURRENCY');
?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-title">
                    <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
               
                <div class="card-body" style="text-align: center;padding-bottom:20px">
                    <h4 style="color:red; text-align:center; padding:20px">You have a pending balance payment of <?php echo e($currency. number_format($program->checkBalance($program->id))); ?> for : <?php echo e($program->p_name); ?></h4> <br>
                   
                    <?php if($program->checkBalance($program->id) < $balance): ?>
                       
                        <a style="margin-top:15px" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal" class="mr-1 mb-1 pay-option" name="payment_mode" value="<?php echo e($payment_mode->id); ?>"><i class="fa fa-credit-card"></i> Pay from Account Balance (<?php echo e($currency.number_format($balance)); ?>)</a><br><br><br>
                        <p><a class="btn btn-success btn-sm" target="_blank" style="border-radius:5px" href="<?php echo e(route('home')); ?>"><i class="fa fa-plus"></i>&nbsp;Top Up Account Balance</a>
                        </p>
                    <?php else: ?> 
                        <p>
                             <span class="balance">Your Account Balance : (<?php echo e($currency.number_format($balance)); ?>)</span> <br>
                            <a target="_blank" class="btn btn-success btn-sm" style="border-radius:5px"href="<?php echo e(route('home')); ?>"><i class="fa fa-plus"></i>&nbsp;Top Up Account Balance to be able to make payment</a></p>
                        </p>
                    <?php endif; ?>
                    
                </div>
                <div class="card-body" style="text-align: center;">
                    
                </div>
            </div>
            
        </div>


        
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pay balance of <?php echo e($program->checkBalance($program->id )); ?>from Account Balance (<?php echo e($currency.number_format($balance)); ?>)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('account.pay', 'wallet')); ?>" method="POST" onsubmit="return loader()">
                    <?php echo csrf_field(); ?>
                  
                    <?php if($program->allow_flexible_payment == 'yes'): ?>
                    <div class="col-md-12">
                        <div class="form-group<?php echo e($errors->has('amount') ? ' has-error' : ''); ?>">
                            <label for="amount">Amount</label>
                            <input id="amount" type="number" class="form-control" amount="amount" min="1" name="amount" value="<?php echo e(old('amount')); ?>" autofocus required>
                            <?php if($errors->has('amount')): ?>
                            <span class="help-block">
                                <strong><?php echo e($errors->first('amount')); ?></strong>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?> 
                    <input type="hidden" class="form-control" name="amount" value="<?php echo e($program->checkBalance($program->id)); ?>" autofocus required>
                    <?php endif; ?>
                    <input type="hidden" class="form-control" name="p_id" value="<?php echo e($program->id); ?>" required>
                    <button type="submit" class="btn btn-success">Make payment</button> <span id="spinner" style="display:none"><i style="color:red" class="fa fa-spinner fa-spin"></i> <strong style="color:red">Please wait, payment is processing</strong></span>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="s-button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
    <script>
        function loader(){
            let doc;
            let result = confirm("Are you sure you want to make payment?");
            if (result == true) {
                $("#spinner").show();
                $("s-button").hide();
                return true;
            } else {
                $('#exampleModal').modal('hide');
                return false;
            }
        }
    </script>
<?php $__env->stopSection(); ?>
    
<?php echo $__env->make('dashboard.student.trainingsindex', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/balance_checkout.blade.php ENDPATH**/ ?>