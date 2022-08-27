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
                        <form id="waacsp" action="{{ route('send.waacsp')}}" class="btn btn-success" method="POST">
                            {{ csrf_field() }}
                        <input type="hidden" id="participants" name="participants" value="{{ $users }}">
                        <input type="hidden" name="passmark" id="passmark" value="{{  $passmark }}">
                        <input type="hidden" name="training" id="training" value="{{ $program_name }}">
                        <input type="hidden" name="email" id="email" value="{{ \App\Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL')}}">
                        <input type="hidden" name="token" id="token" value="{{ \App\Settings::select('token')->first()->value('token')}}">
                        <button type="submit" class="btn btn-primary">Send to WAACSP</button>
                        </form>
                        
                    </div>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Name</th>
                            @if(auth()->user()->role_id == 'Admin' )
                            <th>Email</th>
                            @endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>Cert. Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin')<th>C.T. Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<th>R. Play Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>Email Score</th>@endif
                            <th>Passmark</th>
                            @if(auth()->user()->role_id == 'Admin')<th>T. Score</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<th>Facilitator</th>@endif
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
                             @if(auth()->user()->role_id == 'Admin' )
                            <td>{{ $user->email }}</td>
                            @endif
                             @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<td>{{ isset($user->total_cert_score ) ? $user->total_cert_score : '' }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin')<td>{{ $user->final_ct_score }}%</td>@endif
                             @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<td>{{ $user->total_role_play_score }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<td>{{ $user->total_email_test_score }}%</td>@endif
                            <td>{{ $user->passmark }}%</td>
                            @if(auth()->user()->role_id == 'Admin')<td>{{ $user->total_cert_score  + $user->final_ct_score + $user->total_role_play_score + $user->total_email_test_score }}%</td>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Facilitator')<th>{{ $user->marked_by }}</th>@endif
                            @if(auth()->user()->role_id == 'Admin' || auth()->user()->role_id == 'Grader')<th>{{ $user->grader }}</th>@endif
                            {{-- <th> {{ $user->cl_module_count}}</th> --}}
                            
                            
                            <td>
                               
                                @if( $user->result_id) 
                                    <div class="btn-group">
                                         @if($user->redotest == 0)
                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                            class="btn btn-info" href="{{ route('results.add', ['uid' => $user->user_id, 'result' => $user->result_id, 'pid'=>$user->program_id]) }}"><i
                                                class="fa fa-eye"></i>
                                        </a>
                                        @endif
                                        @if($user->redotest == 0)
                                        <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" action="{{ route('results.destroy', $user->result_id, ['uid' => $user->user_id, 'result' => $user->result_id ]) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}
                                            <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                            <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete certification test details and enable resit"> <i
                                                    class="fa fa-redo"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($user->redotest != 0)
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