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
                    <form action="{{ route('user.select') }}" method="POST" class="pb-2">
                        {{ csrf_field() }}
                        <!--Gives the first error for input name-->

                        <div><small>{{ $errors->first('title')}}</small></div>
                        <div class="form-group">

                            <label for="class">Select Training *</label>

                            <select name="program_id" id="program_id" class="form-control" required>

                                <option value="">-- Select Training --</option>

                                @foreach ($programs as $program)
                                @if($program->users_count)
                                <option value="{{ $program->id }}">{{$program->p_name}}</option>
                                @endif
                                @endforeach
                            </select>
                            <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
                        </div>

                        <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

                       
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection