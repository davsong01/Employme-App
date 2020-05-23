@extends('dashboard.admin.index')
@section('title', 'Teacher')
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
                                @if(session()->get('warning'))
                                    <script>alert('WARNING! Details not saved! Student cannot pay more than program fee')</script>
                                    <script>alert('Please try again')</script>
                                @endif
                            <h4 class="card-title">Add new Teacher</h4>
                        </div>
                        <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                         
                        @include('dashboard.admin.teachers.addform')
            </div>
        </div>
    </div>
</div>
@endsection