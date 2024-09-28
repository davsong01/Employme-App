@extends('dashboard.admin.index')
@section('css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
<style>
    .select2-container--default .select2-selection--single {
        border: 1px solid #e9ecef;
        border-radius: 20px;
        padding: 0.375rem 0.75rem;
        height: calc(2.25rem + 2px);
        font-size: 0.875rem;
        color: #4F5467;
        background-color: #fff;
        line-height: 1.5;
    }

    .select2.select2-container.select2-container--default {
        width: 100% !important;
    }
</style>
@endsection
@section('title', 'Payment History')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment History
                <div class="badge float-right">
                    <span class="transaction-count">{{ $records }}</span>
                </div>
            </h5>
            <div class="card-body">
                @php
                    $currentStatus = request('status');
                @endphp
            
                <div class="">
                <form class="search-form" method="GET" action="{{ route('payments.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="{{ request('phone') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control select2">
                                    <option value="">Select Type</option>
                                    @foreach ($types as $type)
                                    <option value="{{ $type->t_type}}">{{ $type->t_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="program_id">Select Training</label> <br>
                                <select name="program_id" id="program_id" class="form-control select2">
                                    <option value="">Select Training</option>
                                    @foreach($allPrograms as $training)
                                    <option value="{{ $training->id}}">{{ $training->p_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from">From</label>
                                <input type="date" class="form-control" name="from" id="from">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to">To</label>
                                <input type="date" class="form-control" name="to" id="to">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for=""><span style="color:transparent">label</span></label> <br>
                                <button type="submit" class="btn btn-primary btn-search" style="width: 100%">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer details</th>
                                <th>Training details</th>
                                <th>Payment details</th>
                                <th>Actions</th>       
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach($transactions as $transaction)
                            
                            <tr>
                                <td>{{ $i++ }}</a>
                                <td><strong>Name: </strong><a href="{{route('users.edit', $transaction->user_id)}}" target="_blank">{{ $transaction->user->name ?? 'N/A' }} &nbsp;<img src="/external.png" alt="" style="width: 10px;"></a>
                                    <br> <strong>Phone: </strong>{{ $transaction->t_phone ?? 'N/A' }} <br> <strong>Email:</strong> {{ $transaction->user->email ?? 'N/A' }} <br> <strong>Account balance: </strong>{{number_format($transaction->user->account_balance)}}</td>
                                <td>
                                    <small class="training-details">
                                        <a href="{{ route('programs.edit', $transaction->program->id)}}" target="_blank"><strong>Training:</strong> {{ $transaction->program->p_name ?? 'N/A' }}</a><br>  
                                        @if($transaction->program->allow_preferred_timing == 'yes' && !empty($transaction->program->preferred_timing)) <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;">{{$transaction->preferred_timing}} </span> <br> @endif
                                        <strong>Paid:</strong> {{ $transaction->currency. number_format($transaction->t_amount) }}
                                        @if(!is_null($transaction->coupon_code))
                                        <span style="color:blue">
                                        <strong>Coupon ({{ $transaction->coupon_code }}) Applied | {{ $transaction->currency.number_format($transaction->coupon_amount) }}  </strong>
                                        </span>
                                        @endif
                                        <br>
                                        <strong>Balance:</strong>
                                            @if($transaction->balance > 0 )
                                                <span style="color:red">{{  $transaction->currency. number_format($transaction->balance) }} </span>
                                            @else
                                                <span style="color:green">{{ $transaction->currency.  number_format($transaction->balance) }}</span>
                                            @endif
                                        <br>      
                                    
                                        <?php
                                            if(isset($transaction->t_location) && isset($transaction->t_location)){
                                                $locations = json_decode($transaction->locations, true);
                                                $location_address = $locations[$transaction->t_location] ?? null;
                                            }
                                        ?>
                                    
                                        @if(isset($transaction->t_location) && !empty($transaction->t_location) && !empty( $location_address))
                                        <strong>Location:</strong> {{ $transaction->t_location}}({{ $location_address}}) <br>
                                        @endif
                                        <strong>Date: </strong>{{ $transaction->created_at }}
                                    
                                    </small>
                                    
                                </td>   
                                <td>
                                    <small class="id-details">
                                        <strong>Invoice ID:</strong> {{ $transaction->invoice_id }} <br>
                                        <strong>Transaction ID:</strong> {{ $transaction->transid }} 
                                        @if(isset($transaction->balance_amount_paid))
                                        <br>
                                        <strong>Last Balance Paid:</strong> {{ $transaction->currency_symbol.number_format($transaction->balance_amount_paid) }} <br>
                                        <strong>Paid At:</strong> {{ $transaction->balance_paid }} 
                                        @endif
                                        <br>
                                        <strong>Payment Type:</strong> {{ $transaction->paymenttype }} <br>
                                        @if(isset($transaction->training_mode))
                                        <strong>Training Mode:</strong> {{ $transaction->training_mode }} <br>
                                        @endif
                                        <strong>Type: </strong>{{ $transaction->t_type }} <br>
                                        <strong>Currency: </strong>{{ $transaction->currency }}
                                    
                                        @if($transaction->paymentthreads->count() > 0)
                                        <br>
                                            <a class="btn btn-info btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#exampleModal{{$transaction->transid }}"><i class="fa fa-eye"></i>View Payment Trail</a>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a data-toggle="tooltip" data-placement="top" title="Edit Transaction"
                                            class="btn btn-info btn-sm" href="{{ route('payments.edit', $transaction->id) }}"><i
                                                class="fa fa-edit"></i>
                                        </a>
                                        <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                            class="btn btn-warning btn-sm" href="{{ route('payments.print', $transaction->id) }}"><i
                                                class="fa fa-print"></i>
                                        </a>
                                        <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                            class="btn btn-primary btn-sm" href="{{ route('payments.show', $transaction->id) }}"><i
                                                class="far fa-envelope"></i>
                                        </a>
                                        @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                                            <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                                class="btn btn-dark btn-sm" href="{{ route('impersonate', $transaction->user_id) }}"><i
                                                    class="fa fa-unlock"></i>
                                            </a>
                                        @endif 
                                        <form action="{{ route('payments.destroy', $transaction->id) }}" method="POST"
                                            onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                                data-placement="top" title="Delete transaction"> <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                            <div class="modal fade" id="exampleModal{{$transaction->transid}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Payment Trail for {{ $transaction->transid }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @foreach($transaction->paymentthreads as $thread)
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
                                            @if(!empty($thread->admin_id))
                                            <div class="col-md-6" style="background: #18006f38;padding: 10px;border-radius: 10px;">
                                                Transaction added by<br>
                                                <strong>{{ $thread->admin->name }}</strong>
                                            </div>
                                            @else 
                                            <div class="col-md-6" style="background: #006f3138;padding: 10px;border-radius: 10px;">
                                                Transaction added by<br>
                                                <strong>{{ $thread->user->name }}</strong>
                                            </div>
                                            @endif
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
                {{  $transactions->appends($_GET)->links()  }}

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select Training",
            width: '100%'
            allowClear: true            
        });
    });
</script>
@endsection