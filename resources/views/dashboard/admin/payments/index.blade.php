@extends('dashboard.admin.index')
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
                            <th>Training</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount Paid</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Manage</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($pops as $pop)
                        <tr>
                            <td>{{ $pop->date }}</td>
                            <td>{{ $pop->program->p_name }}</td>
                            <td>{{ $pop->name }}</td>
                            <td>{{ $pop->email }}</td>
                            <td>{{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY'). $pop->amount }}</td>
                            <td>{{ $pop->bank }}</td>
                            <td>{{ $pop->location }}</td>
                           
                             <td>
                                <div class="btn-group">
                                    <a href="#"><img data-toggle="tooltip" data-placement="top" title="View Proof of payment" id="myImg{{ $pop->file }}" src="view/{{ $pop->file }}" alt="{{ $pop->name }}" style="width:40px;max-width:300px"></a>
                                    
                                    <a data-toggle="tooltip" data-placement="top" title="Approve Payment"  onclick="return confirm('Are you really sure?');"
                                        class="btn btn-success" href="{{ route('pop.show', $pop->id) }}"><i
                                            class="fa fa-check"></i>
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
                            <th>Manage</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($transactions as $transaction)
                        
                        <tr>
                            <td>{{ $transaction->name ?? 'N/A' }} <br> {{ $transaction->email ?? 'N/A' }} <br>{{ $transaction->t_phone ?? 'N/A' }}  </td>
                            <td>
                                <small class="training-details">
                                    <strong>Training:</strong> {{ $transaction->p_name ?? 'N/A' }} <br>  
                                    <strong>Paid:</strong> {{ $transaction->currency. $transaction->t_amount }}<br>  
                                    <strong>Balance:</strong>
                                         @if($transaction->paymentStatus == 0 )
                                            <span style="color:red">{{  $transaction->currency. $transaction->balance }} </span>
                                        @else
                                            <span style="color:green">{{ $transaction->currency.  $transaction->balance }}</span>
                                        @endif
                                    <br>      
                                    <strong>Bank: </strong>{{ $transaction->t_type }} <br>
                                    <strong>Date: </strong>{{ $transaction->created_at }}
                                   
                                </small>
                                
                            </td>   
                            <td>
                                <small class="id-details">
                                    <strong>Invoice ID:</strong> {{ $transaction->invoice_id }} <br>
                                    <strong>Transaction ID:</strong> {{ $transaction->transid }} <br>
                                    <strong>Payment Type:</strong> {{ $transaction->paymenttype }} <br>
                                     <strong>Type: </strong>{{ $transaction->t_type }} <br>
                                    <strong>Currency: </strong>{{ $transaction->currency }}
                                     
                                </small>
                            </td>
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
                        {{-- @endif --}}
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection