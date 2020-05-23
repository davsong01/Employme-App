@extends('dashboard.admin.index')
@section('title', 'Upload result')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="card-title">Upload Result: </h4>
                    </div>
                    @if(session()->get('message'))
                        <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Success!</strong> {{ session()->get('message')}}
                            </div>
                        @endif 
                    
                    <form action="{{ route('results.store') }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        @include('dashboard.teacher.results.form')
                </div>
            </div>
        </div>
    </div>
    @endsection