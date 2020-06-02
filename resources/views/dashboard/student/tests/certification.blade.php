@extends('dashboard.student.index')
@section('title', 'My Tests')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="header" id="myHeader">
            <h4>
                <div style="font-weight: bold;" id="quiz-time-left"></div>
            </h4>

        </div>

        <div class="content">
            @include('layouts.partials.alerts')
            <p><strong>Training: {{ $program }}</strong><br>
                <strong>Module: {{ $module_title }}</strong>
                Select the correct answer
            </p>
            <form name="quiz" id="quiz_form" action="{{route('tests.store')}}" method="POST" class="pb-2">
                {{ csrf_field() }}

                @foreach($questions as $question)
                <div class="form-group">
                    <label for="title">{{ $i ++ .'. ' }}{{ $question->title }}</label><br>

                    <label for="{{ $question->id}}">Your answer</label><br>
                    <div class="form-group">
                    <textarea style="max-width: 100%;" name="{{ $question->id}}" id="{{ $question->id}}" rows="10" cols="100"
                        placeholder="Enter your answer for question {{ $i - 1 }} here"></textarea>
                    </div>
                    <input type="hidden" name="id" value="{{$question->id}}">
                    <input type="hidden" name="mod_id" value="{{$question->module->id}}">
                </div>
                @endforeach
                
                <div class="row">
                    <button type="submit" class="btn btn-primary" style="width:100%">
                        Submit
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        window.onscroll = function () {
            myFunction()
        };

        var header = document.getElementById("myHeader");
        var sticky = header.offsetTop;

        function myFunction() {
            if (window.pageYOffset > sticky) {
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }
        }

        var max_time = {{$time}};
        var c_seconds = 0;
        var total_seconds = 60 * max_time;
        max_time = parseInt(total_seconds / 60);
        c_seconds = parseInt(total_seconds % 60);
        document.getElementById("quiz-time-left").innerHTML = 'Time Left: ' + max_time + ' minutes ' + c_seconds +
            ' seconds';

        function init() {
            document.getElementById("quiz-time-left").innerHTML = 'Time Left: ' + max_time + ' minutes ' + c_seconds +
                ' seconds';
            setTimeout("CheckTime()", 999);
        }

        function CheckTime() {
            document.getElementById("quiz-time-left").innerHTML = 'Time Left: ' + max_time + ' minutes ' + c_seconds +
                ' seconds';
            if (total_seconds <= 0) {
                setTimeout('document.quiz.submit()', 1);

            } else {
                total_seconds = total_seconds - 1;
                max_time = parseInt(total_seconds / 60);
                c_seconds = parseInt(total_seconds % 60);
                setTimeout("CheckTime()", 999);
            }

        }


        init();
    </script>

    @endsection