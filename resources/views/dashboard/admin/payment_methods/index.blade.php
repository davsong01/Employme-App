@extends('dashboard.admin.index')
@section('title', 'Payment methods')
@section('content')
@php
    $totalMethods = method_exists($methods, 'total') ? $methods->total() : $methods->count();
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Payment Methods</h1>
                            <p class="text-muted mb-0">Manage the alternate payment method records used by the platform.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalMethods }} methods</span>
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
                <div class="text-muted small">Keep the image, name, and type fields in sync with checkout expectations.</div>
                <a href="{{ route('paymentmethod.create') }}" class="btn btn-outline-primary">Add Payment Method</a>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($methods as $method)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Image">
                                <img src="/{{ $method->image }}" alt="image" class="rounded-circle" width="50" height="50">
                            </td>
                            <td data-label="Name">{{ $method->name }}</td>
                            <td data-label="Type">{{ $method->type }}</td>
                            <td class="text-end" data-label="Manage">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit payment method" class="btn btn-outline-primary btn-sm" href="{{ route('paymentmethod.edit', $method->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('paymentmethod.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete payment method">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('paymentmethod.edit', $method->id) }}"><i class="fa fa-edit me-2"></i>Edit Method</a></li>
                                        <li>
                                            <form action="{{ route('paymentmethod.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash me-2"></i>Delete Method
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
