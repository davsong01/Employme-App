<div class="modal fade" id="" tabindex="-1" aria-labelledby="resultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-slideout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resultModalLabel">Result Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="resultContent">
                    <!-- Dynamic content will be loaded here -->
                    <p class="text-center">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>


<a class="btn btn-info btn-sm btn-sm w-100 mb-3 viewUpdateResult" 
   href="javascript:void(0);" 
   data-toggle="modal" 
   data-target="#resultModal" 
   data-url="{{ route('results.add', ['uid' => $user->user_id, 'pid' => $user->program_id]) }}">
   <i class="fa fa-eye"> New View/Update </i>
</a>


<div class="modal fade  come-from-modal right" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="resultModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.viewUpdateResult', function () {
        const url = $(this).data('url');
        const modalBody = $('#resultContent');
    
        // Show a loading indicator
        modalBody.html('<p class="text-center">Loading...</p>');
    
        // Fetch the result details
        $.ajax({
            url: url,
            method: 'GET',
            success: function (response) {
                // Populate the modal with the response
                modalBody.html(response);
            },
            error: function () {
                modalBody.html('<p class="text-danger text-center">An error occurred while loading the content. Please try again later.</p>');
            }
        });
    });
</script>
