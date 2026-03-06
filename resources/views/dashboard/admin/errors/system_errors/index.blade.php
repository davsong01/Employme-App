@extends('dashboard.admin.index')
@section('title', 'System Errors')
@section('content')
<style>
    .badge {
        display: flex;
        justify-content: center;
        align-items: center;
        width: unset !important;
        height: unset !important;
        border-radius: 10%;
    }
</style>
<div class="container-fluid">
    <div class="row">
        {{-- Left: Recent Logs --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent System Errors</h5>
                    <form action="{{ route('admin.errors.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all database logs?');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Clear All Logs</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div style="padding: 5px 10px;">
                        @include('layouts.partials.alerts')
                    </div>

                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Level</th>
                                <th style="width: 20%;">Message</th>
                                <th style="width: 20%;">Context</th>
                                <th style="width: 25%;">Stack Trace</th>
                                <th>Source</th>
                                <th>Logged At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = ($logs->currentPage()-1) * $logs->perPage() + 1; @endphp
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    @php
                                        $levelColors = [
                                            'EMERGENCY' => 'bg-dark',
                                            'ALERT'     => 'bg-danger',
                                            'CRITICAL'  => 'bg-danger',
                                            'ERROR'     => 'bg-danger',
                                            'WARNING'   => 'bg-warning text-dark',
                                            'NOTICE'    => 'bg-info text-dark',
                                            'INFO'      => 'bg-primary',
                                            'DEBUG'     => 'bg-secondary',
                                        ];
                                        $badgeClass = $levelColors[$log->level] ?? 'bg-light text-dark';
                                        @endphp

                                    <td><span class="badge {{ $badgeClass }}">{{ $log->level }}</span></td>
                                    <td style="white-space: normal; word-break: break-word;">
                                        {{ Str::limit($log->message, 150) }} 
                                        
                                        <a href="{{ route('admin.error.delete', $log->id) }}" onclick="return(confirm('Are you sure'))" class="btn btn-dark btn-sm">Delete</button>

                                    </td>
                                    <td style="white-space: normal; word-break: break-word;">
                                        @if(!empty($log->context))
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#contextModal{{ $log->id }}">View</button>

                                            {{-- Context Modal --}}
                                            <div class="modal fade" id="contextModal{{ $log->id }}" tabindex="-1" aria-labelledby="contextModalLabel{{ $log->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="contextModalLabel{{ $log->id }}">Context for Log #{{ $log->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <pre style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="white-space: normal; word-break: break-word;">
                                        @if($log->stack_trace)
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#stackModal{{ $log->id }}">View</button>

                                            {{-- Stack Trace Modal --}}
                                            <div class="modal fade" id="stackModal{{ $log->id }}" tabindex="-1" aria-labelledby="stackModalLabel{{ $log->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="stackModalLabel{{ $log->id }}">Stack Trace for Log #{{ $log->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <pre>{{ $log->stack_trace }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->source }}</td>
                                    <td>{{ $log->logged_at instanceof \Carbon\Carbon ? $log->logged_at->format('d/m/Y H:i:s') : \Carbon\Carbon::parse($log->logged_at)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end p-2">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Top Recurring Errors --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Recurring Errors</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 75%;">Error Message</th>
                                <th class="text-center" style="width: 25%;">Occurrences</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recurring as $error)
                                <tr>
                                    <td style="white-space: normal; word-break: break-word;">{{ $error->message }}</td>
                                    <td class="text-center"><span class="badge bg-primary rounded-pill">{{ $error->total }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No recurring errors found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection