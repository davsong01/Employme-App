
@if(session()->get('message'))
<div class="alert alert-success" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Success!</strong> {{ session()->get('message')}}
</div>
@endif
@if(session()->get('error'))
<div class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> {{ session()->get('error')}}
</div>
@endif
@if(session()->get('msg'))
<div class="alert alert-warning" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
    <strong>Error!</strong> {{ session()->get('msg')}}
</div>
@endif
@if(session()->get('warning'))
<script>
    alert('WARNING! Details not saved! Student cannot pay more than program fee')
</script>
<script>
    alert('Please try again')
</script>
@endif