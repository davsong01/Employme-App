@extends('dashboard.admin.index')
@section('title', 'Proofs of Payment')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Customer details</th>
                            <th>Amount Paid</th>
                            <th>Training details</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Actions</th>      
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($transactions->sortBy('date') as $transaction)
                       
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $transaction->created_at }}</td>
                            <td>
                                {{ $transaction->name }} <br>
                                {{ $transaction->phone }} <br>
                                {{ $transaction->email }} <br>
                            </td>
                            <td>
                                {{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($transaction->amount) }}
                            </td>
                            <td>{{ $transaction->program->p_name }} <br>({{  $transaction->program->e_amount <= 0 ? 'Amount: '.\App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$transaction->program->p_amount : 'E/Amount '. \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$transaction->program->e_amount  }}) <br>
                            @if(!is_null($transaction->coupon_code))
                                <span style="color:blue">
                                    <strong>Coupon ({{ $transaction->coupon }}) Applied | {{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($transaction->coupon->coupon_amount) }}  </strong>
                                </span>
                                @endif
                            </td>
                            <td>{{ $transaction->bank }}</td>
                            <td>{{ $transaction->location }}</td>
                           
                             <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm('Are you really sure?');"
                                        class="btn btn-danger" href="{{ route('temp.destroy', $transaction->id) }}"><i
                                            class="fa fa-trash"></i>
                                    </a>

                                    {{-- <form action="{{ route('temp.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete Temp Transaction"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form> --}}
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