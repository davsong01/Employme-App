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
                        <h5 class="card-title"> All Results <button class="btn btn-success" id="csv">Export Results</button></h5>
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
                            <th>Training</th>
                            <th>Cert. Score</th>
                            <th>C.T. Score</th>
                            <th>R. Play Score</th>
                            <th>Email Score</th>
                            <th>Passmark</th>
                            <th>T. Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @if($user->program->scoresettings)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->program->p_name }}</td>
                            <td>{{ $user->total_cert_score}}%</td>
                            <td>{{ $user->test_score}}%</td>
                            <td>{{ $user->total_role_play_score }}%</td>
                            <td>{{ $user->total_email_test_score }}%</td>
                            <td>{{ $user->program->scoresettings->passmark }}%</td>
                            <td>{{ $user->total_cert_score + $user->test_score + $user->total_role_play_score + $user->total_email_test_score }}%</td>
                            
                            <td>
                                @if($user->result_id)
                                    <div class="btn-group">

                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                            class="btn btn-info" href="{{ route('results.add', ['uid' => $user->id, 'modid' => $user->result_id]) }}"><i
                                                class="fa fa-eye"></i>
                                        </a>
                                        {{-- <a data-toggle="tooltip" data-placement="top" title="Update User scores"
                                        class="btn btn-warning" href="{{ route('results.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                        </a> --}}
                                            <form action="{{ route('results.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

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