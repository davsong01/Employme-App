@extends('dashboard.admin.index')

@section('title', 'Cron Tasks')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Cron Tasks</h5>
                <small class="text-muted">Track and manage utility cron jobs from one place.</small>
            </div>
        </div>

        <div class="card-body">
            @include('layouts.partials.alerts')

            <form method="GET" action="{{ route('admin.utility-cron-tasks.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or key">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.utility-cron-tasks.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">S/N</th>
                            <th>Name</th>
                            <th style="width: 180px;">Status</th>
                            <th style="width: 280px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>{{ $loop->iteration + (($tasks->currentPage() - 1) * $tasks->perPage()) }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $task->name }}</div>
                                    <small class="text-muted">{{ $task->key }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($task->status) {
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                            'pending' => 'warning',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($task->status ?? 'pending') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-center">
                                        @if($task->status === 'failed')
                                            <form action="{{ route('admin.utility-cron-tasks.retry', $task) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Retry</button>
                                            </form>
                                        @endif

                                        @if($task->status === 'pending')
                                            <form action="{{ route('admin.utility-cron-tasks.try-now', $task) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Try Now</button>
                                            </form>
                                        @endif

                                        @if($task->status === 'completed')
                                            <form action="{{ route('admin.utility-cron-tasks.mark-pending', $task) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Completed to Pending</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.utility-cron-tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-dark">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No cron tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
