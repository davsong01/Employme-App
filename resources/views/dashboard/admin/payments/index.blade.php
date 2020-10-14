@extends('dashboard.admin.index')
@section('title', 'Payment History')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Payment History</h5>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Amount Paid</th>
                            <th>Balance</th> 
                            <th>Bank</th> 
                            <th>Invoice ID</th>
                            <th>Transaction ID</th> 
                            <th>Manage</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($transactions as $transaction)
                        @if(isset($transaction->details->name))
                        <tr>
                            <td>{{ $transaction->created_at }}</td>
                            <td>{{isset($transaction->details->name ) ? $transaction->details->name : 'N/A' }}</td>
                            <td>{{isset($transaction->details->email ) ? $transaction->details->email : 'N/A' }}</td>
                            <td>{{ $transaction->program->p_name }}</td>
                            <td>{{ config('custom.default_currency'). $transaction->t_amount }}</td>
                            @if($transaction->paymentStatus == 0 )
                                <td><b style="color:red">{{  config('custom.default_currency'). $transaction->balance }} </b></td> 
                            @else
                                <td><b style="color:green">{{ config('custom.default_currency').  $transaction->balance }}</b></td> 
                            @endif
                            <td>{{ $transaction->t_type }}</td>
                            <td>{{ $transaction->invoice_id }}</td>
                            <td>{{ $transaction->transid }}</td>
                             <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Transaction"
                                        class="btn btn-info" href="{{ route('payments.edit', $transaction->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning" href="{{ route('payments.print', $transaction->id) }}"><i
                                            class="fa fa-print"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                        class="btn btn-primary" href="{{ route('payments.show', $transaction->id) }}"><i
                                            class="far fa-envelope"></i>
                                    </a>
                                    <form action="{{ route('payments.destroy', $transaction->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete transaction"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

@endsection