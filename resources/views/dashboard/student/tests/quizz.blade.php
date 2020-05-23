@extends('dashboard.student.index')
@section('title', 'Employme Test Management')
@section('content')

<div class="row">
    <div style="width:100%; background:red" class="timer" id="mytimer">
        <div style="font-weight: bold" id="quiz-time-left"></div>
        <div><p>- Do not leave this tab, do not refresh page until you are done</p></div>
    </div>
</div>

@endsection