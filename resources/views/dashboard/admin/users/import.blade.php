@extends('dashboard.admin.index')
@section('title', 'Add New question')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{ route('users.import')}}" method="POST" name="importform" class="pb-2"  enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                               
                                <div class="form-group">
                                    <label for="class">Upload File </label> <a class="pull-right" style="font-size:smaller;" href="{{ route('user-bulk-sample', 'bulk_users.xlsx') }}"><i class="fa fa-download"></i> Download sample</a>
                                   <input type="file" name="file" class="form-control" accept=".csv, .xlsv, .xls, .xlsx" required>
                                    <br>
                                    @if ($errors->has('file'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('file') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <input type="hidden" value="{{ $p_id  }}" name="p_id">
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection