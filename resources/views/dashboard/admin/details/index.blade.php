@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
                <h5> All Trainings</h5>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title m-b-0">Retrieve User details</h4>
                    <div class="m-t-20">

                        <form action="{{ route('details.index') }}" method="POST" enctype="multipart/form-data"
                            class="pb-2">
                            <!--Gives the first error for input name-->
                            <div><small>{{ $errors->first('type')}}</small></div>
                            <div class="form-group">
                                <label for="class">Select Retieval type</label>
                                <select name="type" id="type" class="form-control" required>
                                        <option value="email">Email</option>
                                        <option value="name">Name</option>
                                    <option value="t_phone">Phone Number</option>
                                    
                                </select>
                                <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                            </div>
                            <!--Gives the first error for input name-->
                            <div><small>{{ $errors->first('title')}}</small></div>
                            <div class="form-group">
                                <label for="class">Select Training *</label>
                                <select name="program_id" id="program_id" class="form-control" required>
                                    <option value=""></option>
                                    @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{$program->p_name}}</option>
                                    @endforeach
                                </select>
                                <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
                            </div>

                            <input type="submit" name="submit" value="Submit" class="btn btn-primary"
                                style="width:100%">

                            {{ csrf_field() }}
                        </form>

                    </div>
                   
                </div>
            </div>
        </div>

    </div>
</div>
</div>

@endsection