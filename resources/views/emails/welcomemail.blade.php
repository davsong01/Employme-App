<?php
    $logo = \App\Models\Settings::first()->value('logo');
    $currency = \Session::get('currency');
    $currency_symbol = \Session::get('currency_symbol');
    $exchange_rate = \Session::get('exchange_rate');
?>
@component('mail::message')
<strong>Dear {{ $data['name'] }}</strong>,

@if(isset($data['type']) && $data['type'] == 'balance')
<div>
<p style="text-align:justify !important">Your balance payment of {{ $data['currency_symbol'] }}{{ $data['amount'] }} for {{ $data['programName'] }} has been received.<br><br>You can now access all sections of your portal!</p>
</div>
@else 
<span style="text-align:justify !important">Your {{ $data['message'] }} of {{  $currency_symbol.$data['amount'] }} for the {{ $data['programName'] }} ({{ $data['programAbbr'] }}) via {{ $data['t_type'] }} has been received. <br><br></span>
<span><strong style="color:red">NOTE: </strong>Attached to this email are your E-receipt, booking form (if available) and feedback form(if available) which you are to print and bring along with you to the training center (NOT APPLICABLE FOR OUR ONLINE TRAININGS).</strong> <br><br></span>
<span>Your customized portal is where you can view/download study materials for this training, view your payment history and do much more. <br><br></span>
<span><strong>Your customized portal login details are:</strong> <br><br>
Username: {{ $data['email'] }} <br>
Password: 12345 <small> <strong>(Use existing password if you are a returning participant)</strong> </small>
</span>

@component('mail::button', ['url' =>  config('app.url').'/login'])
Login to your Portal here
@endcomponent

</div>
@endif

Accept our warm regards.<br><br>
<b> {{ \App\Models\Settings::select('program_coordinator')->first()->value('program_coordinator') }}</b><br>
Program Coordinator
@endcomponent