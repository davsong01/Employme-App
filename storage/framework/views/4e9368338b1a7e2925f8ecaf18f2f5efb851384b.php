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
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', $details['user_name'] ); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <?php if($details['allow_editing'] != 0): ?>
          <div class="card-title">
            <?php echo $__env->make('layouts.partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <h4 style="color:green">Update Scores for: <?php echo e($details['user_name']); ?></h4>
            <div>
              <?php if(isset($history) && !empty($history)): ?>
                <span class="retake">RESITS </span><span style="background: aqua;padding: 5px;border-radius: 50px;"
                class="thread-count"><?php echo e($history->count()); ?></span>
                <?php if($history->count() > 0): ?>
                <a style="border-radius: 6px;" class="btn btn-info btn-sm" href="#resit" id="myBtn">
                  View Resit History
                </a>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <form action="<?php echo e(route('results.update', $results->id)); ?>" method="POST" enctype="multipart/form-data"
            class="pb-2">
            <?php echo e(csrf_field()); ?>

            <?php echo e(method_field('PATCH')); ?>

            <div class="row">
              <div class="col-md-6">
                <h6 style="color:red">Training details</s></h6>
                <!--Gives the first error for input name-->
                <div class="form-group">
                  <label>Training</label>
                  <input type="text" name="" value="<?php echo e($program->p_name); ?>" class=" form-control" disabled>
                </div>

                <small><small style="color:red"><?php echo e($errors->first('passmark')); ?></small></small>
                <div class="form-group">
                  <label>Pass Mark Set</label>
                  <input type="number" name="passmark"
                    value="<?php echo e(old('passmark') ?? $program->scoresettings->passmark); ?>" class="form-control" min="0"
                    max="100" required disabled>
                </div>
                <small><small style="color:red"><?php echo e($errors->first('passmark')); ?></small></small>
              </div>

              <div class="col-md-6">
                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                <h6 style="color:red">Add Email score here</h6>
                <div class="form-group">
                  <label>Email Score* <span style="color:green">(Max score =
                      <?php echo e($program->scoresettings->email); ?>)</span></label>
                  <input type="number" name="emailscore" value="<?php echo e(old('emailscore') ?? $details['email_test_score']); ?>"
                    class="form-control" min="0" max="<?php echo e($program->scoresettings->email); ?>">
                </div>

                <div><small style="color:red"><?php echo e($errors->first('emailscore')); ?></small></div>
                <?php endif; ?>
                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?>
                <h6 style="color:red">Add Role play score here</h6>
                <div class="form-group">
                  <label>Role Play Score* <span style="color:green">(Max score =
                      <?php echo e($program->scoresettings->role_play); ?>)</span></label>
                  <input type="number" name="roleplayscore"
                    value="<?php echo e(old('roleplayscore') ?? $details['role_play_score']); ?>" class="form-control" min="0"
                    max="<?php echo e($program->scoresettings->role_play); ?>" required>
                </div>
                <div><small style="color:red"><?php echo e($errors->first('roleplayscore')); ?></small></div>
              </div>
              <?php endif; ?>

            </div>

            <div class="row">
              <div class="col-md-12">
                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                <h6 style="color:red">Certificate Test Submision</h6>
                <p>Please go through this user's attempt and grade user with the grade box below</p>


                <div class="form-group">
                  <?php $__currentLoopData = $user_results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $results): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                  <label for="title"> <strong style="color:green">QUESTION <?php echo e($i ++); ?></strong></label><br>
                  <strong style="color:green">Module:</strong> <?php echo $results->module->title.'<br><br>'; ?></span>

                  <strong style=:color:green>Question:</strong> <?php echo $results['title'] .'<br><br>'; ?></span>

                  <strong>Answer:</strong> <?php echo $results['answer'] .'<br><br>'; ?>


                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <div class="form-group">
                    
                  </div>
                </div>
                

                <h6 style="color:red">Now, score this candidate's certification test (Result with score of 10 will be
                  recorded as 'processing' on cadidate's dashboard): </h6>
                <div class="form-group">
                  <label><span style="color:green">(Max score =
                      <?php echo e($program->scoresettings->certification); ?>)</span></label>
                  <input type="number" name="certification_score"
                    <?php echo e((!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))) ? "" : 'Readonly'); ?>

                    value="<?php echo e(old('certification_score') ?? $details['certification_score']); ?>" class="form-control"
                    min="0" max="<?php echo e($program->scoresettings->certification); ?>">
                </div>
                <?php else: ?>
                <input type="hidden" value="<?php echo e($details['certification_score']); ?>" name="certification_score">
                <?php endif; ?>
                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))): ?>
                <div class="form-group">
                  <label>Grader Comment(Optional) </label>
                  <textarea name="grader_comment" class="form-control" id="" cols="30" rows="10"
                    value="<?php echo e(old('grader_comment') ?? $details['grader_comment']); ?>"><?php echo e(old('grader_comment') ?? $details['grader_comment']); ?></textarea>

                </div>
                <?php endif; ?>
                <?php if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role()))): ?>
                <div class="form-group">
                  <label>Facilitator Comment(Optional) </label>
                  <textarea name="facilitator_comment" class="form-control" id="" cols="30" rows="10"
                    value="<?php echo e(old('facilitator_comment') ?? $details['facilitator_comment']); ?>"><?php echo e(old('facilitator_comment') ?? $details['facilitator_comment']); ?></textarea>

                </div>
                <?php endif; ?>
                <div class="row">
                  <button type="submit" class="btn btn-primary form-group" style="width:100%">Submit</button>
                </div>
              </div>
            </div>

          </form>
          <?php else: ?>
          <h2>Expecting user to re-take certification tests hence scores cannot be modified </h2>
          <?php endif; ?>
        </div>
      </div>
      <!-- The Modal -->
      <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
          <span class="close">&times;</span>
          <div class="card">
            <div class="card-body">
              <div class="card-title">
                <h4 style="color:green">RESIT HISTORY</h4> <br>
                <div>
                  <div class="card-content">
                    <?php if(isset($history) && !empty($history)): ?>
                      <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      
                            <strong style="background: #f71193;padding: 11px;border-radius: 20px;color: white;;"> Submitted on: <?php echo e($result->submitted_on); ?> </strong> <br> <br>

                            <label for="title"> <strong style="color:green">QUESTION <?php echo e($i ++); ?></strong></label><br>
                            <strong style="color:green">Module:</strong> <?php echo $result->module->title.'<br><br>'; ?></span>

                            <strong style=:color:green>Question:</strong> <?php echo $result->title .'<br><br>'; ?></span>

                            <strong>Answer:</strong> <?php echo $result->answer .'<br><br>'; ?> <br>
                             <br>
                            <strong style="color:green">Facilitator's comment</strong>(<?php echo e($result->marked_by); ?>): <?php echo $result->facilitator_comment; ?></span> <br>
                            <strong style="color:green">Grader's comment:</strong>(<?php echo e($result->grader_comment); ?>): <?php echo $result->grader_comment; ?></span> <br>
                            <strong style="color:green">Score:</strong><?php echo e($result->certification_test_score); ?></span>
                            
                          
                        
                        <hr style="border-top: 1px solid red;">

                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("myBtn");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks the button, open the modal 
            btn.onclick = function () {
              modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function () {
              modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
              if (event.target == modal) {
                modal.style.display = "none";
              }
            }
        </script>
      <?php $__env->stopSection(); ?>
<?php echo $__env->make('dashboard.admin.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/admin/results/edit.blade.php ENDPATH**/ ?>