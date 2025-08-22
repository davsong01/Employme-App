@php
    $checks = [
        'pop.edit',
        'pop.show',
        'pop.destroy',
    ];

    $permissions = canUserAccessPermission($checks);
@endphp
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
                            <th>Type</th>
                            <th>Customer details</th>
                            <th>Amount Paid</th>
                            <th>Training details</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Image</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($pops as $pop)
                            @if($pop->related)
                                <tr>
                                    <td>{{ $pop->date }}</td>
                                    <td>{{ ucfirst($pop?->temp?->type ?? 'N/A') }}</td>

                                    <td>{{ $pop->name }} <br>
                                        {{ $pop->phone }} <br>
                                        {{ $pop->email }} <br>
                                        <?php 
                                            $string =  "*Name:* " . $pop->name . "
                                            *Phone:* " . $pop->phone . "
                                            *Email:* " . $pop->email . "
                                            *Training:* " . $pop->related?->p_name . "
                                            *Amount Paid:* " . $pop->amount;
                                        ?>
                                        
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <a class="btn btn-dark btn-sm" href="https://api.whatsapp.com/send?phone=2347038378085&text={{ urlencode($string) }}" target="_blank">
                                                <i class="fab fa-whatsapp"></i> Send via WhatsApp
                                            </a>
                                            @if($permissions['pop.edit'])
                                            <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editpop{{ $pop->id }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            @endif
                                            @if($permissions['pop.show'])
                                            <a title="Approve Payment" class="btn btn-success btn-sm" href="{{ route('pop.show', $pop->id) }}">
                                                <i class="fa fa-check"></i> Approve
                                            </a>
                                            @endif
                                            @if($permissions['pop.destroy'])
                                            <form action="{{ route('pop.destroy', $pop->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you really sure?');">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Proof of Payment">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ number_format($pop->amount) }}</td>
                                    <td>{{ $pop->related->p_name }} <br>({{  $pop->related->e_amount <= 0 ? 'Amount: '.$pop->currency_symbol.$pop->related->p_amount : 'E/Amount '. $pop->currency_symbol.$pop->related->e_amount  }})
                                    @if(isset($pop->is_fresh)) <br>
                                    <span style="margin:5px 10px;border-radius:10px" class="btn btn-info btn-sm">Fresh Payment</span>
                                    @endif
                                    </td>
                      
                                    <td>{{ $pop->bank }}</td>
                                    <td>{{ $pop->location }}</td>
                                
                                    <td>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#myModal{{ $pop->id }}">
                                            <img title="View Proof of Payment" id="myImg{{ $pop->id }}" 
                                                src="{{ url('/uploads/'.$pop->file) }}" 
                                                alt="{{ $pop->name }}" 
                                                class="img-thumbnail" style="width: 60px;">
                                        </a>
                                    </td>
                                </tr>
                                <div class="modal fade mt-5" id="myModal{{ $pop->id }}" tabindex="-1" aria-labelledby="imageModal{{ $pop->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $pop->name }}'s Payment Proof</h5>
                                                <button type="button" class="close"  data-bs-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{ url('/uploads/'.$pop->file) }}" alt="{{ $pop->name }}" class="img-fluid">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal" id="editpop{{ $pop->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update {{ $pop->name }}'s Payment Proof</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('pop.update', $pop->id) }}" method="POST">
                                                @method('PATCH')
                                                @csrf
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
                                                    {{-- @if($pop->is_package) --}}
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="group_id" class="form-label">Training/Package</label>
                                                            <select name="is_package" class="form-control">
                                                                <option value="">Select</option>
                                                                <option value="0" {{ !$pop->is_package ? 'selected' : ''}}>Training</option>
                                                                <option value="1" {{ $pop->is_package ? 'selected' : ''}}>Package</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="group_id" class="form-label">Package</label>
                                                            <select name="group_id" id="group_id_{{ $pop->id }}" class="form-control">
                                                                <option value="">Select</option>
                                                                @foreach($packages as $package)
                                                                <option value="{{ $package->id }}" {{ $package->id == $pop->related->id ? 'selected' : '' }}>
                                                                    {{ $package->p_name }} ({{ $package->p_amount }})
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    {{-- @else --}}
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="program_id" class="form-label">Training</label>
                                                            <select name="program_id" id="program_id_{{ $pop->id }}" class="form-control">
                                                                <option value="">Select</option>
                                                                @foreach($programs as $program)
                                                                <option value="{{ $program->id }}" {{ $program->id == $pop->related->id ? 'selected' : '' }}>
                                                                    {{ $program->p_name }} ({{ $program->p_amount }})
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    {{-- @endif --}}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection