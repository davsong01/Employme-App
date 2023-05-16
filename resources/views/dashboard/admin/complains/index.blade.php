@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' CRM Tool' }}
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> {{$complains->count()}} Query(s)</h6>

                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                    <h6 class="text-white"><b></b>{{$resolvedComplains}} Query(s) Resolved</h6>
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
                    <h6 class="text-white">{{$pendingComplains}} Query(s) Pending</h6>
                </div>
            </div>
        </div>

    </div>


    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Queries <a href="{{route('complains.create')}}"><button type="button" class="btn btn-outline-primary">Add New Query</button></a></h5> 
                </div>
            </div>
            
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Assignee</th>                            
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>SLA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complains as $complain)
                        <tr>
                            <td>EMPL000{{ $complain->id}} <br>
                               
                            </td>
                            <td>{{ $complain->user->name ?? 'NOT SET'}} <span style="color:blue">({{ $complain->user->responseStatus ?? '0' }}% Response Rate)</span> <br>
                                @if(isset($complain->program))
                                 <small style="color:green"><strong>Training:</strong> {{ $complain->program->p_name ?? '' }}</small>
                                 @endif
                            </td>
                            <td>{{ $complain->created_at->format('d/m/Y') }}</td>
                            <td>{{$complain->status}}</td>
                            <td>{{ $complain->sla }} {{ $complain->sla ? 'hours' : '' }}</td>
                            <td>
                                <div class="btn-group">

                                    <a class="btn btn-info" href="{{route('complains.edit', $complain->id)}}"><i
                                            class="fa fa-eye"></i> View</a>
                                    @if($complain->status <> 'Resolved')
                                        <a 
                                            class="btn btn-success" href="{{route('crm.resolved', $complain->id)}}"><i
                                                class="fa fa-check"></i> Resolve</a>
                                    @endif
                                    @if(Auth::user()->role_id == "Admin")
                                        <form action="{{ route('complains.destroy', $complain->id)}}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete Query"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr> @endforeach
                    </tbody>
                    
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