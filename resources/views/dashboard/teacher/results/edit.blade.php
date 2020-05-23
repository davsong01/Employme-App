@extends('dashboard.admin.index')
@section('title', $results->user->name )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                                @if(session()->get('message'))
                                <div class="alert alert-success" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <strong>Success!</strong> {{ session()->get('message')}}
                                      </div>
                                @endif
                            <h4 class="card-title">Edit details for: {{$results->user->name}}</h4>
                        </div>
                        <form action="{{route('results.update', $results->user_id)}}" method="POST" enctype="multipart/form-data" class="pb-2">
                            {{ method_field('PATCH') }}
                        @include('dashboard.admin.results.eform')
            </div>
        </div>
    </div>
</div>
@endsection

