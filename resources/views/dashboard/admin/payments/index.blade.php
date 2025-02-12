@php
    $checks = [
        'payments.destroy',
        'payments.edit',
        'impersonate',
        'payments.show',
        'users.edit'
    ];

    $permissions = canUserAccessPermission($checks);

@endphp
@extends('dashboard.admin.index')
@section('css')
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
@section('title', 'All Transactions')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">All Transactions
                @include('layouts.partials.alerts')
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="name" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="{{ request('phone') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <tr id="transaction-row-{{ $transaction->id }}">
                            <td>{{ $i++ }}</a>
                            <td><strong>Name: </strong>
                                @if($permissions['users.edit'])
                                <a href="{{ route('users.edit', $transaction->user_id)}}" target="_blank">{{ $transaction->user->name ?? 'N/A' }} <i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
                                <br> <strong>Phone: </strong>{{ $transaction->user->phone ?? 'N/A' }} <br> <strong>Email:</strong> {{ $transaction->user->email ?? 'N/A' }}
                                @endif
                                @if($transaction->user->last_login) <br>
                                <span style="color:green"><strong>Last Login: </strong>{{ $transaction->user->last_login ? date("M jS, Y H:i", strtotime($transaction->user->last_login)) : '' }}</span>
                                @endif
                                @if($permissions['payments.edit'])
                                <br> 
                                <strong>Account balance: </strong>{{number_format($transaction->user->account_balance)}}
                                @endif
                                @if($permissions['impersonate']) <br>
                                <a target="_blank" data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                class="btn btn-dark btn-sm w-50 mb-3" href="{{ route('impersonate', $transaction->user_id) }}">
                                    <i class="fa fa-unlock"> Peek</i>
                                </a>
                                @endif

                                <span id="formSuccessSpan-{{ $transaction->id }}" style="display:none">
                                    <div class="alert alert-success" role="alert">
                                        <strong><span class="formSuccess"></span></strong> 
                                    </div>
                                </span>
                            </td>
                            <td>
                                <small class="training-details">
                                    <a href="{{ route('programs.edit', $transaction->program->id)}}" target="_blank"><strong>Training:</strong> {{ $transaction->program->p_name ?? 'N/A' }} <i class="fas fa-external-link-alt" aria-hidden="true"></i></a><br>  
                                    @if($transaction->program->allow_preferred_timing == 'yes' && !empty($transaction->program->preferred_timing)) <strong>Preferred Timing: </strong> <span style="background: #05f4a6;padding: 5px;border-radius: 5px;">{{$transaction->preferred_timing}} </span> @endif
                                        @if($permissions['payments.edit'])
                                        
                                            <strong>Paid:</strong> {{ $transaction->currency }} <span  id="transaction-amount-{{ $transaction->id }}">{{ number_format($transaction->amount) }}</span>
                
                                            @if(!is_null($transaction->coupon_code))
                                            <span style="color:blue">
                                            <strong>Coupon ({{ $transaction->coupon_code }}) Applied | {{ $transaction->currency.number_format($transaction->coupon_amount) }}  </strong>
                                            </span>
                                            @endif
                                            <br>
                                            <strong>Balance:</strong>
                                                @if($transaction->balance > 0 )
                                                    <span id="transaction-balance-redspan-{{ $transaction->id }}" style="color:red">{{ $transaction->currency }} <span id="transaction-balance-red-{{ $transaction->id }}">{{ number_format($transaction->balance) }}</span> </span>
                                                @else
                                                    <span id="transaction-balance-greenspan-{{ $transaction->id }}" style="color:green">{{ $transaction->currency }} <span id="transaction-balance-green-{{ $transaction->id }}">{{ number_format($transaction->balance) }}</span></span>
                                                @endif
                                            <br>      
                                        @endif
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
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModals{{$transaction->transid }}">
                                            <i class="fa fa-eye"></i> View Payment Trail
                                        </button>
                                    @endif

                                </small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    @if($permissions['payments.edit'])
                                        <!-- Button Trigger -->
                                        <a data-toggle="tooltip" data-placement="top" title="Edit Transaction:"
                                            class="btn btn-info btn-sm open-modal" 
                                            data-id="{{ $transaction->id }}"
                                            href="javascript:void(0)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <!-- Sidebar Modal -->
                                        <div class="modal fade sidebarModal" id="editSidebarModal" tabindex="-1" role="dialog" aria-labelledby="editSidebarModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-lg modal-fullscreen-sm-down modal-dialog-slideout" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editSidebarModalLabel">Update Transaction</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="modalContent">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <a data-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                        class="btn btn-warning btn-sm" href="{{ route('payments.print', $transaction->id) }}"><i
                                            class="fa fa-print"></i>
                                    </a>
                                    @if($permissions['payments.show'])
                                    <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                        class="btn btn-primary btn-sm" href="{{ route('payments.show', $transaction->id) }}"><i
                                            class="far fa-envelope"></i>
                                    </a>
                                    @endif
                                    
                                    @if($permissions['payments.destroy'])
                                    <form action="{{ route('payments.destroy', $transaction->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete transaction"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="exampleModal{{$transaction->transid}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Payment Trail for {{ $transaction->transid }}</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModals{{$transaction->transid }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Payment Trail for {{ $transaction->transid }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @foreach($transaction->paymentthreads->sortByDESC('created_at') as $thread)
                                            <div class="row">
                                                <div class="col-md-8">
                                                    Transaction Id :
                                                    <strong>{{ $thread->transaction_id}}</strong>
                                                    <br>
                                                    Date: 
                                                    <strong>{{ $thread->created_at->format('d/m/Y H:i:s') }}</strong> <br>
                                                    Amount: 
                                                    <strong>{{ number_format($thread->amount) }}</strong>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    @if(!empty($thread->admin_id))
                                                        <div style="background: #18006f38;padding: 10px;border-radius: 10px;">
                                                            Transaction added by<br>
                                                            <strong>{{ $thread->admin?->name }}</strong>
                                                        </div>
                                                    @else 
                                                        <div style="background: #006f3138;padding: 10px;border-radius: 10px;">
                                                            Transaction added by<br>
                                                            <strong>{{ $thread->user?->name }}</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr>
                                        @endforeach
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    $(document).ready(function () {
        $(document).on('click', '.open-modal', function () {
            const transactionId = $(this).data('id'); 
            const modalContent = $('#modalContent');

            // Show loading spinner
            modalContent.html(`
                <div class="text-center my-3">
                    <i class="fas fa-spinner fa-spin fa-2x"></i> Loading...
                </div>
            `);
            
            // Generate the dynamic URL using the transaction ID
            const url = "{{ route('payments.edit', ':id') }}".replace(':id', transactionId);
            // Make an AJAX request to fetch the data
            $.ajax({

                url: url, // Use the dynamic URL here
                method: 'GET',
                success: function (response) {
                    // Inject the response HTML into the modal body
                    modalContent.html(response);

                    // Show the modal
                    $('#editSidebarModal').modal('show');
                },
                error: function (xhr) {
                    console.error('Error loading modal content:', xhr.responseText);
                    modalContent.html(`
                        <div class="text-danger text-center my-3">
                            <i class="fas fa-exclamation-circle"></i> Failed to load data.
                        </div>
                    `);
                }
            });
        });
    });

</script>
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