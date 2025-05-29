<html>
<head>
    <meta charset="utf-8">
    <title>E-Receipt</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .receipt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .receipt-header img { max-width: 180px; }
        .receipt-title { text-align: right; }
        .receipt-title h4 { margin: 0; }
        .customer-details p { margin: 5px 0; }
        .table td, .table th { vertical-align: middle !important; }
        .coupon-line { font-size: 11px; color: #666; font-style: italic; }
    </style>
</head>
<body>
<div class="container">
    <div class="receipt-header">
        <div><img src="{{ asset('assets/images/logo-text.png') }}" alt="Logo"></div>
        <div class="receipt-title">
            <h4><strong><span style="font-size:28px;">E - RECEIPT</span></strong></h4>
            <p><b style="color:blue">TRANSACTION ID:</b><br><span style="color:green; font-size:16px;">{{ $allData['transid'] }}</span></p>
            @php $date = $allData['created_at'] ?? now(); @endphp
            <p><em>{{ \Carbon\Carbon::parse($date)->format('jS F, Y, h:iA') }}</em></p>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12"><strong>School Address:</strong> {!! \App\Models\Settings::value('ADDRESS_ON_RECEIPT') !!}</div>
    </div>

    <br>
    <div class="row customer-details">
        <div class="col-sm-4">
            <p><b style="color:red">NAME</b><br>{{ $allData['participant_name'] }}</p>
        </div>
        <div class="col-sm-4">
            <p><b style="color:red">EMAIL ADDRESS</b><br>{{ $allData['participant_email'] }}</p>
        </div>
        <div class="col-sm-4">
            <p><b style="color:red">PHONE</b><br>{{ $allData['participant_phone'] }}</p>
        </div>
    </div>

    <br>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice ID</th>
                <th>Program</th>
                <th>Payment Mode</th>
                <th>Amount Due</th>
                <th>Amount Paid</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $totalBalance = 0;
                $totalProgramFee = 0;
                $currency = $allDetails['paymentModeDetails']['currency_symbol'] ?? '₦';
            @endphp
            @foreach($allDetails as $index => $data)
                @php
                    $amount = $data['amountDetails']['amount_paid'] ?? 0;
                    $fee = $data['programFee'] ?? 0;
                    $balance = $data['amountDetails']['balance'];
                    $grandTotal += $amount;
                    $totalProgramFee += $data['programFee'];
                    $totalBalance += $balance;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $data['invoice_id'] }}</td>
                    <td>
                        <strong>{{ $data['programName'] ?? '-' }}</strong><br>
                        @if(!empty($data['couponData']))
                            <span class="coupon-line">
                                Coupon applied: {{ $data['couponData']['coupon_code'] ?? '' }}, Value:  {{  $currency.$data['couponData']['coupon_amount'] ?? '' }}
                                {{-- {{ $data['coupon']['discount'] ?? '0' }}{{ $data['coupon']['type'] === 'percentage' ? '%' : $currency }} --}}
                            </span>
                        @endif
                    </td>
                    <td>{{ $data['t_type'] ?? '-' }}</td>
                    <td class="text-right">{{ $currency }}{{ number_format($fee) }}</td>
                    <td class="text-right">{{ $currency }}{{ number_format($amount) }}</td>
                    <td class="text-right">{{ $currency }}{{ number_format($balance) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                <td class="text-right"><strong>{{ $currency }}{{ number_format($totalProgramFee) }}</strong></td>
                <td class="text-right"><strong>{{ $currency }}{{ number_format($grandTotal) }}</strong></td>
                <td class="text-right"><strong>{{ $currency }}{{ number_format($totalBalance) }}</strong></td>
            </tr>
            {{-- {{dd($data)}} --}}

        </tbody>
    </table>
    

    <br>
    <p><b><i>School Administrator</i></b></p>
</div>
</body>
</html>
