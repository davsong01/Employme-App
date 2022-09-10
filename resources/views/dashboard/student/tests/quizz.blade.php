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
            <p><strong>Training: {{ $program_name }}</strong><br>
                <strong>Module: {{ $module_title }}</strong><br><br>
                Select the correct answer
            </p>
            <form name="quiz" id="quiz_form"
                action="{{ route('tests.store', ['p_id' => $program->id]) }}"
                method="POST" class="pb-2">
                {{ csrf_field() }}
                @foreach($questions as $question)
                    <div class="form-group">
                        <label for="name">{{ $i ++ .'. ' }}{{ $question->title }}

                        </label><br>
                        <input type="radio" id="{{ $question->id }}{{ $question->optionA }}"
                            name="{{ $question->id }}" value="A" required>
                        <label
                            for="{{ $question->id }}{{ $question->optionA }}">{{ $question->optionA }}</label><br>

                        <input type="radio" id="{{ $question->id }}{{ $question->optionB }}"
                            name="{{ $question->id }}" value="B" required>
                        <label
                            for="{{ $question->id }}{{ $question->optionB }}">{{ $question->optionB }}</label><br>

                        <input type="radio" id="{{ $question->id }}{{ $question->optionC }}"
                            name="{{ $question->id }}" value="C" required>
                        <label
                            for="{{ $question->id }}{{ $question->optionC }}">{{ $question->optionC }}</label><br>

                        <input type="radio" id="{{ $question->id }}{{ $question->optionD }}"
                            name="{{ $question->id }}" value="D" required>
                        <label
                            for="{{ $question->id }}{{ $question->optionD }}">{{ $question->optionD }}</label><br>

                        <input type="hidden" name="id" value="{{ $question->id }}">
                        <input type="hidden" name="mod_id" value="{{ $question->module->id }}">
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

        var max_time = {
            {
                % 24 time
            }
        };
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
</div>
@endsection