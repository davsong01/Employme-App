@extends('dashboard.admin.index')
@section('title', 'Employme CRM')
@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> {{$complains->count()}} Complain(s)</h6>

                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                    <h6 class="text-white"><b></b>{{$resolvedComplains}} Complains Resolved</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="fa fa-spinner"></i></h1>
                    <h6 class="text-white"><b> {{$InProgressComplains}} </b> In Progress</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-danger text-center">
                    <h1 class="font-light text-white"><i class="fa fa-clock"></i></h1>
                    <h6 class="text-white">{{$pendingComplains}} Complain(s) Pending</h6>
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
                            <th>Assignee</th>                            
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complains as $complain)
                        <tr>
                            <td>EMPL000{{ $complain->id}}</td>
                            <td>{{ $complain->user->name }} <span style="color:blue">({{ $complain->user->responseStatus }}% Response Rate)</span></td>
                            <td>{{ $complain->created_at->format('d/m/Y') }}</td>
                            <td>{{$complain->status}}</td>
                            <td>
                                <div class="btn-group">

                                    <a data-toggle="tooltip" data-placement="top" title="View Complain"
                                        class="btn btn-info" href="{{route('complains.edit', $complain->id)}}"><i
                                            class="fa fa-eye"></i></a>

                                    @if($complain->status <> 'Resolved')
                                        <a data-toggle="tooltip" data-placement="top" title="Mark as Resolved"
                                            class="btn btn-success" href="{{route('crm.resolved', $complain->id)}}"><i
                                                class="fa fa-check"></i></a>
                                    @endif
                                        <form action="{{ route('complains.destroy', $complain->id)}}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete Complain"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                </div>
                            </td>
                        </tr> @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Assignee</th>
                            <th>Date Created</th>
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
    $(".delete").on("submit", function () {
        return confirm("Are you sure?");
    });
</script>
@endsection