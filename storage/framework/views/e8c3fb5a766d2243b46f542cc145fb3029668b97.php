<html>
	<head><meta http-equiv="Content-Type" content="text/html; charset=us-ascii">
		<title></title>
		
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" rel="stylesheet" /><script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script><script src='https://code.jquery.com/jquery-1.11.1.min.js'></script>
	</head>

<body>
<div class="container">

	<div style="float:left"><img src="<?php echo e(asset('assets/images/logo-text.png')); ?>" style="width:100%" /></div>

	<div style="float:right">
		<h4></h4>

		<h4><strong><span style="font-size:36px;">E - RECEIPT</span></strong></h4>

		<p></p>
		<p><b style="color:blue !important">TRANSACTION ID: <br> <span style="color:green !important;font-size: 16px;"><?php echo e($data['transid']); ?></span>  </b></p>
		<p><b style="color:blue !important">INVOICE ID: <br> <span style="color:green !important;font-size: 16px;"><?php echo e($data['invoice_id']); ?></span> </b></p>
		<?php 
		$date =  $data['created_at'] ?? now()
		?>
		<p><em><?php echo e(\Carbon\Carbon::parse($date)->format('jS F, Y, h:iA')); ?></em></p>
		
	</div>

	<div class="row">
		<div class="col-4"></div>
	</div>

	<div class="row">&nbsp;
		<div class="col-8"><strong>School Address: </strong><?php echo \App\Settings::select('ADDRESS_ON_RECEIPT')->first()->value('ADDRESS_ON_RECEIPT'); ?>

		</div>
	</div>

	<div class="row">
		<p><b style="color:red">PARTICIPANT</b><br />
		<?php echo e($data['name']); ?></p>

		<p><b style="color:red">CONTACT EMAIL</b><br />
		<?php echo e($data['email']); ?></p>
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
					<td class="col-md-10"><em style="color:red !important"><?php echo e($data['programName']); ?> </em></td>
					<td class="col-md-2" style="color:red !important"><?php echo e($data['t_type'] ?? null); ?></td>
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
					<td class="col-md-8"><?php echo e($data['programName']); ?><br />
					<small><i>(<?php echo e($data['message']); ?>)</i></small></td>
					<td class="col-md-1 text-center"><?php echo e($data['currency'] ?? null); ?><?php echo e(number_format($data['programFee'])); ?></td>
					<td class="col-md-2 text-center"><?php echo e($data['currency'] ?? null); ?><?php echo e(number_format($data['amount'])); ?></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="text-right">
					<p><strong>Total:&nbsp;</strong></p>
					</td>
					<td class="text-center">
					<p><strong><?php echo e($data['currency']?? null); ?><?php echo e(number_format($data['amount'])); ?></strong></p>
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="text-right">
					<h4><strong style="color:red !important">Balance:&nbsp;</strong></h4>
					</td>
					<td class="text-center">
					<h4><strong style="color:red !important"><?php echo e($data['currency'] ?? null); ?><?php echo e(number_format($data['balance'])); ?></strong></h4>
					</td>
				</tr>
			</tbody>
		</table>
	<div>
			<h5 style='font-style: italic;'><span style='color:#FF0000;'></span></h5>
			</div>
			<br />
			<p><b><i>School Administrator</i></b></p>
	</div>
</div>

</body>
</html><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/emails/receipt.blade.php ENDPATH**/ ?>