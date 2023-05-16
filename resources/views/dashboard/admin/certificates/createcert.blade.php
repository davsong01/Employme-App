@extends('dashboard.admin.index')

@section('title', 'Add Certificate')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Certificate</h4>
                    </div>
                    <form action="{{ route('certificates.save') }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ csrf_field() }}
                        <!--Gives the first error for input name-->

                        <div><small>{{ $errors->first('title')}}</small></div>
                        <div class="form-group">

                            <label for="class">Select User *</label>

                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value=""></option>
                                @foreach ($users->sortBy('name') as $user)
                                    @if($user->certificates_count <= 0)
                                        <option value="{{ $user->user_id }}">{{$user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div><small style="color:red">{{ $errors->first('user_id')}}</small></div>

                            <div class="form-group">
                                <label>Choose Certificate</label>
                                <input type="file" id="certificate" name="certificate" class="form-control" required>
                            </div>
                            <div><small style="color:red">{{ $errors->first('certificate')}}</small></div>
                        </div>
                        <input type="hidden" value="{{ $p_id }}" name="p_id">
                       
                        <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#user_id').select2();
        });
    </script>
    @endsection