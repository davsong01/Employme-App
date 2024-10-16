@extends('dashboard.admin.index')
@section('title', 'Download materials')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            <h4 style="padding:10px">Click a Training below to manage its materials</h4>
            @include('layouts.partials.alerts')
        </div>
        <div class="card-body">
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Materials</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilitator_programs as $facilitator_program)
                            <tr>
                                <td>{{  $i++ }}</td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view materials for this program"
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
</div>
@endsection