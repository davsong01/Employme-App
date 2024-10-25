@extends('dashboard.student.index')
@section('title', 'Payment History')
@section('css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">
<style>
    a{
        text-decoration: none !important;
    }

    .accounts{
        min-height: 270px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
@endsection
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
                            <th>Invoice ID</th>
                            <th>Payment Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($transactiondetails as $details)
                        <tr>
                            <td>
                                <strong>Invoice Id:</strong> {{ $details->invoice_id }} <br>
                                <strong>Transaction Id:</strong> {{ $details->transid }} <br>
                                <strong>Date: </strong>{{ $details->created_at->format('d/m/Y')}} <br>
                                <strong>Channel: </strong>{{ $details->t_type }}
                                @if($details->paymentthreads->count() > 0)
                                <br>
                                    <a class="btn btn-info btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal{{$details->transid }}"><i class="fa fa-eye"></i>View Payment Trail</a>
                                @endif
                            </td>
                            
                            <td>
                                <strong>Training: </strong>{{ $details->p_name }} <br>
                                <strong>Training Fee: </strong>{{ $details->p_amount }} <br>
                                <strong>Amount Paid: </strong>{{ config('custom.default_currency') }}{{ $details->t_amount }} <br>
                                @if(!empty($details->paymenttype))
                                <strong>Type: </strong>{{ $details->paymenttype }} <br>
                                @endif
                                <strong>Balance: </strong><span style="color:{{$details->paymentStatus == 0 ? 'red' : 'green'}}">{{ config('custom.default_currency') }}{{ $details->balance }} </span>
                            </td>
                            
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning btn-sm" href="{{ route('payments.print', $details->id) }}"><i
                                            class="fa fa-print"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="exampleModal{{$details->transid}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Payment Trail for {{ $details->transid }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @foreach($details->paymentthreads as $thread)
                                    <div class="row">
                                        <div class="col-md-6">
                                            Transaction Id <br>
                                            <strong>{{ $thread->transaction_id}}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            Date <br>
                                            <strong>{{ $thread->created_at->format('d/m/Y') }}</strong>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            Amount<br>
                                            <strong>{{ number_format($thread->amount) }}</strong>
                                        </div>
                                    </div>
                                    <hr>
                                    @endforeach
                                </div>
                                
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

@endsection