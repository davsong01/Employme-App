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
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Earning</th>
                            <th>currency</th>
                            <th>Date</th>
                            <th>Training</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td><img src="{{ asset('profiles/'. $user->profile_picture  )}}" alt="{{ $user->profile_picture }}" class="rounded-circle" width="50"
                                height="50"></td>
                            <td>{{ $user->name ?? NULL }}</td>
                            <td>{{ $user->email ?? NULL }}</td>
                            <td>{{ $user->t_phone ?? NULL}}</td>
                            {{-- <td>{{ \App\Settings::first()->value('DEFAULT_CURRENCY') }}{{ number_format($user->t_amount) }}</td> --}}
                            <td>{{ \App\Settings::first()->value('DEFAULT_CURRENCY') }}{{ $user->facilitator_earning }}</td>
                            <td>{{ $user->currency ?? NULL}}</td>
                            <td>{{ $user->created_at}}</td>
                            <td>{{ $user->p_name ?? NULL}}</td>
                            @endforeach
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>
@endsection