@extends('dashboard.admin.index')
@section('title', 'Update payment method')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Update payment method</h4>
                    </div>
                    <form action="{{route('paymentmethod.update', $paymentmethod->id)}}" method="POST" class="pb-2" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ?? $paymentmethod->name }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control" name="image" value="">
                                </div>
                               
                            </div>
                        </div>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">Update</button>
                        </div>

                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
    @endsection