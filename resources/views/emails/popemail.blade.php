@component('mail::message')

<strong>Dear Admin</strong>,

<p>Please find below proof of payment details with file attached </p>

Name: {{ $data['name'] }}

Email: {{ $data['email'] }}

Phone: {{ $data['phone'] }}

Bank: {{ $data['bank'] }}

Amount: {{ $data['amount'] }}

Training: {{ $data['training'] }}

Location: {{ $data['location'] }}

@component('mail::button', ['url' => config('app.url').'/login'])
Login to confirm Participant<br>
@endcomponent

Regards
@endcomponent