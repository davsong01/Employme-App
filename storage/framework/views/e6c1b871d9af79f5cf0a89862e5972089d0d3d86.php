<?php $__env->startSection('title', 'Payment modes'); ?>
<?php $__env->startSection('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Payment modes 
                        <a href="<?php echo e(route('payment-modes.create')); ?>"><button type="button" class="btn btn-outline-primary">Add Payment mode</button></a>
                    </h5>
                </div>
            </div>
            <?php $default_currency = \App\Models\Settings::value('CURR_ABBREVIATION') ?>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Processor</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <tr>
                            <td><?php echo e($i++); ?></td>
                            <td> <img src="paymentmodes/<?php echo e($mode->image); ?>" alt="image" class="rounded-circle" width="50" height="50" style="margin: auto;display: block;"> </td>
                            <td><?php echo e($mode->type); ?></td>
                            
                            <td>
                               <strong style="color:blue"> <?php echo e($mode->name); ?>(<?php echo e($mode->currency_symbol); ?>)</strong> | <strong style="color:red"> <?php echo e($mode->currency); ?></strong> | 1 <?php echo e($default_currency); ?> = <?php echo e($mode->exchange_rate . ' '. $mode->currency); ?> <br>
                                <strong style="color:green">Secret key: </strong><?php echo e($mode->secret_key); ?> <br>
                                
                                <button class="btn btn-<?php echo e($mode->status == 'active' ? 'success':'danger'); ?> btn-xs"><?php echo e(ucFirst($mode->status)); ?></button>
                            </td>
                            <td><?php echo e($mode->processor); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit payment mode"
                                        class="btn btn-info" href="<?php echo e(route('payment-modes.edit', $mode->id)); ?>"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                  
                                    <form action="<?php echo e(route('payment-modes.destroy', $mode->id)); ?>" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>


                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <script type="text/javascript" src="<?php echo e(asset('src/jspdf.min.js')); ?> "></script>
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/jspdf.plugin.autotable.min.js'
                    )); ?>"></script>
                    
                    <script type="text/javascript" src="<?php echo e(asset('src/tableHTMLExport.js')); ?>"></script>
                    
                    <script type="text/javascript">
                                           
                      $("#csv").on("click",function(){
                        $("#zero_config").tableHTMLExport({
                          type:'csv',
                          filename:'Participants.csv'
                        });
                      });
                    
                    </script>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/payment_modes/index.blade.php ENDPATH**/ ?>