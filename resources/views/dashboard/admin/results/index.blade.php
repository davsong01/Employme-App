@extends('dashboard.admin.index')
@section('title', 'Test Results')
@section('css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    .select2-container--default .select2-selection--multiple {
        width: 100% !important; /* Force full width */
    }

    .select2-container {
        width: 100% !important; /* Force full width */
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black; /* Text color for selected items */
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: black; /* Text color for the rendered selections */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black; /* Text color for the single selected item */
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: black; /* Text color for the placeholder */
    }

    .select2-container--default .select2-results__option {
        color: black; /* Text color for the dropdown options */
    }

    .badge {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px;
        height: 45px;
        background-color: #4CAF50;
        border-radius: 50%;
        color: white;
        font-size: 10px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .transaction-count {
        text-align: center;
    }
    .search-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-control {
        border-radius: 20px;
    }

    .rounded {
        border-radius: 20px !important;
    }
    .btn-search {
        border-radius: 20px;
        transition: background-color 0.3s;
    }
    .btn-search:hover {
        background-color: #0056b3;
    }

    .btn.active {
        background-color: #0056b3;
        color: white;
        border: 4px solid black;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        transform: scale(1.05); 
        transition: all 0.3s;
    }

    .btn:not(.active):hover {
        transform: scale(1.05); 
    }

    .button-container .btn {
        border-radius: 8px;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease; 
    }

    .button-container .btn:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .button-container .btn:disabled {
        opacity: 0.6;
    }

    .button-container .fa-unlock {
        margin-right: 0.25rem; 
    }

</style>
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                @include('layouts.partials.alerts')
                <div class="card-header">
                    <div>
                        <h5 class="card-title"> 
                            {!! $title !!}
                        </h5>
                        <br>
                        <div class="card-body">
                            @php
                                $currentStatus = request('status');
                            @endphp
                            <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id]) }}">
                                <button class="btn btn-dark rounded {{ is_null($currentStatus) ? 'active' : '' }}">All</button>
                            </a>
                            <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id]) }}?{{ http_build_query(array_merge(request()->query(), ['status' => 'yes'])) }}">
                                <button class="btn btn-success rounded {{ $currentStatus === 'yes' ? 'active' : '' }}">Has Tests</button>
                            </a>
                            <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id]) }}?{{ http_build_query(array_merge(request()->query(), ['status' => 'no'])) }}">
                                <button class="btn btn-danger rounded {{ $currentStatus === 'no' ? 'active' : '' }}">Pending Tests</button>
                            </a>

                            <a class="btn btn-info rounded" href="javascript:void(0)" data-toggle="modal" data-target="#exportmodal"><i class="fa fa-download"></i> Export {{ $page == 'results' ? 'Post' : 'Pre'}} Test Results</a>
                            <div class="badge float-right">
                                <span class="transaction-count">{{ $records }}</span> <!-- Number of transactions -->
                            </div>
                        </div>
                        <div class="mt-4">
                            <form class="form-inline search-form" method="GET" action="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id]) }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="{{ request('staffID') }}">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                                </div>
                                <div class="form-group mx-sm-2 mb-2">
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="{{ request('phone') }}">
                                </div>
                                <button type="submit" class="btn btn-primary btn-search mb-2">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <table class="table table-striped table-bordered">
                    
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Test Scores</th>
                            @if($page == 'results')
                            <th>Admin Details</th>
                            @endif
                            <th>Passmark</th>
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))<th>Total</th>
                            @endif
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @if($user->passmark)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>
                                @if($page == 'mocks')
                                {{(($user->mocks->count() > 0)) ? $user->mocks->last()->created_at->format('d/m/Y') : ''}}
                                @else 
                                {{(($user->results->count() > 0)) ? $user->results->last()->created_at->format('d/m/Y') : ''}}
                                @endif
                            </td>
                            <td>{{ $user->name }}
                                @if(!empty(array_intersect(adminRoles(), Auth::user()->role())))
                                    <br><b>StaffID</b>: <i>{{ $user->staffID }}</i>
                                    <br><b>Email:</b> <i>{{ $user->email }}</i>
                                    @if($user->phone)
                                    <br><b>Phone</b> <i>{{ $user->phone }}</i>
                                    @endif
                                    @if($user->user->last_login) <br>
                                    <span style="color:green"><strong>Last Login: </strong>{{ $user->user->last_login ? date("M jS, Y H:i", strtotime($user->user->last_login)) : '' }}</span>
                                    @endif
                                @endif
                                <div class="button-container">
                                    @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                                        <a target="_blank" data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                        class="btn btn-dark btn-sm w-50 mb-3" href="{{ route('impersonate', $user->user_id) }}"><i
                                            class="fa fa-unlock"> Peek</i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                                    <?php
                                        $total = ((!empty($score_settings->certification) && $score_settings->certification > 0) ? $user->total_cert_score : 0 )
                                        + ((!empty($score_settings->class_test) && $score_settings->class_test > 0 ) ? $user->final_ct_score : 0)
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
                            @if($page == 'results')
                            <td>
                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(facilitatorRoles(), auth()->user()->role())))<strong class="tit">Marked by: </strong> {{ $user->marked_by ?: 'N/A' }}@endif

                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) <br> <strong class="tit">Graded by: </strong> {{ $user->grader ?: 'N/A'}}<br>

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
                            @endif
                            <td><strong class="tit" style="color:blue">{{ $user->passmark }}%</strong> </td>
                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())))
                            <td>
                                <strong class="tit" style="color:{{ $total < $user->passmark ? 'red' : 'green'}}">{{ $total }}%</strong> 
                            </td>
                            @endif

                            @if($page == 'mocks')
                            <td>
                                @if( $user->result_id )
                                    <div class="btn-group">
                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                            class="btn btn-info" href="{{ route('mocks.add', ['uid' => $user->user_id, 'result' => $user->result_id]) }}"><i
                                                class="fa fa-eye"></i>
                                        </a>
                                            <form action="{{ route('mocks.destroy', $user->result_id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
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
                            @else 
                            <td>
                                <div class="button-container">
                                @if( isset($user->result_id)) 
                                    @if($user->redotest == 0)
                                        @if (!empty($user->certification_test_details))
                                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                                    <a class="btn btn-info btn-sm btn-sm w-100 mb-3" href="{{ route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id]) }}"><i
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
                                            <button class="btn btn-danger btn-sm w-100 mb-3" style="display: block;" disabled>Resit In Progress!</button>
                                            @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role())))
                                                <a class="btn btn-info btn-sm w-100 mb-3" href="{{ route('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id]) }}"><i
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
                                                <button type="submit" class="btn btn-danger btn-sm w-100 mb-3"> <i
                                                        class="fa fa-redo"> Enable Resit</i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    @else
                                        @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions()))
                                            @if($user->redotest != 0)
                                                @if(!empty(array_intersect(adminRoles(), auth()->user()->role())) || in_array(22, Auth::user()->Permissions()))
                                                    <a onclick="return confirm('This will stop this this user from access to take retest certification test/ Are you sure you want to do this?');" class="btn btn-warning btn-sm w-100 mb-3" href="{{ route('stopredotest',['user_id'=>$user->user_id, 'result_id'=>$user->result_id]) }}"><i
                                                            class="fa fa-stop"></i> End resit
                                                    </a>
                                                @endif
                                            @endif
                                        @endif
                                        </div>
                                    @endif
                                @else
                                    <button class="btn btn-danger btn-sm w-100 mb-3" disabled>No Test Taken!</button>
                                @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{$users->render()}}
        </div>
    </div>
</div>
<div class="modal fade" id="exportmodal" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export {{ $page == 'results' ? 'Post' : 'Pre'}} test results</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id'=>$program->id])}}" method="POST" class="pb-2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="columns">User Columns to Export</label>
                                <select name="columns[]" id="columns" class="form-control select2 w-100" multiple="multiple" required>
                                    <option value="all" selected>All</option>
                                    <option value="name">Name</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone</option>
                                    <option value="gender">Gender</option>
                                    <option value="staffID">StaffID</option>
                                    <option value="metadata">Metadata</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary" style="width:100%">
                            Submit
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>     
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Option --",
            allowClear: true
        });
    });
</script>
@endsection