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
        
            <div class="responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Training Title</th>
                            <th>Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainings as $training)
                        <?php
                            $permissionsToCheck = ['view.tests'];
                            $permissions = checkTrainingHasPermissions($training->id, $permissionsToCheck);
                        ?>
                        <tr>
                            <td>{{  paginationIndex($trainings, $loop) }}</td>
                            <td>{{ $training->p_name }}</td>
                            <td>{{ number_format($training->result_count) }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($permissions['view.tests'])
                                        <a data-toggle="tooltip" data-placement="top" title="View Grades"
                                            class="btn btn-info" href="{{ route('results.getgrades', ['id' => $training->id, 'p_id' => $training->id])}}"><i class="fa fa-eye"></i>
                                        </a>
                                    @endif
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
