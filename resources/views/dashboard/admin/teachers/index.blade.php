@extends('dashboard.admin.index')
@section('title', 'All Facilitators')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
                @if(session()->get('message'))
                <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> {{ session()->get('message')}}
                      </div>
                @endif
            <h5 class="card-title">All Facilitators</h5>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Type</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->t_phone}}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->program->p_name }}</td>
                            <td>{{ $user->role_id }}</td>
                     
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown">Action
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><i class="far fa-edit"></i><a href="{{ route('teachers.edit', $user->id) }}"> Edit</a></li>
                                      <!--  <li><i class="far fa-envelope"></i><a href="{{ route('teachers.show', $user->id) }}"> Send E-receipt</a></li> -->
                                        
                                    <form class="delete" action="{{ route('teachers.destroy', $user->id) }}" method="POST">
                                            <i class="fas fa-trash"></i>
                                            <input type="hidden" name="_method" value="DELETE">
                                            {{ csrf_field() }}
                                            <input type="submit" class="custombutton" value="Delete">
                                        </form> </li>
                                    </ul>
                                </div>
                            </td>
                            @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Type</th>
                            <th>Manage</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection