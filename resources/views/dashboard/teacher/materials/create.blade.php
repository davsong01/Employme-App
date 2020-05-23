@extends('dashboard.teacher.index')
@section('title', 'Add study material')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="card-title">Add new study Material</h4>
                    </div>
                    <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        @include('dashboard.teacher.materials.form')
                </div>
            </div>
        </div>
    </div>
    @endsection