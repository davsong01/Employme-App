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
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                <h5 class="card-title mb-0">Proof of Payment History</h5>

                <form method="GET" action="{{ route('proof.payment') }}" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="missing_relation" value="0">
                    <div class="form-check mb-0">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            value="1"
                            id="missing_relation"
                            name="missing_relation"
                            {{ request()->boolean('missing_relation') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="missing_relation">
                            Missing relation only
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    @if(request()->boolean('missing_relation'))
                        <a href="{{ route('proof.payment') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    @endif
                </form>
            </div>
            <div class="">
                <table id="myTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Customer details</th>
                            <th>Payment</th>
                            <th>Training details</th>
                            <th>Bank</th> 
                            <th>Location</th>
                            <th>Image</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($pops as $pop)
                            @php
                                $related = $pop->related ?? $pop->program ?? $pop->group;
                                $paymentType = strtolower($pop->payment_type ?? $pop?->temp?->payment_type ?? $pop?->temp?->type ?? 'n/a');
                                $paymentSource = null;

                                if ($paymentType === 'earlybird') {
                                    $paymentSource = 'Auto-detected';
                                }

                                $trainingName = $related?->p_name
                                    ?? ($pop->is_package ? 'Package removed' : 'Training removed')
                                    ?? 'N/A';

                                $trainingAmount = $related
                                    ? ($related->e_amount <= 0
                                        ? 'Amount: ' . $pop->currency_symbol . number_format($related->p_amount)
                                        : 'E/Amount: ' . $pop->currency_symbol . number_format($related->e_amount))
                                    : 'No linked training/package';

                                $string = "*Name:* " . $pop->name . "
                                *Phone:* " . $pop->phone . "
                                *Email:* " . $pop->email . "
                                *Training:* " . $trainingName . "
                                *Amount Paid:* " . $pop->amount;
                            @endphp

                            <tr>
                                <td>
                                    {{ $pop->date }}
                                </td>
                                <td>
                                    <div>{{ ucfirst($paymentType) }}</div>
                                    @if($paymentSource)
                                        <span class="badge bg-light text-dark border mt-1">{{ $paymentSource }}</span>
                                    @endif
                                    @if(!$related)
                                        <span class="badge bg-warning text-dark border mt-1">Missing relation</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $pop->name }} <br>
                                    {{ $pop->phone }} <br>
                                    {{ $pop->email }} <br>

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
                                            <a title="Approve Payment" onclick="return confirm('Are you sure')" class="btn btn-success btn-sm" href="{{ route('pop.show', $pop->id) }}">
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
                                <td>
                                    Amount Paid: {{ $pop->currency_symbol.number_format($pop->amount) }}
                                    @if(!empty($pop->temp->coupon_id))
                                        <small style="color:blue"><br>Coupon Applied: <strong>{{ $pop->temp->coupon->code }}</strong> ({{$pop->currency_symbol.number_format($pop->temp->coupon->amount)}})</small>
                                    @endif
                                    @if(!empty($pop->temp_transaction_id))
                                        <small style="color:indigo"><br>
                                        TransactionID: {{$pop->temp->transid}}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $trainingName }}</div>
                                    <div>({{ $trainingAmount }})</div>
                                    <strong>
                                    <br>
                                    <strong>Type: </strong>{{ $pop->is_package ? 'Package' : 'Training' }}

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
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

                                                            <input
                                                                type="date"
                                                                class="form-control"
                                                                id="date"
                                                                name="date"
                                                                value="{{ $pop->date ? \Carbon\Carbon::parse($pop->date)->format('Y-m-d') : '' }}"
                                                            >
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="name" class="form-label">Name</label>

                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="name"
                                                                name="name"
                                                                value="{{ $pop->name }}"
                                                            >
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="phone" class="form-label">Phone</label>

                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="phone"
                                                                name="phone"
                                                                value="{{ $pop->phone }}"
                                                            >
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="email" class="form-label">Email</label>

                                                            <input
                                                                type="email"
                                                                class="form-control"
                                                                id="email"
                                                                name="email"
                                                                value="{{ $pop->email }}"
                                                            >
                                                        </div>

                                                        @if($pop->temp)
                                                            <div class="col-md-12 mb-3">

                                                                <label for="transId" class="form-label">
                                                                    Transaction ID
                                                                </label>

                                                                <div class="d-flex align-items-center gap-3">

                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        id="transId"
                                                                        name="transId"
                                                                        value="{{ $pop->temp->transid }}"
                                                                    >

                                                                    <div class="form-check mt-2 flex-shrink-0">
                                                                        <input
                                                                            class="form-check-input"
                                                                            type="checkbox"
                                                                            value="1"
                                                                            id="delete_transaction_{{ $pop->id }}"
                                                                            name="delete_transaction"
                                                                        >

                                                                        <label
                                                                            class="form-check-label text-danger"
                                                                            for="delete_transaction_{{ $pop->id }}"
                                                                        >
                                                                            Delete Transaction
                                                                        </label>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        @endif

                                                        <div class="col-md-6 mb-3">
                                                            <label for="amount" class="form-label">Amount</label>

                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="amount"
                                                                name="amount"
                                                                value="{{ $pop->amount }}"
                                                            >
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <label for="location" class="form-label">Location</label>

                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="location"
                                                                name="location"
                                                                value="{{ $pop->location }}"
                                                            >
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Training/Package?</label>

                                                            <select name="is_package" class="form-control pop-type-toggle" data-pop-id="{{ $pop->id }}">
                                                                <option value="">Select</option>

                                                                <option value="0" {{ !$pop->is_package ? 'selected' : '' }}>
                                                                    Training
                                                                </option>

                                                                <option value="1" {{ $pop->is_package ? 'selected' : '' }}>
                                                                    Package
                                                                </option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Payment Type</label>

                                                            <select name="payment_type" class="form-control">
                                                                <option value="">Select payment type</option>
                                                                <option value="full" {{ $pop->payment_type === 'full' ? 'selected' : '' }}>Full</option>
                                                                <option value="part" {{ $pop->payment_type === 'part' ? 'selected' : '' }}>Part</option>
                                                                <option value="earlybird" {{ $pop->payment_type === 'earlybird' ? 'selected' : '' }}>Early Bird</option>
                                                            </select>
                                                            @if($pop->payment_type === 'earlybird')
                                                                <small class="text-muted d-block mt-1">
                                                                Auto Detect                                                                </small>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-12 mb-3 pop-package-row" id="pop-package-row-{{ $pop->id }}">
                                                            <label class="form-label">Package</label>

                                                            <select
                                                                name="group_id"
                                                                id="group_id_{{ $pop->id }}"
                                                                class="form-control"
                                                            >
                                                                <option value="">Select</option>

                                                                @foreach($packages as $package)
                                                                    <option
                                                                        value="{{ $package->id }}"
                                                                        {{ $related && $package->id == $related->id ? 'selected' : '' }}
                                                                    >
                                                                        {{ $package->p_name }}
                                                                        ({{ $package->p_amount }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-md-12 mb-3 pop-training-row" id="pop-training-row-{{ $pop->id }}">
                                                            <label class="form-label">Training</label>

                                                            <select
                                                                name="program_id"
                                                                id="program_id_{{ $pop->id }}"
                                                                class="form-control"
                                                            >
                                                                <option value="">Select</option>

                                                                @foreach($programs as $program)
                                                                    <option
                                                                        value="{{ $program->id }}"
                                                                        {{ $related && $program->id == $related->id ? 'selected' : '' }}
                                                                    >
                                                                        {{ $program->p_name }}
                                                                        ({{ $program->p_amount }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        @if(!empty($pop->temp->coupon_id))
                                                            <div class="col-md-12 mb-3">

                                                                <label class="form-label">
                                                                    Coupon Attached
                                                                </label>

                                                                <select class="form-control" disabled>
                                                                    @if($related)
                                                                        @foreach ($related->coupon as $coupon)
                                                                            <option
                                                                                value="{{ $coupon->id }}"
                                                                                {{ $coupon->id == $pop->temp->coupon_id ? 'selected' : '' }}
                                                                            >
                                                                                {{ $coupon->code }}
                                                                            </option>
                                                                        @endforeach
                                                                    @else
                                                                        <option value="">No linked training/package</option>
                                                                    @endif
                                                                </select>

                                                            </div>
                                                        @endif

                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
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
@endsection

@section('scripts')
<script>
    (function () {
        function togglePopSelectors(popId) {
            const typeSelect = document.querySelector(`.pop-type-toggle[data-pop-id="${popId}"]`);
            const packageRow = document.getElementById(`pop-package-row-${popId}`);
            const trainingRow = document.getElementById(`pop-training-row-${popId}`);

            if (!typeSelect || !packageRow || !trainingRow) {
                return;
            }

            const isPackage = typeSelect.value === '1';

            packageRow.style.display = isPackage ? '' : 'none';
            trainingRow.style.display = isPackage ? 'none' : '';
        }

        document.addEventListener('change', function (event) {
            if (event.target.classList.contains('pop-type-toggle')) {
                togglePopSelectors(event.target.dataset.popId);
            }
        });

        document.addEventListener('shown.bs.modal', function (event) {
            const modal = event.target;
            const typeSelect = modal.querySelector('.pop-type-toggle');

            if (typeSelect) {
                togglePopSelectors(typeSelect.dataset.popId);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.pop-type-toggle').forEach(function (select) {
                togglePopSelectors(select.dataset.popId);
            });
        });
    })();
</script>
@endsection
