@extends('dashboard.admin.index')
@section('title')
{{ config('app.name') }} Test Management
@endsection
@section('content')

<div class="container-fluid">
     <div class="card">
        <div class="card-body">
           
            <div class="card-header">
                <div>
                    @include('layouts.partials.alerts')
                    <p style="color:red"><strong>Here you can set score parameters for individual training assesment. Please use the default values except you know what you are doing.</strong> <br><br>
                    NOTE: You cannot edit assessment parameters for a program whose Modules have been enabled. <br>Total Scores for all assessment parameter for an individual training cannot be more than 100%</p>
                    <h5 class="card-title"> All Questions <a href="{{route('scoreSettings.create')}}"><button type="button" class="btn btn-outline-primary">Add New Assessment Parameter</button></a></h5> 
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Training</th>
                            <th>Enabled Modules</th>
                            <th>Class Tests</th> 
                            <th>Role Play Score</th>
                            <th>Email Score</th>
                            <th>Certification Score</th>
                            <th>Total</th>
                             <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scores as $score)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $score->program->p_name}}</td>
                            <td>{{$score->module_status_count}}</td>
                            <td>{{ $score->class_test }}</td>
                            <td>{{ $score->role_play }}</td>
                            <td>{{ $score->email }}</td>
                            <td>{{ $score->certification }}</td>
                            <td>{{ $score->total}}</td>
                            <td>
                                <div class="btn-group">
                                  

                                    <form action="{{ route('scoreSettings.destroy', $score->id) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to Delete forever?');">
                                        
                                        @if($score->module_status_count <= 0)
                                        <a data-toggle="tooltip" data-placement="top" title="Edit"
                                            class="btn btn-info" href="{{ route('scoreSettings.edit', $score->id)}}"><i
                                                class="fa fa-edit"></i>
                                        </a>
                                        @endif
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}
                                        @if($score->module_status_count <= 0)
                                        <button type="submit" class="btn btn-warning" data-toggle="tooltip"
                                            data-placement="top" title="Delete scores"> <i class="fa fa-trash"></i>
                                        </button>
                                        @else N/A
                                        @endif
            
                                        
                                    </form>
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

<script>
    $(".delete").on("submit", function () {
        return confirm("Are you sure?");
    });
</script>
@endsection