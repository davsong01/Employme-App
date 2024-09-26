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
                        <h5 class="card-title"> All Pretest Results for: {{ $program_name }} </h5><br>
                        <button class="btn btn-success" id="csv">Export Results</button>
                    </div>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Details</th>
                            <th>Scores</th>
                            <th>Grader Details</th>
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
                            <td><strong class="tit"></strong>{{ $user->name }} </td>
                            <td>
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) ) <br>
                                <strong class="tit"></strong>{{ $user->staffID ?? $user->email }} <br>
                                @endif
                            </td>
                            <td>
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))<br> <strong class="tit">Marked by: </strong> {{ $user->marked_by }}@endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) <br> <strong class="tit">Graded by: </strong> {{ $user->grader }}<br>

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
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                                    <?php
                                        $total = ((!empty($score_settings->class_test) && $score_settings->class_test > 0) ? $user->total_cert_score : 0 )
                                        + ((!empty($score_settings->certification) && $score_settings->certification > 0 ) ? $user->final_ct_score : 0)
                                        + ((!empty($score_settings->email) && $score_settings->email > 0 ) ? $user->total_email_test_score : 0)
                                        + ((!empty($score_settings->role_play) && $score_settings->role_play > 0) ? $user->total_role_play_score : 0) 
                                        + ((!empty($score_settings->crm_test) && $score_settings->crm_test > 0) ?  $user->total_crm_test_score : 0);
                                    ?>

                                    @if(isset($score_settings->class_test) && $score_settings->class_test > 0)
                                        <strong class="tit">Class Tests:</strong> {{ $user->final_ct_score }}% <br> @endif
                                    @endif
                                @endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                    @if(isset($score_settings->certification) && $score_settings->certification > 0)
                                    <strong>Certification: </strong> {{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}% <br>
                                    @endif
                                @endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                                    
                                    @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))
                                        @if(isset($score_settings->role_play) && $score_settings->role_play > 0)
                                        <strong class="tit">Role Play: </strong> {{ $user->total_role_play_score }}%  <br> 
                                        @endif
                                    @endif
                                    @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))
                                        @if(isset($score_settings->crm_test) && $score_settings->crm_test > 0)
                                        <strong class="tit">CRM Test: </strong> {{ $user->total_crm_test_score }}%  <br> 
                                        @endif
                                    @endif
                                    @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                        @if(isset($score_settings->email) && $score_settings->email > 0)
                                            <strong>Email: </strong> {{ $user->total_email_test_score }}% 
                                        @endif
                                    @endif
                                @endif
                            </td>
                            
                            <td><strong class="tit" style="color:blue">{{ $user->passmark }}%</strong> </td>
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                            <td>
                                <strong class="tit" style="color:{{ $total < $user->passmark ? 'red' : 'green'}}">{{ $total }}%</strong> 
                            </td>
                            @endif
                            <td>
                                @if( isset($user->result_id)) 
                                    @if($user->redotest == 0)
                                        @if (!empty($user->certification_test_details))
                                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                                    <a class="btn btn-info btn-sm" href="{{ route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id]) }}"><i
                                                            class="fa fa-eye"> View/Update </i>
                                                    </a>
                                                @endif
                                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions()))
                                                <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="{{ route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ]) }}" method="POST">
                                                    {{ csrf_field() }}
                                                    {{method_field('DELETE')}}
                                                    <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                                    <input type="hidden" name="rid" value="{{ $user->result_id }}">
                                                    <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm"> <i class="fa fa-redo"> Enable Resit</i>
                                                    </button>
                                                </form>
                                                @endif
                                        @else 
                                            <div class="btn-group">
                                            <button class="btn btn-button btn-danger btn-sm" style="display: block;" disabled>Participant did not retake a resit!</button>
                                            </div>
                                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                                <a class="btn btn-info btn-sm" href="{{ route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id]) }}"><i
                                                        class="fa fa-eye"> View/Update </i>
                                                </a>
                                            @endif
                                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions()))
                                            <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="{{ route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ]) }}" method="POST">
                                                {{ csrf_field() }}
                                                {{method_field('DELETE')}}
                                                <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                                <input type="hidden" name="rid" value="{{ $user->result_id }}">
                                                <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                                <button type="submit" class="btn btn-danger btn-sm"> <i
                                                        class="fa fa-redo"> Enable Resit</i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    @else
                                        @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions()))
                                            @if($user->redotest != 0)
                                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions()))
                                                    <a onclick="return confirm('This will stop this this user from access to take retest certification test/ Are you sure you want to do this?');" class="btn btn-warning btn-sm" href="{{ route('stopredotest',['user_id'=>$user->user_id, 'result_id'=>$user->result_id]) }}"><i
                                                            class="fa fa-stop"></i> End resit
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="btn-group">
                                    <button class="btn btn-button btn-danger btn-sm" disabled>Participant has not taken any test!</button>
                                    </div>
                                @endif
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                    class="btn btn-warning btn-sm" href="{{ route('impersonate', $user->user_id) }}"><i
                                        class="fa fa-unlock"> Peek</i>
                                    </a>
                                @endif 
                            </td>
                        </tr>
                     
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