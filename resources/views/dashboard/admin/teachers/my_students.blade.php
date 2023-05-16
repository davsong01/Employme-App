@extends('dashboard.admin.index')
@section('title', 'My Students')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
             <div class="card-header">
                <div>
                    <h5 class="card-title">My Students</h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date Joined</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            @if(auth()->user()->role_id == 'Admin')
                            <th>Email</th>
                            @endif
                            <th>Phone</th>
                            <th>Training</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $user->created_at}}</td>
                            <td><img src="{{ asset('profiles/'. $user->profile_picture  )}}" alt="{{ $user->profile_picture }}" class="rounded-circle" width="50"
                                height="50"></td>
                            <td>{{ $user->name ?? NULL }}</td>
                            @if(auth()->user()->role_id == 'Admin')
                            <td>{{ $user->email ?? NULL }}</td>
                            @endif
                            <td>{{ $user->t_phone ?? NULL}}</td>
                            
                            <td>{{ $user->p_name ?? NULL}}</td>
                            @endforeach
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>
@endsection