{{-- <html>
	<head><meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
		<title></title>
		
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" rel="stylesheet" /><script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script><script src='https://code.jquery.com/jquery-1.11.1.min.js'></script>
	</head>
<!------ Include the above in your HEAD tag ---------->
<body>
<div class="container">
<div>
<div style="float:left"><img src="{{ asset('assets/images/elearninglogo.jpg') }}" style="width:50%" /></div>

<div style="float:right">
<h4></h4>

<h4><strong><span style="font-size:36px;">E - RECEIPT</span></strong></h4>

<p></p>

<p><b style="color:red !important">INVOICE ID: {{ $details['invoice_id']}} </b></p>

<p><em>Date: {{ date('Y:m:d') }}</em></p>
</div>

<div class="row">
<div class="col-4"></div>
</div>

<div class="row">&nbsp;
<div class="col-8"><strong>School Address: </strong>School Address:IFECHUKWU HOUSE<br />
Plot 87A Mustapha Azeeza Close, Off Alakoso Road<br />
<small>(ABC Transport Terminal Axis) Amuwo-Odofin Industrial Area</small><br /><br />
</div>
</div>

<div class="row">
<p><b style="color:red">CLIENT</b><br />
{{ $data['name']}}</p>

<p><b style="color:red">CONTACT EMAIL</b><br />
{{ $data['email']}}</p>
</div>

<div class="row">
<table class="table table-hover">
	<thead>
		<tr>
			<th>Program</th>
			<th>Payment Mode</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="col-md-10"><em style="color:red !important">{{ $details['programName']}} </em></td>
			<td class="col-md-2" style="color:red !important">{{ $data['bank']}}</td>
		</tr>
	</tbody>
</table>
&nbsp;

<table class="table table-bordered">
	<thead>
		<tr>
			<th>S/N</th>
			<th>DESCRIPTION</th>
			<th>FEE</th>
			<th>AMOUNT PAID</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="col-md-1" style="text-align: center">1</td>
			<td class="col-md-8">{{ $details['programName']}}<br />
			<small><i>({{ $details['message']}})</i></small></td>
			<td class="col-md-1 text-center"><strike>N</strike>{{ $details['programFee']}}</td>
			<td class="col-md-2 text-center"><strike>N</strike>{{ $data['amount'] }}</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td class="text-right">
			<p><strong>Total:&nbsp;</strong></p>
			</td>
			<td class="text-center">
			<p><strong><strike>N</strike>{{ $data['amount'] }}</strong></p>
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td class="text-right">
			<h4><strong style="color:red !important">Balance:&nbsp;</strong></h4>
			</td>
			<td class="text-center">
			<h4><strong style="color:red !important"><strike>N</strike>{{ $details['balance'] }}</strong></h4>
			</td>
		</tr>
	</tbody>
</table>
<div>
		<h5 style='font-style: italic;'><span style='color:#FF0000;'><strong>Please carefully read our Terms and Conditions!</strong></span></h5>
		</div>
		<br />
<div style="float-left; padding-left: 20px;"><img alt="signature" src="{{ asset('assets/images/sign.png') }}" style="width:8%" />
<p><b><i>School Administrator</i></b></p>
</div>
</div>

</div>
</div>
</body>
</html> --}}