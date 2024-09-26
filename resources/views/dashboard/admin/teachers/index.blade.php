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
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Trainings</th>
                            <th>Direct Students</th>
                            <th>Earnings</th>
                            <th>Off Season</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>
                                <img src="{{ $user->image }}" alt="avatar" class="rounded-circle" width="50" height="50">
                            </td>
                            <td>
                                <small>
                                <strong>Name: </strong> {{ $user->name }}<br>
                                <strong>Email: </strong> {{ $user->email }}<br>
                                <strong>Phone: </strong> {{ $user->t_phone }}<br>
                                
                                <strong>Assigned trainings: </strong>{{ $user->trainings->count() }} <br>
                                @if($user->license)
                                <strong style="color:green">WTN License: </strong> <span style="color:green">{{ $user->license }}</span> <br>
                                @endif
                                </small>
                            </td>
                            
                            <td style="margin: auto;display: flex;border-bottom: none;display: grid;">
                                @if(!empty(array_intersect(facilitatorRoles(), $user->role())))
                                <button class="disabled btn btn-primary btn-sm">Facilitator</button> <br>
                                @endif

                                @if(!empty(array_intersect(graderRoles(), $user->role())))
                                <button class="disabled btn btn-info btn-sm">Grader</button> <br>
                                @endif

                                @if(!empty(array_intersect(adminRoles(), $user->role())))
                                <button class="disabled btn btn-success btn-sm" style="background-color: darkblue;border-color: darkblue;">Admin</button> <br>
                                @endif
                              
                            @if($user->status == 'active') <button class="btn btn-success btn-xs">Active</button> @else <button class="btn btn-danger btn-xs">Inactive</button> @endif
                            </td>
                            <td>
                                <small>
                                    @foreach($user->p_names as $index=>$names)
                                    <strong style="color:red">@if($index < count($user->p_names))|| @endif</strong>{{ $names }} @if($index < count($user->p_names)-1)<br>@endif
                                    @endforeach
                                </small>
                            </td>
                            <td>{{ $user->students_count }} <br>
                                <a target="_blank" href="{{ route('teachers.students', $user->id) }}" class="btn btn-info btn-xs">View</a>
                            </td>
                            
                            <td>{{ $user->payment_modes->currency_symbol ?? 'NGN' }}{{ $user->earnings ? number_format($user->earnings) : 0 }} <br>
                                <a target="_blank" href="{{ route('teachers.earnings', $user->id) }}" class="btn btn-info btn-xs">View</a>
                            </td>
                          
                            <td>{{ $user->off_season_availability == 1 ? 'Yes' : 'No' }}</td>
                                          
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