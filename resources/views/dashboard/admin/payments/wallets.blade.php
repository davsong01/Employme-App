<?php 
    use App\Models\Settings;
?>
@extends('dashboard.admin.index')
@section('title', 'Account TopUp History')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Account TopUp History </h5>
            <div style="margin: 15px 0;">
                Total Balance: <span class="bal" style="padding: 5px;border-radius: 10px;background: antiquewhite;">{{ Settings::value('DEFAULT_CURRENCY'). number_format($balance) }}</span>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Transaction ID</th>
                            <th>Method</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($wallets as $wallet)
                        <tr>
                            <td>Name: {{$wallet->user->name}} <br>Email: {{$wallet->user->email}} <br>Date: {{  date("M jS, Y", strtotime($wallet->created_at)) }}
                                @if(!empty($wallet->admin_id))
                                  <br> <small style="color:blue"> <strong>(Admin Transaction) <br> Created by: {{$wallet->admin->name}} </strong></small>
                                @endif
                            </td>
                            <td>{{ $wallet->transaction_id }}</td>
                            <td>{{ ucfirst($wallet->method) }}</td>
                            <td style="color:{{ $wallet->type == 'credit' ? 'green' : 'red'}}">{{ $wallet->type }}</td>
                            <td>{{ Settings::value('DEFAULT_CURRENCY').number_format($wallet->amount) }}</td>
                            <td style="color:{{ $wallet->status == 'approved' ? 'green' : 'red'}}">{{ ucfirst($wallet->status) }} 
                                @if(isset($wallet->admin_id))
                                <br>
                                <small><strong>By: {{$wallet->admin->name}}</strong> </small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if($wallet->method == 'manual' && $wallet->type == 'credit' && $wallet->status == 'pending')
                                    <a href="#"><img title="View Proof of payment" id="myImg{{ $wallet->proof_of_payment }}" src="pop/{{ $wallet->proof_of_payment }}" alt="View pop" style="width:29px;max-width:300px"></a>
                                    
                                    <a onclick="return(confirm('Are you sure'))" title="Approve TopUp" class="btn btn-success btn-sm" href="{{ route('approve.wallet.history', $wallet->id) }}"><i class="fa fa-check"></i>
                                    </a>
                                    <a onclick="return(confirm('Are you sure'))" title="Delete TopUp" class="btn btn-danger btn-sm" href="{{ route('delete.wallet.history', $wallet->id) }}"><i class="fa fa-trash"></i>
                                    </a>
                                    

                                    <div id="myModal" class="modal">
                                        <span class="close2">&times;</span>
                                        <img class="modal-content" id="img01">
                                        <div id="caption"></div>
                                    </div>
                                    @endif
                                    
                                </div>
                            </td>
                        </tr>
                        
                        <script>
                            // Get the modal
                            var modal = document.getElementById("myModal");

                            // Get the image and insert it inside the modal - use its "alt" text as a caption
                            var img = document.getElementById("myImg{{ $wallet->proof_of_payment }}");
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
</div>

@endsection