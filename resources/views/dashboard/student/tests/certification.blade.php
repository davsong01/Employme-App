@extends('dashboard.student.trainingsindex')
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
            <p><strong>Training: {{ $program_name}}</strong><br>
                <strong>Module: {{ $module_title }}</strong><br>
                Please Type in your answer in the text boxes under each question
            </p>
          
            <form name="quiz" id="quiz_form" action="{{route('tests.store', ['p_id' => $program->id ])}}" method="POST" class="pb-2">
                {{ csrf_field() }}

                @foreach($questions as $question)
                <div class="form-group">
                    <label for="title">{{ $i ++ .'. ' }}{!! $question->title !!}</label><br>

                    <label for="{{ $question->id}}">Your answer <strong style="color:green">( Maximum words: 500 )</strong></label><br>
                    Word Count : <span style="font-weight: 1000;" id="{{ $question->id}}">0</span>
                    <div class="form-group">
                    <textarea id= "text{{ $question->id}}" style="max-width: 100%;" name="{{ $question->id}}" id="{{ $question->id}}" rows="20" cols="100"
                        placeholder="Enter your answer for question {{ $i - 1 }} here" required></textarea>
                    </div>
                  
                    <input type="hidden" name="id" value="{{$question->id}}">
                    <input type="hidden" name="mod_id" value="{{$question->module->id}}">
                </div>
                <script>
                    $('#text{{ $question->id}}').keydown(function() {
                        var length = jQuery.trim($(this).val()).split(/\s+/).length;
                        $('#{{ $question->id}}').text(length);

                        //stop user input
                        if(length > 500){
                            $(this).prop("maxLength", 1);
                            
                        }else{
                            $(this).removeAttr("maxLength");
                        }
                    });

                </script>
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