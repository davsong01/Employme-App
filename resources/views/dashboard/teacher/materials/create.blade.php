@extends('dashboard.admin.index')
@section('title', 'Add study material')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new study Material for: <strong>{{ $program->p_name }}</strong></h4>
                    </div>
                    <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        <!--Gives the first error for input name-->

                        <div><small>{{ $errors->first('title')}}</small></div>

                        <div class="form-group">
                            <label>Select files</label>
                            <input type="file" id="file" name="file[]" value="" class="form-control" multiple>
                        </div>
                        <div><small style="color:red">{{ $errors->first('file[]')}}</small></div>
                        <input type="hidden" name="p_id" value="{{ $program->id }}">
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
@endsection