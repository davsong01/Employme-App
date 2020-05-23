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
            <h4>- Do not leave this tab, do not refresh page until you are done</h4>
            <p><strong>Training: {{ $program }}</strong><br>
                <strong>Module: {{ $module_title }}</strong><br><br>
                Select the correct answer
            </p>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <form name="quiz" id="quiz_form" action="{{route('tests.store')}}" method="POST" class="pb-2">
                            {{ csrf_field() }}
                            @foreach($questions as $question)
                            <div class="form-group">
                                <label for="name">{{ $i ++ .'. ' }}{{ $question->title }}

                                </label><br>
                                <input type="radio" id="answer{{ $question->id}}" name="answer{{ $question->id}}"
                                    value="A">
                                <label for="answer">{{ $question->optionA }}</label><br>

                                <input type="radio" id="answer{{ $question->id}}" name="answer{{ $question->id}}"
                                    value="B">
                                <label for="answer{{ $question->id}}">{{ $question->optionB }}</label><br>

                                <input type="radio" id="answer{{ $question->id}}" name="answer{{ $question->id}}"
                                    value="C">
                                <label for="answer{{ $question->id}}">{{ $question->optionC }}</label><br>

                                <input type="radio" id="answer{{ $question->id}}" name="answer{{ $question->id}}"
                                    value="D">
                                <label for="answer{{ $question->id}}">{{ $question->optionD }}</label><br>

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
            </div>

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

        var max_time = 1;
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


        //Submit the form if tab changed
        $(window).focus(function () {
            alert("You left the browser, test will now auto-submit!");
            //document.quiz.submit();
            // setTimeout('document.quiz.submit()', 1);
        });

        window.onbeforeunload = function () {
            alert("You refreshed the browser, test will now auto-submit!");
            //document.quiz.submit();

            setTimeout('document.quiz.submit()', 1);

            function finishpage() {
                alert("unload event detected!");
                //document.quiz.submit();

            }

        }

        init();
    </script>

    @endsection