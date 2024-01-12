@extends('dashboard.admin.index')
@section('css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
@endsection
@section('title', 'Payment History')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Proof of Payment History</h5>
            <div class="">
                <table id="myTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer details</th>
                            <th>Training details</th>
                            <th>Amount Paid</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Actions</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($pops as $pop)
                        
                        <tr>
                            <td>{{ $pop->date }}</td>
                            <td>{{ $pop->name }} <br>
                                {{ $pop->phone }} <br>
                                {{ $pop->email }} <br>
                            </td>
                            <td>{{ $pop->program->p_name }} <br>({{  $pop->program->e_amount <= 0 ? 'Amount: '.$pop->currency_symbol.$pop->program->p_amount : 'E/Amount '. $pop->currency_symbol.$pop->program->e_amount  }})
                                 @if($pop->program->allow_preferred_timing == 'yes') <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;">{{$pop->preferred_timing}} </span> <br> @endif
                            @if(isset($pop->is_fresh)) <br>
                            <span style="margin:5px 10px;border-radius:10px" class="btn btn-info btn-sm">Fresh Payment</span>
                            @endif
                            </td>
                            <td>{{ number_format($pop->amount) }}</td>
                            <td>{{ $pop->bank }}</td>
                            <td>{{ $pop->location }}</td>
                           
                             <td>
                                <div class="btn-group">
                                    <a href="#"><img title="View Proof of payment" id="myImg{{ $pop->file }}" src="view/{{ $pop->file }}" alt="{{ $pop->name }}" style="width:40px;max-width:300px"></a>
                                    
                                    <a  title="Approve Payment" class="btn btn-success" href="{{ route('pop.show', $pop->id) }}"><i class="fa fa-check"></i>
                                    </a>
                                    <form action="{{ route('pop.destroy', $pop->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete proof of payment"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <div id="myModal" class="modal">
                            <span class="close2">&times;</span>
                            <img class="modal-content" id="img01">
                            <div id="caption"></div>
                        </div>
                        <script>
                        // Get the modal
                        var modal = document.getElementById("myModal");

                        // Get the image and insert it inside the modal - use its "alt" text as a caption
                        var img = document.getElementById("myImg{{ $pop->file }}");
                        var modalImg = document.getElementById("img01");
                        var captionText = document.getElementById("caption");
                        img.onclick = function(){
                        modal.style.display = "block";
                        modalImg.src = this.src;
                        captionText.innerHTML = this.alt;
                        }

                        // Get the <span> element that closes the modal
                        var span = document.getElementsByClassName("close2")[0];

                        // When the user clicks on <span> (x), close the modal
                        span.onclick = function() { 
                        modal.style.display = "none";
                        }
                        </script>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="transTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Customer details</th>
                            <th>Training details</th>
                            <th>Payment details</th>
                            <th>Actions</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($transactions as $transaction)
                        
                        <tr>
                            <td><strong>Name: </strong><a href="{{route('users.edit', $transaction->user_id)}}" target="_blank">{{ $transaction->name ?? 'N/A' }} &nbsp;<img src="/external.png" alt="" style="width: 10px;"></a>
                                <br> <strong>Phone: </strong>{{ $transaction->t_phone ?? 'N/A' }} <br> <strong>Email:</strong> {{ $transaction->email ?? 'N/A' }} <br> <strong>Account balance: </strong>{{number_format($transaction->user->account_balance)}}</td>
                            <td>
                                <small class="training-details">
                                    <a href="{{ route('programs.edit', $transaction->program_id)}}" target="_blank"><strong>Training:</strong> {{ $transaction->p_name ?? 'N/A' }}</a><br>  
                                    @if($transaction->allow_preferred_timing == 'yes') <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;">{{$transaction->preferred_timing}} </span> <br> @endif
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
                                   
                                    <strong>Bank: </strong>{{ $transaction->t_type ?? null }} <br>
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
        </div>
    </div>
</div>
@endsection