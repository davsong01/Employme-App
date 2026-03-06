@extends('dashboard.admin.index')
@section('title', 'Error Log Files')
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
    td, th {
        white-space: normal;
        word-break: break-word;
    }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Error Log Files</h5>
            <form action="{{ route('admin.error-files.deleteAll') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all error log files?');">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Delete All Files</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div style="padding: 5px 10px;">
                @include('layouts.partials.alerts')
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Last Modified</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @forelse($files as $file)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $file['name'] }}</td>
                                <td>{{ $file['size'] }}</td>
                                <td>{{ $file['modified'] }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.error-files.download', $file['name']) }}" class="btn btn-info btn-sm" title="Download"><i class="fa fa-download"></i></a>
                                    <form action="{{ route('admin.error-files.delete', $file['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No error log files found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection