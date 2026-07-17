@extends('dashboard.admin.index')
@section('title', 'Currencies')
@section('content')
@php
    $totalCurrencies = method_exists($currencies, 'total') ? $currencies->total() : $currencies->count();
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Currencies</h1>
                            <p class="text-muted mb-0">Maintain supported currencies and their conversion rates for the platform.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalCurrencies }} currencies</span>
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
                <div class="text-muted small">Currencies are used for payment display and conversion across the app.</div>
                <a href="{{ route('currency.create') }}" class="btn btn-outline-primary">Add Payment Currency</a>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Country Name</th>
                            <th>Conversion Rate</th>
                            <th>Symbol</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $currency)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Name">{{ $currency->name }}</td>
                            <td data-label="Country Name">{{ $currency->country_name }}</td>
                            <td data-label="Conversion Rate">{{ $currency->conversion_rate }}</td>
                            <td data-label="Symbol">{{ $currency->symbol }}</td>
                            <td data-label="Status">
                                <span class="badge {{ $currency->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $currency->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end" data-label="Action">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit currency" class="btn btn-outline-primary btn-sm" href="{{ route('currency.edit', $currency->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </div>
                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('currency.edit', $currency->id) }}"><i class="fa fa-edit me-2"></i>Edit Currency</a></li>
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
