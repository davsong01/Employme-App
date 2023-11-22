<?php $__env->startSection('title', 'Trainings'); ?>
<?php $__env->startSection('css'); ?>
<style>
  /* The Modal (background) */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black w/ opacity */
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  /* The Close Button */
  .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
  .modal-backdrop {
  position: relative;
  }
  a.pre-order-btn { 
    color:#000;
    background-color:gold;
    border-radius:1em;
    padding:1em;
    display: block;
    margin: 2em auto;
    width:100%;
    font-size:1.25em;
    font-weight:6600;
    text-align: center
  }

a.pre-order-btn:hover { 
    background-color:#000;
    text-decoration:none;
    color:gold;
}

</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('dashboard'); ?>
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                <?php if(Auth::user()->isImpersonating() ): ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="<?php echo e(route('stop.impersonate')); ?>" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                <?php endif; ?>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"  style="color:yellow !important; font-weight:bolder"
                        href="<?php echo e(url('/')); ?>" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">All Trainings</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="<?php echo e(url('dashboard')); ?>" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">My Trainings</span></a></li>
                <?php if($program->hasmock == 1): ?>
                    <li class="sidebar-item"><a href="<?php echo e(route('mocks.index', ['p_id' => $program->id])); ?>" class="sidebar-link"><i
                        class="fa fa-chalkboard"></i><span class="hide-menu">Pre Class Tests</span></a>
                    </li>    
                <?php endif; ?>
                <li class="sidebar-item"><a href="<?php echo e(route('materials.index', ['p_id' => $program->id])); ?>" class="sidebar-link"><i
                    class="fas fa-download"></i><span class="hide-menu">My Study Materials
                </span></a>
                </li>
                <?php if($program->hascrm == 1): ?>
                <li class="sidebar-item"><a href="<?php echo e(route('complains.index', ['p_id' => $program->id])); ?>" class="sidebar-link"><i
                    class="fas fa-comments"></i><span class="hide-menu">CRM Tool</span></a>
                </li>
                <?php endif; ?>
                <?php if(isset($facilitator) && !empty($facilitator)): ?>
                <li class="sidebar-item"><a href="<?php echo e(route('training.instructor', ['p_id'=>$program->id])); ?>" class="sidebar-link"><i
                    class="fas fa-chalkboard-teacher"></i><span class="hide-menu">Program Instructor</span></a>
                </li>
                <?php endif; ?>
                <li class="sidebar-item"><a href="<?php echo e(route('tests.index', ['p_id'=>$program->id])); ?>" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">Post Class Tests</span></a>
                </li>
                <li class="sidebar-item"><a href="<?php echo e(route('tests.results', ['p_id' => $program->id])); ?>" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">My Completed Tests</span></a>
                </li>
               
                <?php if($program->hasresult == 1 ): ?>
                <li class="sidebar-item"><a href="<?php echo e(route('results.show', ['result' => Auth::user()->id, 'p_id' => $program->id])); ?>" class="sidebar-link"><i class="fas fa-star-half-alt"></i><span class="hide-menu">My Result
                        </span></a>
                </li>
                <?php endif; ?>
                
                <?php if(auth()->user()->certificates->count() > 0 ): ?>
                <li class="sidebar-item"><a href="<?php echo e(route('certificates.index', ['p_id' => $program->id])); ?>" class="sidebar-link"><i
                            class="fas fa-certificate"></i><span class="hide-menu">My Certificate
                    </span></a>
                </li>
                <?php endif; ?>

                <?php if(isset($balance) && $balance > 0): ?>
                    <li class="sidebar-item">
                        <a class="blinking btn btn-danger btn-lg btn-block" href="<?php echo e(route('balance.checkout', ['p_id' => $program->id] )); ?>" class="form-horizontal">Pay balance of <?php echo e(number_format($balance)); ?> now</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
          <div class="card">
            <div class="card-body">
              <div class="card-title">
                <div>
                  <div class="card-content">
                        <a class="pre-order-btn" href="<?php echo e(route('download.program.brochure',['p_id' => $program->id])); ?>">DOWNLOAD TRAINING CATALOGUE</a>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
    </div>
</aside>

<?php if($program->show_catalogue_popup == 'yes' && auth()->user()->downloaded_catalogue == 'no'): ?>
<script>
    $(document).ready(function(){       
        $('#myModal').modal('show');
    }); 
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/student/trainingsindex.blade.php ENDPATH**/ ?>