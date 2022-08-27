@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                 <h5 class="card-title" style="color:red"> Click the eye icon to View pre test for respective trainings </h5><br>
                 <p>Trainings that do not have Pre Class Tests enabled do not show up here</p>
                @include('layouts.partials.alerts')
             </div>
           
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Program Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teacher_programs as $program)
                        @if(isset($program->p_name))
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $program->p_name }}</td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="View Grades"
                                        class="btn btn-info" href="{{ route('mocks.getgrades', $program->program_id)}}"><i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection