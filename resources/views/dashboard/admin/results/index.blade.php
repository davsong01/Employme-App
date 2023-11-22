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
                        <h5 class="card-title"> All Results for: {{ $program_name }} </h5><br>
                        <button class="btn btn-success" id="csv">Export Results</button>
                      
                    </div>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Details</th>
                            <th>Scores</th>
                            <th>Passmark</th>
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))<th>Total</th>@endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @if($user->passmark)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>
                                <strong class="tit">Name: </strong>{{ $user->name }} 
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) ) <br>
                                <strong class="tit">Email: </strong>{{ $user->email }} <br>
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))<br> <strong class="tit">Marked by: </strong> {{ $user->marked_by }}@endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) <br> <strong class="tit">Graded by: </strong> {{ $user->grader }}@endif <br>
                                <small> Last updated on: {{isset($user->updated_at) ?  \Carbon\Carbon::parse($user->updated_at)->format('jS F, Y, h:iA')  : ''}}</small>
                                @endif
                                <br>
                                Certificate Access : @if(isset($user->cert))
                                    @if($user->cert->show_certificate == 1)
                                    <strong style="color:green">Enabled</strong>
                                    @else
                                    <strong style="color:red">Disabled</strong>
                                    @endif
                                @else
                                Not Uploaded
                                @endif 
                            </td>
                            <td>
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                    @if(isset($score_settings->certification) && $score_settings->certification > 0)
                                    <strong>Certification: </strong> {{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}% 
                                    @endif
                                @endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))

                                @if(isset($score_settings->class_test) && $score_settings->class_test > 0)
                                    <br><strong class="tit">Class Tests:</strong> {{ $user->final_ct_score }}% <br> @endif
                                @endif

                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))
                                    @if(isset($score_settings->role_play) && $score_settings->role_play > 0)
                                    <strong class="tit">Role Play: </strong> {{ $user->total_role_play_score }}%  <br> 
                                    @endif
                                @endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                    @if(isset($score_settings->email) && $score_settings->email > 0)
                                        <strong>Email: </strong> {{ $user->total_email_test_score }}% @endif
                                    @endif
                                <?php
                                    $total = $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score;
                                ?>
                            </td>
                            <td><strong class="tit" style="color:blue">{{ $user->passmark }}%</strong> </td>
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                            <td>
                                 <strong class="tit" style="color:{{ $total < $user->passmark ? 'red' : 'green'}}">{{ $total }}%</strong> 
                            </td>
                            @endif
                            {{-- @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))<br>{{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}%@endif
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))<td>{{ $user->final_ct_score }}%</td>@endif
                             @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))<td>{{ $user->total_role_play_score }}%</td>@endif
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))<td>{{ $user->total_email_test_score }}%</td>@endif
                            <td>{{ $user->passmark }}%</td>
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))<td>{{ $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score }}%</td>@endif
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))<th>{{ $user->marked_by }}</th>@endif
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))<th>{{ $user->grader }}</th>@endif --}}
                            {{-- <th> {{ $user->cl_module_count}}</th> --}}
                            
                            <td>
                                @if( isset($user->result_id)) 
                                    <div class="btn-group">
                                        @if($user->redotest == 0 && !empty($user->certification_test_details))
                                            <a class="btn btn-info btn-sm" href="{{ route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id]) }}"><i
                                                    class="fa fa-eye"> View/Update </i>
                                            </a>
                                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                            <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="{{ route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ]) }}" method="POST">
                                                {{ csrf_field() }}
                                                {{method_field('DELETE')}}
                                                <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                                <input type="hidden" name="rid" value="{{ $user->result_id }}">
                                                <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                                <button type="submit" class="btn btn-danger btn-xsm"> <i
                                                        class="fa fa-redo"> Enable Resit</i>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                            @if($user->redotest != 0)
                                            <a onclick="return confirm('This will stop this this user from access to take retest certification test/ Are you sure you want to do this?');" data-toggle="tooltip" data-placement="top" title="Stop user from retaking certification test"
                                                class="btn btn-warning" href="{{ route('stopredotest',['user_id'=>$user->user_id, 'result_id'=>$user->result_id]) }}"><i
                                                    class="fa fa-stop"></i>
                                            </a>
                                            @endif
                                    
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
<script>

</script>
@endsection