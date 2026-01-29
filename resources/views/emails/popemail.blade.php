@component('mail::message')

<strong>Dear Admin</strong>,

<p>Please find below proof of payment details with file attached </p>

Name: {{ $data['name'] }}

Email: {{ $data['email'] }}

Phone: {{ $data['phone'] }}

Bank: {{ $data['bank'] }}

Amount: {{ $data['amount'] }}

Training: {{ $data['training'] }}

Date of Payment: {{ $data['date'] }}

@if(isset($data['location']))
Location: {{ $data['location'] }}
@endif

@if(isset($data['training_mode']))
Training Mode: {{ $data['training_mode'] }}
@endif

@component('mail::button', ['url' => config('app.url').'/login'])
Login to confirm Participant<br>
@endcomponent

Regards
@endcomponent