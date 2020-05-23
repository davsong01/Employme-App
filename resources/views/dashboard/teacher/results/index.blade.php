@extends('dashboard.teacher.index')
@section('title', 'All Results')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
                @if(session()->get('message'))
                <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> {{ session()->get('message')}}
                      </div>
                @endif
            <h5 class="card-title">All Results</h5>
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
                            <th>Action</th>
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
                            <td style="color:{{ $result->status == 'CERTIFIED' ? 'green' : 'red'}}"><b>{{ $result->status}}</b></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                        data-toggle="dropdown">Action
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">

                                        <li><i class="far fa-eye"></i><a href="{{ route('results.show', $result->user_id) }}"> View Result</a></li>
                                        <li><i class="far fa-edit"></i><a href="{{ route('results.edit', $result->user_id) }}"> Edit</a></li>
                                        <li>
                                        <form class="delete" action="{{ route('results.destroy', $result->user->id) }}" method="POST">
                                                <i class="fas fa-trash"></i>
                                                <input type="hidden" name="_method" value="DELETE">
                                                {{ csrf_field() }}
                                                <input type="submit" class="custombutton" value="Delete">
                                            </form> </li>
                                    </ul>
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
                                <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
@endsection