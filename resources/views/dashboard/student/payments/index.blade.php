@extends('dashboard.student.index')
@section('title', 'Payment History')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice ID</th>
                            <th>Channel</th>
                            <th>Type</th>
                            <th>Training</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($transactiondetails as $details)
                        <tr>
                            <td>{{ $details->created_at}}</td>
                            <td>{{ $details->invoice_id }}</td>
                            <td>{{ $details->t_type }}</td>
                            <td>{{ $details->paymenttype }}</td>
                            <td>{{ $details->p_name }}</td>
                            <td>{{ $details->p_amount }}</td>
                            <td>{{ config('custom.default_currency') }}{{ $details->t_amount }}</td>
                            @if($details->paymentStatus == 0 )
                            <td><b style="color:red">{{ config('custom.default_currency') }}{{ $details->balance }} </b></td>
                            @else
                            <td><b style="color:green">{{ config('custom.default_currency') }}{{ $details->balance }}</b></td>
                            @endif
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning" href="{{ route('payments.print', $details->id) }}"><i
                                            class="fa fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

@endsection