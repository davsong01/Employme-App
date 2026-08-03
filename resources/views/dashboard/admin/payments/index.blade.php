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
    .transaction-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: .65rem 1rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #0f766e, #115e59);
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
        box-shadow: 0 8px 24px rgba(15, 118, 110, .2);
    }

    .search-form {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.25rem;
    }

    .search-form .form-control,
    .search-form .select2-selection--single {
        border-radius: 12px;
    }

    .result-card {
        border-radius: 16px;
    }

    .result-card .card-header {
        background: #fff;
        border-bottom: 1px solid #eef2f7;
    }

    .transaction-count {
        text-align: center;
    }

    .bs4-badge-circle {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px;
        height: 45px;
        background-color: #4CAF50;
        border-radius: 50%;
        color: white;
        font-size: 10px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

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
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero result-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">All Transactions</h1>
                            <p class="text-muted mb-0">Search transactions, inspect payment trails, and update records from one dashboard view.</p>
                        </div>
                        <div class="transaction-count-badge">{{ $records }}</div>
                    </div>
                    @include('layouts.partials.alerts')
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="search-form" method="GET" action="{{ route('payments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="transid" class="form-label small text-uppercase fw-semibold text-muted">Transaction ID</label>
                        <input type="text" class="form-control" name="transid" id="transid" placeholder="Enter Transaction ID" value="{{ request('transid') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="invoice_id" class="form-label small text-uppercase fw-semibold text-muted">Invoice ID</label>
                        <input type="text" class="form-control" name="invoice_id" id="invoice_id" placeholder="Enter Invoice ID" value="{{ request('invoice_id') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="name" class="form-label small text-uppercase fw-semibold text-muted">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label small text-uppercase fw-semibold text-muted">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="phone" class="form-label small text-uppercase fw-semibold text-muted">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="{{ request('phone') }}">
                    </div>

                        {{-- <div class="col-md-2">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">Select</option>
                                    <option value="initiated" {{ request('status') == 'initiated' ? 'selected' : '' }}>Initiated</option>
                                    <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                                </select>
                            </div>
                        </div> --}}

                        <div class="col-md-2">
                            <label for="channel" class="form-label small text-uppercase fw-semibold text-muted">Channel</label>
                            <select name="channel" id="channel" class="form-control select2">
                                <option value="">Select Type</option>
                                <option value="Online" {{ request('channel') == 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="Transfer" {{ request('channel') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="payment_type" class="form-label small text-uppercase fw-semibold text-muted">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-control select2">
                                <option value="">Select Type</option>
                                <option value="part" {{ request('payment_type') == 'part' ? 'selected' : '' }}>Part Payment</option>
                                <option value="full" {{ request('payment_type') == 'full' ? 'selected' : '' }}>Full Payment</option>
                                <option value="earlybird" {{ request('payment_type') == 'earlybird' ? 'selected' : '' }}>Early Bird</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="program_id" class="form-label small text-uppercase fw-semibold text-muted">Select Training</label>
                            <select name="program_id" id="program_id" class="form-control select2">
                                <option value="">Select Training</option>
                                @foreach($allPrograms as $training)
                                    <option value="{{ $training->id }}" {{ request('program_id') == $training->id ? 'selected' : '' }}>
                                        {{ $training->p_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="package_id" class="form-label small text-uppercase fw-semibold text-muted">Select Package</label>
                            <select name="package_id" id="package_id" class="form-control select2">
                                <option value="">Select..</option>
                                @foreach($allPackages as $training)
                                    <option value="{{ $training->id }}" {{ request('package_id') == $training->id ? 'selected' : '' }}>
                                        {{ $training->p_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="coupon_id" class="form-label small text-uppercase fw-semibold text-muted">Select Coupon</label>
                            <select name="coupon_id" id="coupon_id" class="form-control select2">
                                <option value="">Select..</option>
                                @foreach($allCoupons as $coupon)
                                    <option value="{{ $coupon->id }}" {{ request('coupon_id') == $coupon->id ? 'selected' : '' }}>
                                        {{ $coupon->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="from" class="form-label small text-uppercase fw-semibold text-muted">From</label>
                            <input type="date" class="form-control" name="from" id="from" value="{{ request('from') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="to" class="form-label small text-uppercase fw-semibold text-muted">To</label>
                            <input type="date" class="form-control" name="to" id="to" value="{{ request('to') }}">
                        </div>

                        <div class="col-md-2 d-grid align-self-end">
                            <button type="submit" class="btn btn-primary btn-search">Search</button>
                        </div>
                    </div>
                </form>

            </div>

            <div class="table-responsive mt-4">
                <table class="table table-hover align-middle crm-table crm-mobile-stack" style="width:100%">
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
                                <td>{{ paginationIndex($transactions, $loop) }}</td>
                                <td><strong>Name: </strong>
                                    @if($permissions['users.edit'])
                                    <a href="{{ route('users.edit', $transaction?->user_id)}}" target="_blank">{{ $transaction?->user->name ?? 'N/A' }} <i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
                                    <br> <strong>Phone: </strong>{{ $transaction?->user->phone ?? 'N/A' }} <br> <strong>Email:</strong> {{ $transaction?->user->email ?? 'N/A' }}
                                    @endif
                                    @if(isset($transaction?->user->last_login)) <br>
                                    <span style="color:green"><strong>Last Login: </strong>{{ $transaction->user->last_login ? date("M jS, Y H:i", strtotime($transaction?->user->last_login)) : '' }}</span>
                                    @endif
                                    @if($permissions['payments.edit'])
                                    <br> 
                                    <strong>Account balance: </strong>{{number_format($transaction?->user?->account_balance)}}
                                    @endif
                                    @if($permissions['impersonate']) <br>
                                    <a target="_blank" data-bs-toggle="tooltip" data-placement="top" title="Impersonate User"
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
                                    <div class="training-details small">
                                        {{-- If package --}}
                                        <div class="mb-2">
                                            @if($transaction->is_package)
                                                <div class="mb-2">
                                                    <span class="fw-bold">Package Name:</span>
                                                    <span class="fw-medium"><a href="{{ route('groupedprogram.edit', $transaction->program_id)}}" target="_blank">{{ ucfirst($transaction->group->p_name) }}</a></span>
                                                </div>
                                            @endif
                                            <span class="fw-bold">
                                                Training{{ $transaction->is_package ? 's' : '' }}:
                                            </span>
                                            <div class="bg-light rounded p-2 mt-1">
                                                <ol class="mb-0 ps-3">
                                                    @foreach ($transaction->allPrograms() as $child)
                                                        <li><a href="{{ route('programs.edit', $child->id)}}" target="_blank" class="fw-bold text-primary">{{ $child->p_name}}</a></li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        </div>
                                        {{-- Single training --}}
                                        
                                        {{-- Preferred timing --}}
                                        @if($transaction->program && $transaction->program->allow_preferred_timing == 'yes' && !empty($transaction->program->preferred_timing))
                                            <div class="mb-2">
                                                <span class="fw-bold">Preferred Timing:</span>
                                                <span class="badge bg-success text-dark">
                                                    {{ $transaction->preferred_timing }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Payment Info --}}
                                        @if($permissions['payments.edit'])
                                            <div class="mb-2">
                                                <span class="fw-bold">Expected Amount:</span>
                                                {{ $transaction->currency_symbol }}
                                                <span id="transaction-expected-amount-{{ $transaction->id }}">
                                                    {{ number_format($transaction->expected_amount > 0 ? $transaction->expected_amount : $transaction->amount + $transaction->balance) }}
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="fw-bold">Paid:</span>
                                                {{ $transaction->currency_symbol }}
                                                <span id="transaction-amount-{{ $transaction->id }}">
                                                    {{ number_format($transaction->amount) }}
                                                </span>
                                            </div>
                                            @if($transaction->remarks)
                                            <div class="mb-2">
                                                <span class="fw-bold">Remarks:</span>
                                                {{ $transaction->remarks }}
                                                
                                            </div>
                                            @endif

                                            @if(!is_null($transaction->coupon_code))
                                                <div class="mb-2 text-primary">
                                                    <strong>Coupon ({{ $transaction->coupon_code }}) <span style='color:blue'> Applied | {{ $transaction->currency . number_format($transaction->coupon_amount ?? $transaction->discount) }}</span></strong>
                                                </div>
                                            @endif

                                            <div class="mb-2">
                                                <span class="fw-bold">Balance:</span>
                                                <span
                                                    id="transaction-balance-wrap-{{ $transaction->id }}"
                                                    class="{{ $transaction->balance > 0 ? 'text-danger' : 'text-success' }}"
                                                    data-currency-symbol="{{ $transaction->currency_symbol }}"
                                                >
                                                    {{ $transaction->currency_symbol }} <span id="transaction-balance-value-{{ $transaction->id }}">{{ number_format($transaction->balance) }}</span>
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Location --}}
                                        @php
                                            if(isset($transaction->t_location)) {
                                                $locations = json_decode($transaction->locations, true);
                                                $location_address = $locations[$transaction->t_location] ?? null;
                                            }
                                        @endphp
                                        @if(!empty($transaction->t_location) && !empty($location_address))
                                            <div class="mb-2">
                                                <span class="fw-bold">Location:</span>
                                                {{ $transaction->t_location }} ({{ $location_address }})
                                            </div>
                                        @endif

                                    </div>
                                </td>

                                <td>
                                    <small class="id-details">
                                        <strong>Invoice ID:</strong> {{ $transaction->invoice_id }} <br>
                                        <strong>Transaction ID:</strong> {{ $transaction->transid }} 
                                        @if($transaction->paymentthreads->count() > 0)
                                        <br>
                                        <strong>Paid At:</strong> {{ $transaction->created_at }} 
                                        @endif
                                        <br>
                                        <strong>Payment Type:</strong> {{ ucfirst($transaction->type) }} <br>
                                        @if(isset($transaction->training_mode))
                                        <strong>Training Mode:</strong> {{ $transaction->training_mode }} <br>
                                        @endif
                                        <strong>Channel: </strong>{{ $transaction->t_type }} <br>
                                        <strong>Status: </strong>{{ ucfirst($transaction->status) }} <br>
                                        
                                        @if($transaction->paymentthreads->count() > 0)
                                            <button type="button" 
                                                class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#trail-{{ $transaction->id }}">
                                                <i class="fa fa-eye"></i> View Payment Trail
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="trail-{{ $transaction->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                                        Parent Transaction Id :
                                                                        <strong>{{ $thread->parent_transaction_id}}</strong>
                                                                        <br>
                                                                        Date: 
                                                                        <strong>{{ $thread->created_at->format('d/m/Y H:i:s') }}</strong> <br>
                                                                        Amount: 
                                                                        <strong>{{ $transaction->currency_symbol.number_format($thread->amount) }}</strong>
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
                                        @endif

                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @if($permissions['payments.edit'])
                                            <!-- Button Trigger -->
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Edit Transaction:"
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
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Print E-receipt"
                                            class="btn btn-warning btn-sm" href="{{ route('payments.print', $transaction->id) }}"><i
                                                class="fa fa-print"></i>
                                        </a>
                                        @if($permissions['payments.show'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                            class="btn btn-primary btn-sm" href="{{ route('payments.show', $transaction->id) }}"><i
                                                class="far fa-envelope"></i>
                                        </a>
                                        @endif
                                        
                                        {{-- @if($permissions['payments.destroy'])
                                        <form action="{{ route('payments.destroy', $transaction->id) }}" method="POST"
                                            onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                data-placement="top" title="Delete transaction"> <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif --}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                </table>
                
            </div>
            {{  $transactions->appends($_GET)->links()  }}
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
            width: '100%',
            allowClear: true            
        });
    });
</script>
@endsection
