@extends('dashboard.admin.index')
@section('title', 'Training Locations')
@section('content')
@php
    $totalLocations = method_exists($locations, 'total') ? $locations->total() : $locations->count();
@endphp
<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Training Locations</h1>
                            <p class="text-muted mb-0">Manage the venues used when students purchase training sessions.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalLocations }} locations</span>
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
                <div class="text-muted small">Locations appear on the frontend during training purchase.</div>
                <a href="{{ route('locations.create') }}" class="btn btn-outline-primary">Add New Location</a>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Training</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Name">
                                <div class="fw-semibold">{{ $location->title }}</div>
                            </td>
                            <td data-label="Training">
                                {{ $location->program->p_name }}
                            </td>
                            <td class="text-end" data-label="Actions">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit location" class="btn btn-outline-primary btn-sm" href="{{ route('locations.edit', $location->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete location">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('locations.edit', $location->id) }}"><i class="fa fa-edit me-2"></i>Edit Location</a></li>
                                        <li>
                                            <form action="{{ route('locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash me-2"></i>Delete Location
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
