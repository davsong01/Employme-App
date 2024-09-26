
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
                                    @if($result->module->allow_test_retake == 1 && $result->class_test_score < $result->module->noofquestions)<a onclick="return confirm('This will clear all your scores for this module. Are you sure you want to do this?');" href="{{ route('user.retake.module.test', ['module' => $result->module_id, 'p_id'=>$result->program_id])}}" style="border-radius: 10px;" class="btn btn-danger btn-sm"><i class="fas fa-redo"></i> Retake</a>@endif
                                @endif
                                @if($result->module->type == 'Certification Test')
                                    {{ ($result->certification_test_score > 0) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing' }}
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
                                    {{($result->certification_test_score > 0 ) ? $result->certification_test_score.'/'. $program->scoresettings->certification  : 'Processing'}}
                                    @if((isset($result->grader_comment) && !empty($result->grader_comment)) || ((isset($result->facilitator_comment) && !empty($result->facilitator_comment))))
                                        <br>
                                        <a style="width: auto;" href="{{ route('tests.results.comment', ['id'=>$result->id, 'p_id'=>$program->id]) }}"
                                            class="btn m-t-20 btn-info btn-block waves-effect waves-light">
                                            <i class="fa fa-eye"></i>View Comments
                                        </a>
                                    @else
                                    <p class="text-white" style="font-style:italic;padding-bottom: 40px;">&nbsp </p>
                                    @endif
                                @endif
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

    <!-- BEGIN MODAL -->
    <div class="modal" id="comments">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>Result Comments</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    @if(isset($result->grader_comment) && !empty($result->grader_comment))
                    {!! $result->grader_comment !!}
                    @endif
                    @if(isset($result->facilitator_comment) && !empty($result->facilitator_comment))
                    {!! $result->facilitator_comment !!}
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endsection