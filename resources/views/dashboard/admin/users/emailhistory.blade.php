@extends('dashboard.admin.index')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix.css">
@endsection
@section('title', 'Email History')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div>
                <div>
                    <h3>Email History</h3>
                </div>
            </div>

           
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-12">
                        <div
                            class="form-group{{ $errors->has('program') ? ' has-error' : '' }}">
                        </div>
                        <div class="form-group">
                            <label>Email Content</label>

                            <textarea class="form-control" id="summary-ckeditor" name="content">{!!  $email->content !!}</textarea>
                        </div>
    
                    </div>
                </div>
        </div>
    </div>
   
</div>
@endsection
@section('extra-scripts')

<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('summary-ckeditor');
</script>


@endsection