@extends('dashboard.admin.index')
@section('title', 'Payment modes')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Blacklist 
                        <a href="{{route('blacklist.create')}}"><button type="button" class="btn btn-outline-primary"> Add Value</button></a>
                    </h5>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Added By</th>
                            <th>Date</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blacklists as $blacklist)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $blacklist->value }}</td>
                            <td>
                                @if($blacklist->status == 1)
                                    <button class="btn btn-success btn-sm">Active</button>
                                @else
                                    <button class="btn btn-danger btn-sm">Inactive</button>
                                @endif
                            </td>
                            <td>
                                {{ \Illuminate\Support\Str::limit($blacklist->reason, 20, '...') }}
                            </td>
                            <td>
                                {{ $blacklist->addedBy->name ?? 'System' }}
                            </td>
                            <td>
                                {{ $blacklist->created_at }}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Blacklist Entry"
                                        class="btn btn-sm btn-info"
                                        href="{{ route('blacklist.edit', $blacklist->id) }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('blacklist.destroy', $blacklist->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                data-toggle="tooltip" data-placement="top" title="Delete record">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Export Scripts --}}
                <script type="text/javascript" src="{{ asset('src/jspdf.min.js') }}"></script>
                <script type="text/javascript" src="{{ asset('src/jspdf.plugin.autotable.min.js') }}"></script>
                <script type="text/javascript" src="{{ asset('src/tableHTMLExport.js') }}"></script>
                <script type="text/javascript">
                    $("#csv").on("click", function () {
                        $("#zero_config").tableHTMLExport({
                            type: 'csv',
                            filename: 'Blacklist.csv'
                        });
                    });
                </script>
            </div>

        </div>
    </div>
</div>
@endsection