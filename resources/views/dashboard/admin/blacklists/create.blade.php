@extends('dashboard.admin.index')
@section('title', 'Add Blacklisted Value')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Add new Value</h4>
                    </div>
                    <form action="{{route('blacklist.store')}}" method="POST" class="pb-2">
                        @csrf
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="global" selected>Global</option> 
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="name">Value</label>
                                    <input type="text" class="form-control" name="value" value="{{ old('value') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="reason">Reason</label>
                                    <textarea name="reason" id="" class="form-control" cols="30" rows="10" value="{{ old('reason') }}" ></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option> 
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
    @endsection