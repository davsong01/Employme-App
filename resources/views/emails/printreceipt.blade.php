<?php
    $logo = \App\Models\Settings::first()->value('logo');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .summary-table td {
            padding: 8px 12px;
        }

        .summary-label {
            font-weight: bold;
            background-color: #f5f5f5;
            width: 30%;
        }

        .summary-value {
            text-align: right;
        }

        .summary-total {
            font-size: 14px;
            font-weight: bold;
        }

        .summary-balance {
            color: #c0392b;
            font-weight: bold;
            font-size: 14px;
        }

        .header,
        .participant {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
<div class="header">
    <div style="float: left;">
        <a href="{{url('/')}}">
			<img src="{{ url($logo) }}" style="width: 150px;">
		</a>
    </div>

    <div style="float: right; text-align: right;">
        <h2 style="margin:0">E - RECEIPT</h2>
        <p><strong style="color:blue;">TRANSACTION ID:</strong><br><span style="color:green;">{{ $transaction->transid }}</span></p>
        <p><strong style="color:blue;">INVOICE ID:</strong><br><span style="color:green;">{{ $transaction->invoice_id }}</span></p>
        <p><em>{{ \Carbon\Carbon::parse($transaction->created_at ?? now())->format('jS F, Y, h:iA') }}</em></p>
    </div>
    <div style="clear: both;"></div>
</div>

<div class="participant">
    <p><strong style="color:red;">PARTICIPANT</strong><br>{{ $transaction->name }}</p>
    <p><strong style="color:red;">CONTACT EMAIL</strong><br>{{ $transaction->email }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>Payment Mode</th>
            <th>Exchange Rate</th>
            <th>Training Mode</th>
            <th>Training Location</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $transaction->meta['payment_mode']['name'] ?? 'N/A' }}</td>
            <td>{{ $transaction->meta['payment_mode']['exchange_rate'] ?? 'N/A' }}</td>
            <td>{{ $transaction->training_mode ?? 'N/A' }}</td>
            <td>
                @if(!empty($transaction->location))
                    {{ $transaction->location }} ({{ $transaction->location_address }})
                @else
                    N/A
                @endif
            </td>
        </tr>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2">TRAINING(S)
                @if($transaction->is_package)
                    <small style="color:blue;">({{ $transaction->group->p_name }})</small>
                @endif
            </th>
        </tr>
        <tr>
            <th style="width: 50px;">#</th>
            <th>Training Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaction->allPrograms() as $program)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $program->p_name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="summary-table">
    @php
        $meta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true);
    @endphp

    @if (!empty($meta['coupon_used']))
        <tr>
            <td class="summary-label">Coupon Used</td>
            <td class="summary-value">
                {{ $meta['coupon_used']['code'] ?? 'N/A' }}
                ({{ $transaction->currency_symbol }}{{ number_format($meta['coupon_used']['amount'] ?? 0) }} off)
            </td>
        </tr>
    @endif

    <tr>
        <td class="summary-label">Total Paid</td>
        <td class="summary-value summary-total">
            {{ $transaction->currency_symbol }}{{ number_format($transaction->total_amount_paid ?? $transaction->amount) }}
        </td>
    </tr>

    <tr>
        <td class="summary-label">Balance</td>
        <td class="summary-value summary-balance">
            {{ $transaction->currency_symbol }}{{ number_format($transaction->balance) }}
        </td>
    </tr>
</table>

<p style="margin-top: 40px;"><strong><i>School Administrator</i></strong></p>
</body>
</html>
{{-- {{dd('sdds')}} --}}
