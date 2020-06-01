@extends('dashboard.student.index')
@section('title', 'My Results')
@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>My Completed Tests</h4>
                @include('layouts.partials.alerts')
            </div>
            @if($results->count() < 1)
            <div>
                <h5>No completed tests Yet!</h5>
            </div> 
        @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
        </div>
        <!-- Column -->
       
        @foreach($results as $result)
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                            <h5 class="font-light text-white"> <b>Training: </b> {{Auth::user()->program->p_name }}</h5>
                        <h5 class="font-light text-white"> <b>Module: </b>{{ $result->module->title}}</h5>
                            <h4 class="text-white">Test Type: {{ $result->module->type }} </h4>
                            <b class="text-white">My Score: 
                                @if($result->module->type == 'Class Test')
                                    {{$result->class_test_score .'/'.$result->module->noofquestions}}
                                @endif
                                @if($result->module->type == 'Certification Test')
                                    {{isset($result->certification_test_score) ? $result->certification_test_score.'/'.Auth::user()->program->scoresettings->certification : 'Processing'}}
                                @endif</b>
                        </div>
                    </div>
                </div>
            </div>        
        @endforeach
    </div>
</div>
@endsection