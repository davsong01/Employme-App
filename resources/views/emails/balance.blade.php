@component('mail::message')

#<strong>Dear {{ $details['name'] }}</strong>,
 
<p style="text-align:justify !important">Your balance payment of ₦{{ $details['amount'] }} for the {{ $details['programName'] }} ({{ $details['programAbbr'] }}) via {{ $details['bank'] }} has been received. <br><br>

<strong style="color:red">NOTE: </strong>Please find your E-receipt attached to this email.</strong> <br><br>


@component('mail::button', ['url' => config('app.url')])
Login to your Portal to continue learning<br><br>
@endcomponent

Accept our warm regards.<br><br>
Program Coordinator
@endcomponent