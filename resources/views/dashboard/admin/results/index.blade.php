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
                            @if(auth()->user()->role_id == 'Admin')<th>Total</th>@endif
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
                                @if(auth()->user()->role_id == 'Admin' ) <br>
                                <strong class="tit">Email: </strong>{{ $user->email }} <br>
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<br> <strong class="tit">Marked by: </strong> {{ $user->marked_by }}@endif
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader') <br> <strong class="tit">Graded by: </strong> {{ $user->grader }}@endif <br>
                                <small> Last updated on: {{isset($user->updated_at) ?  \Carbon\Carbon::parse($user->updated_at)->format('jS F, Y, h:iA')  : ''}}</small>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')
                                    @if(isset($score_settings->certification) && $score_settings->certification > 0)
                                    <strong>Certification: </strong> {{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}% 
                                    @endif
                                @endif
                                @if(auth()->user()->role_id == 'Admin')

                                @if(isset($score_settings->class_test) && $score_settings->class_test > 0)
                                    <br><strong class="tit">Class Tests:</strong> {{ $user->final_ct_score }}% <br> @endif
                                @endif

                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')
                                    @if(isset($score_settings->role_play) && $score_settings->role_play > 0)
                                    <strong class="tit">Role Play: </strong> {{ $user->total_role_play_score }}%  <br> 
                                    @endif
                                @endif
                                @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')
                                    @if(isset($score_settings->email) && $score_settings->email > 0)
                                        <strong>Email: </strong> {{ $user->total_email_test_score }}% @endif
                                    @endif
                                <?php
                                    $total = $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score;
                                ?>
                            </td>
                            <td><strong class="tit" style="color:blue">{{ $user->passmark }}%</strong> </td>
                            @if(auth()->user()->role_id == 'Admin')
                            <td>
                                 <strong class="tit" style="color:{{ $total < $user->passmark ? 'red' : 'green'}}">{{ $total }}%</strong> 
                            </td>
                            @endif
                            {{-- @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<br>{{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}%@endif
                            @if(auth()->user()->role_id == 'Admin')<td>{{ $user->final_ct_score }}%</td>@endif
                             @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<td>{{ $user->total_role_play_score }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<td>{{ $user->total_email_test_score }}%</td>@endif
                            <td>{{ $user->passmark }}%</td>
                            @if(auth()->user()->role_id == 'Admin')<td>{{ $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<th>{{ $user->marked_by }}</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>{{ $user->grader }}</th>@endif --}}
                            {{-- <th> {{ $user->cl_module_count}}</th> --}}
                            
                            
                            <td>
                                @if( isset($user->result_id)) 
                                    <div class="btn-group">
                                        @if($user->redo_test == 0)
                                            <a class="btn btn-info" href="{{ route('results.add', ['uid' => $user->user_id, 'result' => $user->result_id, 'pid'=>$user->program_id]) }}"><i
                                                    class="fa fa-eye"> View</i>
                                            </a>
                                       
                                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')
                                            <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="{{ route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ]) }}" method="POST">
                                                {{ csrf_field() }}
                                                {{method_field('DELETE')}}
                                                <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                                <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                                <button type="submit" class="btn btn-danger btn-xsm"> <i
                                                        class="fa fa-redo"> Enable Resit</i>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                            @if($user->redo_test != 0)
                                            <a onclick="return confirm('This will stop this this user from access to take retest certification test/ Are you sure you want to do this?');" data-toggle="tooltip" data-placement="top" title="Stop user from retaking certification test"
                                                class="btn btn-warning" href="{{ route('stopredotest',$user->user_id) }}"><i
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
    
// $(document).ready(function () {
//   $("#waacsp").submit(function (event) {

//     var formData = {
//       participants: $("#participants").val(),
//       passmark: $("#passmark").val(),
//       training: $("#training").val(),
//       token: $("#token").val(),
//       email: $("#email").val(),
//     };

//      $.ajax({
//       type: "POST",
//       url: "https://127.0.0.1:8000/api/verify"+"/callback=?",
//       data: formData,
//       dataType: "json",
//       encode: true,
//       crossdomain:true
      
//     }).done(function (data) {
//       console.log(data);
//     });

//     event.preventDefault();
//   });
// });
</script>
@endsection