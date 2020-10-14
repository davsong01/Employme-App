{{-- @extends('dashboard.admin.index')
@section('title', 'select Training' )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h2>Post Class Tests</h2>
                        <h4 style="color:green">Select Training</h4>
                    </div>
                    <form action="{{route('results.getgrades')}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        
                        <div class="row">
                            <div class="col-md-12" style="margin-bottom:20px">
                                <!--Gives the first error for input name-->
                                 <select name="pid" id="pid" class="form-control" required>

                                    <option value="">Select Training</option>

                                    @foreach ($programs as $program)

                                    <option value="{{ $program->id }}">{{$program->p_name}}</option>

                                    @endforeach

                                </select>
                               
                                <small><small style="color:red">{{ $errors->first('pid')}}</small></small>
                            </div>
                        </div>
                            <button type="submit" class="btn btn-primary"
                                style="width:100%">Get Grades</button>
                        </div>
                        
                </div>
            </div>
        </div>
    </div>
    @endsection --}}

    @extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                 <h5 class="card-title" style="color:green"> Click the eye icon to View grades for respective trainings </h5><br>
                @include('layouts.partials.alerts')
             </div>
           
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Program Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $program->p_name }}</td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="View Grades"
                                        class="btn btn-info" href="{{ route('results.getgrades', $program->id)}}"><i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection