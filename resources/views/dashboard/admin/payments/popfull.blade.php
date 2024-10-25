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
                                {{-- 2348037067223 --}}
                                <?php 
                                    $string =  "*Name:* " . $pop->name . "
                                    *Phone:* " . $pop->phone . "
                                    *Email:* " . $pop->email . "
                                    *Training:* " . $pop->program->p_name;
                                ?>
                                <a class="btn btn-dark btn-sm rounded" href="https://api.whatsapp.com/send?phone=2348037067223&text={{ urlencode($string)  }}" target="_blank">
                                    <i class="fab fa-whatsapp"></i> Send via WhatsApp
                                </a>
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
                                    <a href="#" data-toggle="modal" data-target="#myModal{{ $pop->id }}">
                                        <img title="View Proof of payment" id="myImg{{ $pop->id }}" src="{{ url('/uploads/'.$pop->file) }}" alt="{{ $pop->name }}" style="width:40px;max-width:300px">
                                    </a>
                                    <a href="#" class="btn btn-info" data-toggle="modal" data-target="#editpop{{ $pop->id }}"><i class="fa fa-edit"></i>
                                    </a>
                                    <a title="Approve Payment" class="btn btn-success" href="{{ route('pop.show', $pop->id) }}"><i class="fa fa-check"></i></a>
                                    <form action="{{ route('pop.destroy', $pop->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip" data-placement="top" title="Delete proof of payment">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade mt-5" id="myModal{{ $pop->id }}" tabindex="-1" aria-labelledby="imageModal{{ $pop->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ $pop->name }}'s Payment Proof</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="{{ url('/uploads/'.$pop->file) }}" alt="{{ $pop->name }}" class="img-fluid">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editpop{{ $pop->id }}" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="batchModalLabel">Update {{ $pop->name }}'s Payment Proof</h5>
                                        <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('pop.update', $pop->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="date" class="form-label">Date</label>
                                                    <input type="date" class="form-control" id="date" name="date" value="{{ $pop->date ? \Carbon\Carbon::parse($pop->date)->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" value="{{$pop->name}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" value="{{$pop->phone}}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email" value="{{$pop->email}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="amount" class="form-label">Amount</label>
                                                    <input type="text" class="form-control" id="amount" name="amount" value="{{$pop->amount}}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="location" class="form-label">Location</label>
                                                    <input type="text" class="form-control" id="location" name="location" value="{{$pop->location}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="program_id" class="form-label">Training</label>
                                                    <select name="program_id" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach($programs as $program)
                                                        <option value="{{ $program->id }}" {{ $program->id == $pop->program->id ? 'selected' : '' }}>
                                                            {{ $program->p_name }} ({{ $program->p_amount }})
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                Update
                                            </button>
                                        </div>
                                    </form>
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