@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' Test Management' }}
@endsection
@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
        <a href="{{ route('modules.index')}}">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> {{$modules->count()}} Module(s)</h6>
                </div>
            </div>
        </a>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
        <a href="{{ route('questions.index')}}">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                <h6 class="text-white"><b></b> {{ $questions_count }} Questions</h6>
                </div>
            </div>
        </a>
        </div>
        <div class="col-md-3 col-lg-3">
            <a href="{{ route('results.index')}}">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fas fa-user-graduate"></i></h1>
                    <h6 class="text-white"><b></b> Grades </h6>
                    </div>
                </div>
            </a>
            </div>
        @if(Auth()->user()->role_id == "Admin")
        <div class="col-md-3 col-lg-3">
        <a href="{{ route('scoreSettings.index')}}">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-cog"></i></h1>
                <h6 class="text-white"><b></b> Score Settings </h6>
                </div>
            </div>
        </a>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Select a Training to manage its modules </h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Training</th>
                            <th>Modules</th> 
                            <th>Questions</th>                           
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($programs_with_modules as $programs)
                            <tr>
                                <td>{{  $i++ }}</td>
                                
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view modules for this training"
                                    class="btn btn-info" href="{{ route( 'facilitatormodules', ['p_id'=>$programs->id] ) }}">
                                    {{ $programs->p_name }}
                                </a>
                                </td>
                                <td>{{ $programs->modules->count() }}</td>
                                <td>{{ $programs->questions->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection