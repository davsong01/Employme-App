@php
    $check = [
        'coupon.create',
        'coupon.edit',
        'coupon.destroy',
        'coupon.show'
    ];

    $permissions = canUserAccessPermission($check);
    $totalCoupons = method_exists($coupons, 'total') ? $coupons->total() : $coupons->count();
@endphp

@extends('dashboard.admin.index')
@section('title', 'All Coupons')
@section('content')
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Coupons</h1>
                            <p class="text-muted mb-0">Track coupon usage, review who created each code, and manage coupon lifecycle from one place.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalCoupons }} coupons</span>
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
                <div class="text-muted small">Use this table to manage promotional coupons and view usage history.</div>
                @if($permissions['coupon.create'])
                    <a href="{{ route('coupon.create') }}" class="btn btn-outline-primary">Add New Coupon</a>
                @endif
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Amount</th>
                            <th>Training</th>
                            <th>Created by</th>
                            <th>Usage count</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Code">
                                <div class="fw-semibold">{{ $coupon->code }}</div>
                            </td>
                            <td data-label="Amount">
                                {{ $coupon->type === 'fixed' ? currency() . number_format($coupon->amount) : number_format($coupon->amount) . '%' }}
                            </td>
                            <td data-label="Training">
                                {{ isset($coupon->program_id) ? $coupon->program->p_name : ($coupon->group->p_name ?? 'NOT SET') }}
                                <div class="text-muted small">{{ isset($coupon->program_id) ? 'Training' : 'Package' }}</div>
                            </td>
                            <td data-label="Created by">
                                {{ isset($coupon->facilitator->name) ? $coupon->facilitator->name : 'Administrator' }}
                            </td>
                            <td data-label="Usage count">
                                {{ $coupon->coupon_users->count() }}
                            </td>
                            <td class="text-end" data-label="Actions">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    @if($permissions['coupon.edit'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Edit coupon details" class="btn btn-outline-primary btn-sm" href="{{ route('coupon.edit', $coupon->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endif
                                    @if($permissions['coupon.show'])
                                        <a data-bs-toggle="tooltip" data-placement="top" title="View coupon usage" class="btn btn-outline-info btn-sm" href="{{ route('coupon.show', $coupon->id) }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($permissions['coupon.destroy'])
                                        <form action="{{ route('coupon.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete coupon">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($permissions['coupon.edit'])
                                            <li><a class="dropdown-item" href="{{ route('coupon.edit', $coupon->id) }}"><i class="fa fa-edit me-2"></i>Edit Coupon</a></li>
                                        @endif
                                        @if($permissions['coupon.show'])
                                            <li><a class="dropdown-item" href="{{ route('coupon.show', $coupon->id) }}"><i class="fa fa-eye me-2"></i>View Usage</a></li>
                                        @endif
                                        @if($permissions['coupon.destroy'])
                                            <li>
                                                <form action="{{ route('coupon.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash me-2"></i>Delete Coupon
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
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
