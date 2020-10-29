@extends('dashboard.admin.index')
@section('title', 'All Results')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                @include('layouts.partials.alerts')
                <div class="card-header">
                    <div>
                        <h5 class="card-title"> All Results for: {{ $program_name }} </h5><br><button class="btn btn-success" id="csv">Export Results</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>Cert. Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin')<th>C.T. Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<th>R. Play Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<th>Email Score</th>@endif
                            <th>Passmark</th>
                            @if(auth()->user()->role_id == 'Admin')<th>T. Score</th>@endif
                           <th>Facilitator</th>
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>Grader</th>@endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @if($user->passmark)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{isset($user->updated_at) ? $user->updated_at->format('d/m/Y') : ''}}</td>
                            <td>{{ $user->name }}</td>
                             @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<td>{{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin')<td>{{ $user->final_ct_score }}%</td>@endif
                             @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<td>{{ $user->total_role_play_score }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<td>{{ $user->total_email_test_score }}%</td>@endif
                            <td>{{ $user->passmark }}%</td>
                            @if(auth()->user()->role_id == 'Admin')<td>{{ $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score }}%</td>@endif
                           <th>{{ $user->marked_by }}</th>
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>{{ $user->grader }}</th>@endif
                            {{-- <th> {{ $user->cl_module_count}}</th> --}}
                          
                            <td>
                                @if( $user->result_id )
                                    <div class="btn-group">
                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                            class="btn btn-info" href="{{ route('results.add', ['uid' => $user->user_id, 'result' => $user->result_id]) }}"><i
                                                class="fa fa-eye"></i>
                                        </a>

                                            <form action="{{ route('results.destroy', $user->result_id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}
                                            <input type="hidden" name="id" value="{{ $user->result_id }}">
                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete Result"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    N/A
                                @endif
                            </td>
                            @endif
                            @endforeach
                    </tbody>
                </table>
               
                    
                    {{-- <script type="text/javascript" src="{{ asset('src/jquery-3.3.1.slim.min.js') }}"></script> --}}
                    
                    <script type="text/javascript" src="{{ asset('src/jspdf.min.js')}} "></script>
                    
                    <script type="text/javascript" src="{{ asset('src/jspdf.plugin.autotable.min.js'
                    )}}"></script>
                    
                    <script type="text/javascript" src="{{ asset('src/tableHTMLExport.js')}}"></script>
                    
                    <script type="text/javascript">
                      
                     
                      $("#csv").on("click",function(){
                        $("#zero_config").tableHTMLExport({
                          type:'csv',
                          filename:'Results.csv'
                        });
                      });
                    
                    </script>
            </div>

        </div>
    </div>
</div>

@endsection