@extends('dashboard.admin.index')
@section('title', 'All Users')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Students <a href="{{route('users.create')}}"><button type="button"
                        class="btn btn-outline-primary">Add New Student</button></a><button class="btn btn-success" id="csv">Export Students</button></h5>
                </div>
            </div>

            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Details</th>
                            {{-- <th>Avatar</th> --}}
                            <th>Trainings</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        
                        <tr>
                            
                            <td>{{ $i++ }}</td>
                            <td> <strong>Name:</strong> {{ $user->name }} <br>
                                <strong>Email: </strong>{{$user->email}} <br>
                                <strong>Phone: </strong>{{ $user->t_phone}} <br>
                                <strong>Account Balance:</strong> {{number_format($user->account_balance)}} <br>
                                <strong>Date Added:</strong> {{ $user->created_at->format('d/m/Y') }} <br>
                                <strong>Trainings count: </strong>{{ $user->programs()->count() }}
                            </td>
                            {{-- <td> <img src="{{ asset('/avatars/'.$user->profile_picture) }}" alt="avatar" style="width: 80px;border-radius: 50%; height: 80px;"> </td>  --}}

                            <b style="display:none">{{ $count = 1 }}</b>
                            <td>
                                {{ $user->name }} <br>
                                @foreach($user->programs as $programs)
                                    <small style="color:green">{{ $count ++ }}.
                                    {{ $programs->p_name }} <br></small>
                                    <hr style="margin-top: 2px; margin-bottom: 2px; border-top: 1px solid rgb(34, 85, 164);">
                                @endforeach
                            </td> 
                           
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit User"
                                        class="btn btn-info btn-sm" href="{{ route('users.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                        class="btn btn-warning btn-sm" href="{{ route('impersonate', $user->id) }}"><i
                                            class="fa fa-unlock"></i>
                                    </a>
                                    {{-- @if($user->redotest === 0)
                                    <a  onclick="return confirm('Are you really sure?');" data-toggle="tooltip" data-placement="top" title="Allow to retake Test"
                                        class="btn btn-success" href="{{ route('redotest', $user->id) }}"><i class="fa fa-redo" aria-hidden="true"></i>
                                    </a>
                                    @else
                                    <a onclick="return confirm('Are you really sure?');" data-toggle="tooltip" data-placement="top" title="Stop from retaking Test"
                                        class="btn btn-primary" href="{{ route('stopredotest', $user->id) }}"><i class="fa fa-stop" aria-hidden="true"></i>
                                    </a>
                                    @endif --}}
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                            @endforeach
                    </tbody>
                </table>
                <script type="text/javascript" src="{{ asset('src/jspdf.min.js')}} "></script>
                    
                    <script type="text/javascript" src="{{ asset('src/jspdf.plugin.autotable.min.js'
                    )}}"></script>
                    
                    <script type="text/javascript" src="{{ asset('src/tableHTMLExport.js')}}"></script>
                    
                    <script type="text/javascript">
                                           
                      $("#csv").on("click",function(){
                        $("#zero_config").tableHTMLExport({
                          type:'csv',
                          filename:'Participants.csv'
                        });
                      });
                    
                    </script>
            </div>

        </div>
    </div>
</div>
@endsection