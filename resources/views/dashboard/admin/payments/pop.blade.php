@extends('dashboard.admin.index')
@section('title', 'Proofs of Payment')
@section('css')
    <style>
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

        .pop-count-badge {
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
    </style>
@endsection
@section('content')

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Payment History</h1>
                            <p class="text-muted mb-0">Browse attempted payments, filter results, and work through them in batches of 100.</p>
                        </div>
                        <div class="pop-count-badge">{{ $records }}</div>
                    </div>
                    @include('layouts.partials.alerts')
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="search-form" method="GET" action="{{ route('pop.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="transid" class="form-label small text-uppercase fw-semibold text-muted">Transaction ID</label>
                        <input type="text" class="form-control" name="transid" id="transid" value="{{ request('transid') }}" placeholder="Enter Transaction ID">
                    </div>
                    <div class="col-md-3">
                        <label for="name" class="form-label small text-uppercase fw-semibold text-muted">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ request('name') }}" placeholder="Enter Name">
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label small text-uppercase fw-semibold text-muted">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ request('email') }}" placeholder="Enter Email">
                    </div>
                    <div class="col-md-3">
                        <label for="phone" class="form-label small text-uppercase fw-semibold text-muted">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ request('phone') }}" placeholder="Enter Phone">
                    </div>
                    <div class="col-md-3">
                        <label for="channel" class="form-label small text-uppercase fw-semibold text-muted">Channel</label>
                        <select name="channel" id="channel" class="form-control">
                            <option value="">Select Channel</option>
                            <option value="Online" {{ request('channel') == 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="Transfer" {{ request('channel') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_type" class="form-label small text-uppercase fw-semibold text-muted">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-control">
                            <option value="">Select Type</option>
                            <option value="part" {{ request('payment_type') == 'part' ? 'selected' : '' }}>Part Payment</option>
                            <option value="full" {{ request('payment_type') == 'full' ? 'selected' : '' }}>Full Payment</option>
                            <option value="earlybird" {{ request('payment_type') == 'earlybird' ? 'selected' : '' }}>Early Bird</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="program_id" class="form-label small text-uppercase fw-semibold text-muted">Training</label>
                        <select name="program_id" id="program_id" class="form-control">
                            <option value="">Select Training</option>
                            @foreach($allPrograms as $training)
                                <option value="{{ $training->id }}" {{ request('program_id') == $training->id ? 'selected' : '' }}>
                                    {{ $training->p_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="package_id" class="form-label small text-uppercase fw-semibold text-muted">Package</label>
                        <select name="package_id" id="package_id" class="form-control">
                            <option value="">Select Package</option>
                            @foreach($allPackages as $package)
                                <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->p_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="coupon_id" class="form-label small text-uppercase fw-semibold text-muted">Coupon</label>
                        <select name="coupon_id" id="coupon_id" class="form-control">
                            <option value="">Select Coupon</option>
                            @foreach($allCoupons as $coupon)
                                <option value="{{ $coupon->id }}" {{ request('coupon_id') == $coupon->id ? 'selected' : '' }}>
                                    {{ $coupon->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="from" class="form-label small text-uppercase fw-semibold text-muted">From</label>
                        <input type="date" class="form-control" name="from" id="from" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label small text-uppercase fw-semibold text-muted">To</label>
                        <input type="date" class="form-control" name="to" id="to" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-3 d-grid align-self-end">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
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
                        @foreach($transactions as $transaction)
                            @if($transaction->program)
                                <tr>
                                    <td>{{ paginationIndex($transactions, $loop) }}</td>
                                    <td>{{ $transaction->created_at }}</td>
                                    <td>
                                        {{ $transaction->name }} <br>
                                        {{ $transaction->phone }} <br>
                                        {{ $transaction->email }} <br>
                                    </td>
                                    <td>
                                        {{ \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($transaction->amount) }}
                                    </td>
                                    <td>
                                        {{ $transaction->program->p_name }} <br>
                                        ({{ $transaction->program->e_amount <= 0 ? 'Amount: '.\App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$transaction->program->p_amount : 'E/Amount '. \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$transaction->program->e_amount }})
                                        <br>
                                        @if(!is_null($transaction->coupon_code))
                                            <span style="color:blue">
                                                <strong>Coupon ({{ $transaction->coupon }}) Applied | {{ \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($transaction->coupon->coupon_amount) }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->bank }}</td>
                                    <td>{{ $transaction->location }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirm('Are you really sure?');"
                                                class="btn btn-danger" href="{{ route('temp.destroy', $transaction->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
