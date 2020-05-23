@extends('dashboard.student.index')
@section('title', 'My Tests')
@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                <h4>All Tests</h4>
                <h6 style="color:red">Please Read the following carefully before you proceed to take a test</h6>
                <div>
                    <ul>
                        <li>All Tests are timed, if you run out of time, the test gets submitted automatically with all
                            answered questions</li>
                        <li>Tests with Type: <strong>Certification</strong> are open ended tests, you will be required
                            to type in your input</li>
                        <li>Tests with Type: <strong>Class Test</strong> are multiple choice tests, you will be required
                            to select the correct option</li>
                        <li>When a test ends, you will be redirected to the result page where you will see your score in the corresponding card</li>
                        <li>Make you sure you have a stable internet while taking a test</li>
                        <li>Do not leave the test tab, do not switch windows, do not refresh page until you are done,
                            else the test will auto-submit</li>
                        <li>It is not advisable to use mobile phones for these tests! Please use a computer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
                <h5>Available Tests</h5>
            </div>
        </div>
        <!-- Column -->
        @foreach($modules as $module)
            <div class="col-md-4 col-lg-4">
                <div class="card" style="background-color: #eeeeee; margin-bottom:5px">
                    <div class="box bg-white text-center"
                        style="color:blue !important; border: blue 1px solid;  border-radius: 5px;">
                        <h1 class="font-light text-blue"><i class=" fa fa-list-alt"></i></h1>
                        <div class="card-title">
                            {{ $module->title }}
                        </div>
                        <h6 class="text-blue">Type: {{ $module->type }} </h6>
                        <p class="text-blue">No of Questions: {{ $module->questions->count() }} </p>
                        <p class="text-blue">Time: {{ $module->time }}minutes </p>
                    <a href="{{route('tests.show', $module->id)}}"><button style="width:100%" type="button" class="btn btn-outline-primary" onclick="return confirm('I have read the instructions above?');">Start Now!</button></a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

</div>
@endsection