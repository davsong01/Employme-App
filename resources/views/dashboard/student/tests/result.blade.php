
@extends('dashboard.student.trainingsindex')
@section('title', 'My Results')
@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>My Completed Tests <br><br> (Please note that Pre- Class Test results are NOT collated in the final results)</h4><br>
                @include('layouts.partials.alerts')
            </div>
        </div>
    </div>

    @if($results->count() < 1)
            <div>
                <h5 style="color:red">No completed tests Yet! Go back and take a Post Class Test</h5>
            </div> 
        </>
        @else   
         <div class="row"> 
            <div class="col-md-12">
                <h5> <strong>OVERALL TEST RESULTS</strong></h5>
            </div>
        </div>
        @if($hasmock == 1)
        <div class="row">
            @foreach($results as $result)
            @foreach($mock_results as $mock_result)  
            @if($result->module->id ==  $mock_result->module->id)      
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                           
                        <h5 class="font-light text-white"> <b>Training: </b>  {{ $program->p_name }}</h5>
                        <h5 class="font-light text-white"> <b>Module: </b>{{ $result->module->title}}</h5>
                            <h4 class="text-white">Test Type: {{ $result->module->type }} </h4>
                            <p class="text-white" style="font-weight: bold">Post Class Test Score: 
                                @if($result->module->type == 'Class Test')
                                    {{$result->class_test_score .'/'.$result->module->noofquestions}}
                                @endif
                                @if($result->module->type == 'Certification Test')
                                    {{isset($result->certification_test_score) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'}}
                                @endif
                            </p>
                            <p class="text-white" style="font-style:italic">Pre Class Test Score: 
                                @if($mock_result->module->type == 'Class Test')
                                    {{$mock_result->class_test_score .'/'.$mock_result->module->noofquestions}}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div> 
            @endif  
            @endforeach
            @endforeach
       
       
            @foreach($results as $result)
            @if($result->module->type == 'Certification Test')      
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                           
                        <h5 class="font-light text-white"> <b>Training: </b>  {{ $program->p_name }}</h5>
                        <h5 class="font-light text-white"> <b>Module: </b>{{ $result->module->title}}</h5>
                            <h4 class="text-white">Test Type: {{ $result->module->type }} </h4>
                            <p class="text-white" style="font-weight: bold">Post Class Test Score: 
                                @if($result->module->type == 'Certification Test')
                                    {{isset($result->certification_test_score) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'}}
                                @endif
                                 <p class="text-white" style="font-style:italic">&nbsp </p>
                        </div>
                    </div>
                </div>
            </div> 
            @endif  
            @endforeach
    
        </div>
        @endif
        @if($hasmock == 0)
        <div class="row">
            @foreach($results as $result)          
            <div class="col-md-4 col-lg-4">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-list-alt"></i></h1>
                        <div class="card-title">
                           
                            <h5 class="font-light text-white"> <b>Training: </b>  {{ $program->p_name }}</h5>
                        <h5 class="font-light text-white"> <b>Module: </b>{{ $result->module->title}}</h5>
                            <h4 class="text-white">Test Type: {{ $result->module->type }} </h4>
                            <b class="text-white">My Score: 
                                @if($result->module->type == 'Class Test')
                                    {{$result->class_test_score .'/'.$result->module->noofquestions}}
                                @endif
                                @if($result->module->type == 'Certification Test')
                                    {{isset($result->certification_test_score) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'}}
                                @endif</b>
                        </div>
                    </div>
                </div>
            </div>   
            @endforeach
        </div>
        @endif
    @endif
    @endsection