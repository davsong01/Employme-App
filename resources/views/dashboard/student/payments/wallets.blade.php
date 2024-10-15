<?php 
    use App\Models\Settings;
?>
@extends('dashboard.student.index')
@section('title', 'Account TopUp History')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Account TopUp History </h5>
            <div style="margin: 15px 0;">
                Balance: <span class="bal" style="padding: 5px;border-radius: 10px;background: antiquewhite;">{{ Settings::value('DEFAULT_CURRENCY'). number_format($balance) }}</span> 
                <a target="_blank" class="btn btn-success btn-sm" style="border-radius: 5px;" href="{{route('home')}}"><i class="fa fa-plus"></i> &nbsp;Top Up</a>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Channel</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($wallets as $wallet)
                        <tr>
                            <td>{{  date("M jS, Y", strtotime($wallet->created_at)) }}</td>
                            <td>{{ $wallet->transaction_id }}</td>
                            <td>{{ ucfirst($wallet->method) }}</td>
                            <td style="color:{{ $wallet->type == 'credit' ? 'green' : 'red'}}">{{ $wallet->type }}</td>
                            <td>{{ Settings::value('DEFAULT_CURRENCY').number_format($wallet->amount) }}</td>
                            <td style="color:{{ $wallet->status == 'approved' ? 'green' : 'red'}}">{{ ucfirst($wallet->status) }}</td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

@endsection