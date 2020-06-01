@extends('dashboard.teacher.index')
@section('title', 'All Users')
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
            <h5 class="card-title">All Students</h5>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Role</th>
                            <th>Bank</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->t_phone}}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->program->p_name }}</td>
                            <td>{{ $user->role_id }}</td>
                            <td>{{ $user->bank }}</td>
                            <td>{{ $user->t_location }}</td>
                            
                            @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
<script>
        $(".delete").on("submit", function(){
            return confirm("Are you sure?");
        });
    </script>
@endsection