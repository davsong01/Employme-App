@extends('dashboard.admin.index')
@section('title', 'CRM')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <h5 class="card-title" style="color:green"> Click the eye icon to Select CRM for respective trainings </h5><br>
                @include('layouts.partials.alerts')
            </div>
           
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Program Title</th>
                            <th>CRM Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainings as $training)
                        <?php
                            // $permissionsToCheck = ['material.training.select'];
                            // $permissions = checkTrainingHasPermissions($training->id, $permissionsToCheck);
                        ?>
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $training->p_name }}</td>
                            <td>{{ $training->crm_count }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($training->hascrm == 1)
                                    <a class="btn btn-info" href="{{ route('complain.program.select', ['p_id' => $training->id])}}"><i class="fa fa-eye"></i> View
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