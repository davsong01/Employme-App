@extends('dashboard.admin.index')
@section('title', 'Proofs of Payment')
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
                        @foreach($transactions->sortBy('date') as $transaction)
                        <tr>
                            <td>{{ $transaction->date }}</td>
                            <td>{{ $transaction->program->p_name }}</td>
                            <td>{{ $transaction->name }}</td>
                            <td>{{ $transaction->email }}</td>
                            <td>{{ config('custom.default_currency'). $transaction->amount }}</td>
                            <td>{{ $transaction->bank }}</td>
                            <td>{{ $transaction->location }}</td>
                           
                             <td>
                                <div class="btn-group">
                                    <a href="#"><img data-toggle="tooltip" data-placement="top" title="View Proof of payment" id="myImg{{ $transaction->file }}" src="view/{{ $transaction->file }}" alt="{{ $transaction->name }}" style="width:40px;max-width:300px"></a>
                                    
                                    <a data-toggle="tooltip" data-placement="top" title="Approve Payment"  onclick="return confirm('Are you really sure?');"
                                        class="btn btn-success" href="{{ route('pop.show', $transaction->id) }}"><i
                                            class="fa fa-check"></i>
                                    </a>
                                    <form action="{{ route('pop.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
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
                        var img = document.getElementById("myImg{{ $transaction->file }}");
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