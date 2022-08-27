@extends('dashboard.admin.index')
@section('title', 'My Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">My trainings</h5> 
                </div>
            </div>
            <div>
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $user->p_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>

@endsection