@extends('dashboard.student.trainingsindex')
@section('title', 'Pre class Tests')
@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>Pre Class Tests</h4>
                <h6 style="color:red">Please read the following carefully before you proceed to take a test</h6>
                <div>
                    <ul>
                        <li>All Tests are timed, if you run out of time, the test gets submitted automatically with all
                            answered questions</li>
                        <li>Tests with Type: <strong>Certification</strong> are open ended tests, you will be required
                            to type in your input</li>
                        <li>Tests with Type: <strong>Class Test</strong> are multiple choice tests, you will be required
                            to select the correct option</li>
                        <li>When a test ends, your answers will be saved and will be available after Post Class Tests</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
                <h5>All Pre Class Tests</h5>
            </div>
            @if($modules->count() < 1)
            <div>
                <h6 style="color:red">No Pre Tests Available yet! Check back later</h6>
            </div>
            @endif
        </div>
        @foreach($modules as $module)
            <div class="col-md-4 col-lg-4">
                <div class="card" style="background-color: #eeeeee; margin-bottom:5px">
                    <div class="box bg-white text-center"
                        style="color:indigo !important; border: indigo 1px solid;  border-radius: 5px;">
                        <h1 class="font-light text-blue"><i class=" fa fa-list-alt"></i></h1>
                        <div class="card-title">
                            {{ $module->title }}
                        </div>
                        <h6 class="text-blue">Type: {{ $module->type }} </h6>
                        <p class="text-blue">No of Questions: {{ $module->questions->count() }} </p>
                        <p class="text-blue">Time: {{ $module->time }}minutes </p>
                        @if($module->completed == 0)
                            <a href="{{route('mocks.show', ['mock' => $module->id, 'p_id' => $program->id])}}"><button style="width:100%" type="button" class="btn btn-outline-info" onclick="return confirm('I have read the instructions above?');">Start Now!</button></a>
                        @endif
                        @if($module->completed == 1)
                            <a><button style="width:100%" type="button" class="btn btn-outline-success disabled"><b>Pre Test Completed!</b></button></a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

    </div>

</div>
@endsection