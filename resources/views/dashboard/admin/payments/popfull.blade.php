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