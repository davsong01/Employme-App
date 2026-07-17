@extends('dashboard.admin.index')
@section('title', 'Blacklist')
@section('content')
@php
    $totalBlacklists = method_exists($blacklists, 'total') ? $blacklists->total() : $blacklists->count();
@endphp

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Blacklist</h1>
                            <p class="text-muted mb-0">Track blocked values, review the reason, and keep moderation actions easy to audit.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalBlacklists }} entries</span>
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
                <div class="text-muted small">Values in this table can be edited or removed as needed.</div>
                <a href="{{ route('blacklist.create') }}" class="btn btn-outline-primary">Add Value</a>
            </div>

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Added By</th>
                            <th>Date</th>
                            <th class="text-end">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blacklists as $blacklist)
                        <tr>
                            <td data-label="#">
                                {{ paginationIndex($blacklists, $loop) }}
                            </td>
                            <td data-label="Value">{{ $blacklist->value }}</td>
                            <td data-label="Status">
                                <span class="badge {{ $blacklist->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $blacklist->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td data-label="Reason">{{ \Illuminate\Support\Str::limit($blacklist->reason, 20, '...') }}</td>
                            <td data-label="Added By">{{ $blacklist->addedBy->name ?? 'System' }}</td>
                            <td data-label="Date">{{ $blacklist->created_at }}</td>
                            <td class="text-end" data-label="Manage">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit Blacklist Entry" class="btn btn-outline-primary btn-sm" href="{{ route('blacklist.edit', $blacklist->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('blacklist.destroy', $blacklist->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete record">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('blacklist.edit', $blacklist->id) }}"><i class="fa fa-edit me-2"></i>Edit Entry</a></li>
                                        <li>
                                            <form action="{{ route('blacklist.destroy', $blacklist->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-trash me-2"></i>Delete Record
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
