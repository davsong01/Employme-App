@extends('dashboard.admin.index')
@section('title', 'All Results')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                @include('layouts.partials.alerts')
                <h5>All Results</h5>
            </div>
           
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $result->created_at->format('d/m/Y') }}</td>
                            <td>{{ $result->user->name }}</td>
                            <td>{{ $result->user->email }}</td>
                            <td>{{ $result->user->program->p_name }}</td>
                            <td>{{ $result->total }}</td>
                            <td style="color:{{ $result->status == 'CERTIFIED' ? 'green' : 'red'}}">
                                <b>{{ $result->status}}</b></td>
                            <td>
                                <div class="btn-group">

                                    <a data-toggle="tooltip" data-placement="top" title="View Result"
                                        class="btn btn-info" href="{{ route('results.show', $result->user_id) }}"><i
                                            class="fa fa-eye"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Result"
                                    class="btn btn-warning" href="{{ route('results.edit', $result->user_id) }}"><i
                                        class="fa fa-edit"></i>
                                </a>
                                        <form action="{{ route('results.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete Result"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                </div>
                            </td>
                            @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection