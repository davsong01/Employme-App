@extends('dashboard.admin.index')
@section('title', 'Training Locations')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
         </div>
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Locations/Venue(Locations will appear on the frontend when purchasing a training) <br><br><a href="{{route('locations.create')}}"><button type="button" class="btn btn-outline-primary">Add New Location</button></a></h5> 
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered responsive">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Training</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $location->title }}</td>
                            <td>{{ $location->program->p_name }}</td>
                            <td>
                                <div class="btn-group">                                    
                                    <a data-toggle="tooltip" data-placement="top" title="Edit location"
                                    class="btn btn-primary" onclick="return confirm('Are you really sure?');" href="{{ route('locations.edit', $location->id) }}"><i
                                        class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete location"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection