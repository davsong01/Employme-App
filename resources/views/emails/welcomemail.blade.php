@component('mail::message')

#<strong>Dear {{ $data['name'] }}</strong>,
 
<p style="text-align:justify !important">Your {{ $details['message'] }} of ₦{{ $data['amount'] }} for the {{ $details['programName'] }} ({{ $details['programAbbr'] }}) via {{ $data['bank'] }} has been received. <br><br>

<strong style="color:red">NOTE: </strong>Attached to this email are your E-receipt, booking form and feedback form which you are to print and bring along with you to the training center (NOT APPLICABLE FOR OUR ONLINE TRAININGS).</strong> <br><br>

Your customized portal is where you can view/download study materials for this training, view your payment history and do much more. <br><br>

<strong>Your customized portal login details are:</strong> <br><br>
Username: {{ $data['email'] }} <br>
Password: 12345 <small> <strong>(Use existing password if you are a returning participant)</strong> </small> 
</p>

@component('mail::button', ['url' => config('app.url')])
Login to your Portal here<br><br>
@endcomponent

Accept our warm regards.<br><br>
<b>Chris Anozie</b><br>
Program Coordinator
@endcomponent