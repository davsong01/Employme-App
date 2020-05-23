@extends('dashboard.student.index')
@section('title', 'Employme CRM')
@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                <h6 class="text-white"><b></b> {{$complains->count()}} Complain(s)</h6>
         
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white"><b></b>{{$resolvedComplains}} Complains Resolved</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white"><b> {{$InProgressComplains}} </b> In Progress</h6> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-6">
            <div class="card card-hover">
                <div class="box bg-danger text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white">{{$pendingComplains}} Complain(s) Pending</h6>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-6">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white">My Response Percentage: {{Auth::user()->responseStatus}}%</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if(session()->get('message'))
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> {{ session()->get('message')}}
            </div>
            @endif
            <h5 class="card-title"> All complains</h5>
            <div class="table-responsive">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Date Created</th>
                            <th>Date Updated</th>
                            <th>Name of Complainant</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complains as $complain)
                        <tr>
                            <td>EMPL000{{ $complain->id}}</td>
                            <td>{{ $complain->created_at->format('d/m/Y') }}</td>
                            <td>{{ $complain->updated_at->format('d/m/Y') }}</td>
                            <td>{{$complain->name}}</td>
                            <td>{{$complain->status}}</td>
                            <td>
                                <div class="btn-group">

                                <a data-toggle="tooltip" data-placement="top" title="View Complain" class="btn btn-info" href="{{route('complains.edit', $complain->id)}}"><i class="fa fa-eye"  ></i></a>             
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Date Created</th>
                            <th>Date Updated</th>
                            <th>Name of Complainant</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
<script>
        $(".delete").on("submit", function(){
            return confirm("Are you sure?");
        });
    </script>
@endsection