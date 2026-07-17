@extends('dashboard.admin.index')
@section('title', 'Payment modes')
@section('content')
@php
    $totalModes = method_exists($modes, 'total') ? $modes->total() : $modes->count();
    $default_currency = \App\Models\Settings::value('CURR_ABBREVIATION');
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Payment Modes</h1>
                            <p class="text-muted mb-0">Review processor settings, currency mapping, and payment gateway status at a glance.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalModes }} modes</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">Base: {{ $default_currency }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-3">
                <div class="text-muted small">Manage payment processors and their display details from here.</div>
                <a href="{{ route('payment-modes.create') }}" class="btn btn-outline-primary">Add Payment Mode</a>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Processor</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modes as $mode)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Image">
                                <img src="paymentmodes/{{ $mode->image }}" alt="image" class="rounded-circle" width="50" height="50">
                            </td>
                            <td data-label="Type">
                                {{ $mode->type }}
                            </td>
                            <td data-label="Name">
                                <div class="fw-semibold">{{ $mode->name }} ({{ $mode->currency_symbol }})</div>
                                <div class="text-muted small">{{ $mode->currency }} | 1 {{ $default_currency }} = {{ $mode->exchange_rate . ' ' . $mode->currency }}</div>
                                <div class="text-success small">Secret key: {{ $mode->secret_key }}</div>
                                <span class="badge {{ $mode->status == 'active' ? 'bg-success' : 'bg-danger' }} mt-2">
                                    {{ ucFirst($mode->status) }}
                                </span>
                            </td>
                            <td data-label="Processor">
                                {{ $mode->processor }}
                            </td>
                            <td class="text-end" data-label="Manage">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit payment mode" class="btn btn-outline-primary btn-sm" href="{{ route('payment-modes.edit', $mode->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('payment-modes.destroy', $mode->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete payment mode">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('payment-modes.edit', $mode->id) }}"><i class="fa fa-edit me-2"></i>Edit Mode</a></li>
                                        <li>
                                            <form action="{{ route('payment-modes.destroy', $mode->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash me-2"></i>Delete Mode
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
