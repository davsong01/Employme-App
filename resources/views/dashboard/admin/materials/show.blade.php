@extends('dashboard.admin.index')
@section('title', 'Download materials')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
         </div>
        
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Materials</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilitator_programs as $facilitator_program)
                            <tr>
                                <td>{{  $i++ }}</td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                    class="btn btn-info" href="{{ route( 'facilitatormaterials', ['p_id'=>$facilitator_program->program_id] ) }}">
                                    {{ $facilitator_program->p_name }}
                                </a>
                                </td>
                                <td>{{ $facilitator_program->materialCount}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection