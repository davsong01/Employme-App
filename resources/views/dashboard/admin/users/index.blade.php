@extends('dashboard.admin.index')
@section('title', 'All Users')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <h5 class="card-title">All Students</h5>
            <div class="table-responsive">
                <table id="zero_config" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Role</th>
                            <th>Location</th>
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
                            <td {{!$user->program ? 'style=color:red' : ''}}>
                                {{ $user->program->p_name ?? 'Trashed Training' }}</td>
                            <td>{{ $user->role_id }}</td>
                            <td>{{ $user->t_location }}</td>
                            <td>
                                {{-- <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown">Action
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><i class="far fa-edit"></i><a href="{{ route('users.edit', $user->id) }}">
                                Edit</a></li>
                                <li><i class="far fa-envelope"></i><a href="{{ route('users.show', $user->id) }}"> Send
                                        E-receipt</a></li>

                                <form class="delete" action="{{ route('users.destroy', $user->id) }}" method="POST">
                                    <i class="fas fa-trash"></i>
                                    <input type="hidden" name="_method" value="DELETE">
                                    {{ csrf_field() }}
                                    <input type="submit" class="custombutton" value="Delete">
                                </form>
                                </li>
                                </ul>
            </div> --}}
            <div class="btn-group">
                <a data-toggle="tooltip" data-placement="top" title="Edit Users" class="btn btn-info"
                    href="{{ route('users.edit', $user->id) }}"><i class="fa fa-edit"></i>
                </a>
                <a data-toggle="tooltip" data-placement="top" title="Send E-receipt" class="btn btn-primary"
                    href="{{ route('users.show', $user->id) }}"><i class="far fa-envelope"></i>
                </a>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                    onsubmit="return confirm('Are you really sure?');">
                    {{ csrf_field() }}
                    {{method_field('DELETE')}}

                    <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip" data-placement="top"
                        title="Delete user"> <i class="fa fa-trash"></i>
                    </button>
                </form>
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
                    <th>Role</th>
                    <th>Location</th>
                    <th>Manage</th>
                </tr>
            </tfoot>
            </table>
        </div>

    </div>
</div>
</div>
@endsection

