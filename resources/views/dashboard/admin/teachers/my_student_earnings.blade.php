@extends('dashboard.admin.index')
@section('title', 'Earnings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
             <div class="card-header">
                <div>
                    <h5 class="card-title">Earnings ({{ $currency?? 'NGN' }}{{ number_format($earnings->sum('facilitator_earning')) }})</h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice Id</th>
                            <th>Date Earned</th>
                            <th>Student</th>
                            <th>Program</th>
                            <th>Amount</th>
                            <th>Coupon applied</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($earnings as $earning)
                        <tr>
                            <td>{{ $earning->invoice_id ?? NULL}}</td>
                            <td>{{ $earning->created_at ?? NULL }}</td>
                            <td>{{ $earning->name ?? NULL }}</td>  
                            <td>{{ $earning->p_name ?? NULL}}</td>
                            <td>{{ $earning->currency_symbol}} {{ number_format($earning->facilitator_earning, 2) ?? NULL}}</td>
                            <td>
                                @if($earning->coupon_id)
                                    <strong>Coupon Code:</strong> {{ $earning->coupon_code}}  <br>
                                   
                                    <strong>Amount: </strong> {{ $earning->currency_symbol }} {{number_format($earning->coupon_amount,2) }} <br>
                                        @if(isset($earning->coupon_id))
                                        <strong>Created by: </strong>
                                            @if(isset($earning->facilitator_id))
                                            {{ app('\App\Http\Controllers\Admin\CouponController')->getCreatedBy($earning->coupon_id) }}
                                            @endif
                                        @endif
                                @endif
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