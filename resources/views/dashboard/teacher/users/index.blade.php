@extends('dashboard.teacher.index')
@section('title', 'All Users')
@section('content')
<?php 
    $menus = Auth::user()->menu_permissions ?? [];
    if($menus){
        $menus = explode(',',$menus);
    }else{
        $menus = [];
    }
?>

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
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Trainings</th>
                            @if(in_array(20, $menus))
                            <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td> <img src="{{ asset('/avatars/'.$user->user->profile_picture) }}" alt="avatar" style="width: 80px;border-radius: 50%; height: 80px;"> </td> 
                            <b style="display:none">{{ $count = 1 }}</b>
                           
                            <td>{{ $user->user->name }}<br>
                               <span style="color:blue">{{$user->user->email }}</span> <br>
                               {{ $user->user->t_phone}} <br>
                                @foreach($user->user->programs as $programs)
                                    <small style="color:green">{{ $count ++ }}.
                                    {{ $programs->p_name }} <br></small>
                                    <hr style="margin-top: 2px; margin-bottom: 2px; border-top: 1px solid rgb(34, 85, 164);">
                                @endforeach
                            </td>
                            <td>{{ $user->user->programs->count() }}</td>
                            @if(in_array(20, $menus))
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit User"
                                        class="btn btn-info" href="{{ route('users.edit', $user->user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                            @endif
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