@extends('dashboard.admin.index')
@section('title', 'All Facilitators')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">Facilitators $ Graders <a href="{{route('teachers.create')}}"><button type="button" class="btn btn-outline-primary">Add New</button></a></h5> 
                </div>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            {{-- <th>Date</th> --}}
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            {{-- <td>{{ $user->created_at->format('d/m/Y') }}</td> --}}
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->role_id}}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->p_name }}</td>
                                          
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit facilitator"
                                        class="btn btn-info" href="{{ route('teachers.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                    class="btn btn-warning" href="{{ route('impersonate', $user->id) }}"><i
                                        class="fa fa-unlock"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete facilitator"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endforeach
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>
@endsection