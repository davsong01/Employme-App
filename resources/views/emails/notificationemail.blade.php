@component('mail::message')
<strong>Dear {{ $data['name'] }}</strong>, <br>

@if(isset($data['type']) && $data['type'] == 'notify_facilitator')
You have been selected as a facilitator for <b>{{ $data['program_name'] }}</b> by user with the following details: <br><br>
<b>Name: </b>{{ $data['name'] }} <br>
<b>Student name: </b>{{ $data['student_name'] }} <br>
<b>Student email: </b>{{ $data['student_email'] }} <br>
<b>Student phone: </b>{{ $data['student_phone'] }} <br>
<b>Training: </b>{{ $data['program_name'] }} <br>
<b>Date: </b>{{ $data['date'] }} <br> <br>

@component('mail::button', ['url' => config('app.url')])
Login to your Portal to view and manage your students<br><br>
@endcomponent
@endif

Accept our warm regards.<br><br>
<b> {{ \App\Settings::select('program_coordinator')->first()->value('program_coordinator') }}</b><br>
Program Coordinator
@endcomponent