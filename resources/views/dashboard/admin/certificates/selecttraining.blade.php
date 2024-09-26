@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                 <h5 class="card-title" style="color:green"> Click the eye icon to View grades for respective trainings </h5><br>
                @include('layouts.partials.alerts')
             </div>
           
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Program Title</th>
                            <th>Certificate Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $program->p_name }}</td>
                            <td>{{ $program->certificates_count }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-info" href="{{ route('program.select', $program->id)}}"><i class="fa fa-eye"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection