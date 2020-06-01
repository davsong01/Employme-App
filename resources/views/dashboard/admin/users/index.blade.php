@extends('dashboard.admin.index')
@section('title', 'All Users')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Students <a href="{{route('users.create')}}"><button type="button"
                                class="btn btn-outline-primary">Add New Student</button></a> <a class="btn btn-success" href="{{ route('user.export') }}">Export User Data</a></h5>
                </div>
            </div>

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
                            <th>Bank</th>
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
                            <td>{{ $user->bank }}</td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Users"
                                        class="btn btn-info" href="{{ route('users.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                        class="btn btn-primary" href="{{ route('users.show', $user->id) }}"><i
                                            class="far fa-envelope"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
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