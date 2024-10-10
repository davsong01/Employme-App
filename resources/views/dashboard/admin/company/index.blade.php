@extends('dashboard.admin.index')
@section('title', 'All Company Users')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">Company Admins <a href="{{route('companyuser.create')}}"><button type="button" class="btn btn-outline-primary">Add New</button></a></h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Trainings</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>
                                <small>
                                <strong>Name: </strong> {{ $user->name }}<br>
                                <strong>Email: </strong> {{ $user->email }}<br>
                                <strong>Phone: </strong> {{ $user->phone }}<br>
                            </td>
                            <td>
                                <small>
                                    {{-- @foreach($user->p_names as $index=>$names)
                                    <strong style="color:red">@if($index < count($user->p_names))|| @endif</strong>{{ $names }} @if($index < count($user->p_names)-1)<br>@endif
                                    @endforeach --}}
                                </small>
                            </td>
                            <td>
                                @if($user->status == 'active') <button class="btn btn-success btn-xs">Active</button> @else <button class="btn btn-danger btn-xs">Inactive</button> @endif
                            </td>
                            <td>{{$user->created_at }}</td>
                            
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit"
                                        class="btn btn-info" href="{{ route('companyuser.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>                                   
                                    {{-- <a data-toggle="tooltip" data-placement="top" title="Peep"
                                    class="btn btn-warning" href="{{ route('impersonate', $user->id) }}"><i
                                        class="fa fa-unlock"></i>
                                    </a> --}}
                                    <form action="{{ route('companyuser.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete"> <i class="fa fa-trash"></i>
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