@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' Test Management' }}
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Select a Training to manage its modules. Trainings which have no modules will not appear here </h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Training</th>
                            <th>Modules</th> 
                            <th>Questions</th>                           
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($programs_with_modules as $programs)
                         {{-- @if($programs->modules_count >0) --}}
                            <tr>
                                <td>{{  $i++ }}</td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view modules for this training"
                                    class="btn btn-info" href="{{ route( 'facilitatormodules', ['p_id'=>$programs->program_id] ) }}">
                                    {{ $programs->p_name }}
                                </a>
                                </td>
                                <td>{{ $programs->modules_count}}</td>
                                <td>{{ $programs->questions_count}}</td>
                            </tr>
                            {{-- @endif --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection