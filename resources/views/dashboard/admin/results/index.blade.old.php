@php
    $check = [
        'view-certification-score',
        'view-roleplay-score',
        'view-email-score',
        'view-crm-score',
        'view-class-score',
        'result.export',
        'stopredotest',
        'results.destroy',
        'results.add',
        'view-total-score',
        'mocks.add',
        'mocks.destroy'
    ];

    $permissions = checkTrainingHasPermissions($program->id, $check);
    
    $menuCheck = [
        'impersonate',
    ];

    $menuPermissions = canUserAccessPermission($menuCheck);
@endphp
@extends('dashboard.admin.index')
@section('title', 'Test Results')
@section('css')
<style>
    body {
        background-color: #78909C;
    }

    .demo {
        padding-top: 60px;
        padding-bottom: 110px;
    }

    .btn-demo {
        margin: 15px;
        padding: 10px 15px;
        border-radius: 0;
        font-size: 16px;
        background-color: #FFFFFF;
    }

    .btn-demo:focus {
        outline: 0;
    }

    .demo-footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        padding: 15px;
        background-color: #212121;
        text-align: center;
    }

    .demo-footer > a {
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
        color: #fff;
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

                            <a class="btn btn-info rounded" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exportmodal"><i class="fa fa-download"></i> Export {{ $page == 'results' ? 'Post' : 'Pre'}} Test Results</a>
                            <div class="badge float-right">
                                <span class="transaction-count">{{ $records }}</span> <!-- Number of transactions -->
                            </div>
                        </div>
                        <div class="mt-4">
                            <form class="search-form" method="GET" action="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id]) }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-2">
                                        <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="{{ request('staffID') }}">
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="{{ request('phone') }}">
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-search">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <div class="table-responsive mt-4">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Details</th>

                            @if(!$permissions['view-certification-score'] && !$permissions['view-roleplay-score'] && 
                                !$permissions['view-email-score'] && !$permissions['view-crm-score'] && !$permissions['view-class-score'])
                            @else
                                <th>Test Scores</th>
                            @endif

                            @if($page == 'results')
                                <th>Admin Details</th>
                            @endif

                            <th>Passmark</th>

                            @if($permissions['view-total-score'])
                                <th>Total</th>
                            @endif

                            @if(!$permissions['results.add'] && !$permissions['results.destroy'] && !$permissions['stopredotest'] && !$permissions['mocks.add'])
                            @else
                            <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)

                            {{-- @if($user->training_result->passmark) --}}
                                <tr id="result-row-{{ $user->user_id }}">
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        @if($page == 'mocks')
                                            {{ $user->mocks->count() > 0 ? $user->mocks->last()->created_at->format('d/m/Y') : '' }}
                                        @else
                                            {{ $user->results->count() > 0 ? $user->results->last()->created_at->format('d/m/Y') : '' }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(canUserAccessPermission(['users.edit'])['users.edit'])                            
                                            <a target="_blank" href="{{ route('users.edit', $user->id) }}">
                                                {{ $user->name }} <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                            </a>
                                        @else
                                            {{ $user->name }}
                                        @endif

                                        @if(canUserAccessPermission(['users.edit'])['users.edit'])
                                            <br><b>StaffID</b>: <i>{{ $user->staffID }}</i>
                                            <br><b>Email:</b> <i>{{ $user->email }}</i>
                                            @if($user->phone)
                                                <br><b>Phone</b> <i>{{ $user->phone }}</i>
                                            @endif
                                            @if($user->user->last_login)
                                                <br><span style="color:green"><strong>Last Login:</strong> 
                                                    {{ date("M jS, Y H:i", strtotime($user->user->last_login)) }}
                                                </span>
                                            @endif
                                            <br>Certificate Access:
                                            @if(isset($user->cert))
                                                <strong style="color:{{ $user->show_certificate == 1 ? 'green' : 'red' }}">
                                                    {{ $user->show_certificate == 1 ? 'Enabled' : 'Disabled' }}
                                                </strong>
                                            @else
                                                Not Uploaded/Test Not Taken
                                            @endif
                                        @endif

                                        <div class="button-container">
                                            @if($menuPermissions['impersonate'])
                                                <a target="_blank" data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                                class="btn btn-dark btn-sm w-50 mb-3" href="{{ route('impersonate', $user->user_id) }}">
                                                    <i class="fa fa-unlock"> Peek</i>
                                                </a>
                                            @endif

                                            <span id="formSuccessSpan-{{ $user->user_id }}" style="display:none">
                                                <div class="alert alert-success" role="alert">
                                                    <strong><span class="formSuccess"></span></strong> 
                                                </div>
                                            </span>
                                        </div>
                                    </td>
                                    @php
                                        $total = ((!empty($score_settings->certification) && $score_settings->certification > 0) ? $user->total_cert_score : 0)
                                                + ((!empty($score_settings->class_test) && $score_settings->class_test > 0) ? $user->final_ct_score : 0)
                                                + ((!empty($score_settings->email) && $score_settings->email > 0) ? $user->total_email_test_score : 0)
                                                + ((!empty($score_settings->role_play) && $score_settings->role_play > 0) ? $user->total_role_play_score : 0)
                                                + ((!empty($score_settings->crm_test) && $score_settings->crm_test > 0) ? $user->total_crm_test_score : 0);
                                    @endphp
                                    @if(!$permissions['view-certification-score'] && !$permissions['view-roleplay-score'] &&
                                        !$permissions['view-email-score'] && !$permissions['view-crm-score'] && !$permissions['view-class-score'] &&  !$permissions['mocks.add'])
                                    @else
                                        <td>
                                            @if($permissions['view-class-score'] && isset($score_settings->class_test) && $score_settings->class_test > 0)
                                                <strong class="tit">Class Tests:</strong><span id="class_test_score{{ $user->user_id }}"> {{ $user->final_ct_score }}</span>% <br>
                                            @endif
                                            
                                            @if($permissions['view-certification-score'] && isset($score_settings->certification) && $score_settings->certification > 0)
                                                <strong>Certification: </strong><span id="certification_test_score{{ $user->user_id }}"> {{ $user->total_cert_score }}</span>% <br>
                                            @endif

                                            @if($permissions['view-roleplay-score'] && isset($score_settings->role_play) && $score_settings->role_play > 0)
                                                <strong class="tit">Role Play: </strong> <span id="role_play_score{{ $user->user_id }}">{{ $user->total_role_play_score }}</span>% <br>
                                            @endif

                                            @if($permissions['view-crm-score'] && isset($score_settings->crm_test) && $score_settings->crm_test > 0)
                                                <strong class="tit">CRM Test: </strong><span id="crm_test_score{{ $user->user_id }}"> {{ $user->total_crm_test_score }}</span>% <br>
                                            @endif

                                            @if($permissions['view-email-score'] && isset($score_settings->email) && $score_settings->email > 0)
                                                <strong>Email: </strong> <span id="email_test_score{{ $user->user_id }}">{{ $user->total_email_test_score }}</span>% 
                                            @endif
                                        </td>
                                    @endif

                                    @if($page == 'results')
                                        <td>
                                            <strong class="tit">Marked by: </strong><span id="marked_by{{ $user->user_id }}"> {{ $user->marked_by ?: 'N/A' }}</span><br>
                                            <strong class="tit">Graded by: </strong> <span id="grader{{ $user->user_id }}">{{ $user->grader ?: 'N/A'}}</span><br>
                                            <small>Last updated on: <span id="updated_at{{ $user->user_id }}">{{ $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('jS F, Y, h:iA') : ''}}</span></small>
                                        </td>
                                    @endif

                                    <td>
                                        <strong class="tit" style="color:blue">{{ $user->passmark }}%</strong> 
                                    </td>

                                    @if($permissions['view-total-score'])
                                        <td id="total_score{{ $user->user_id }}">
                                            <strong class="tit" style="color:{{ $total < $user->passmark ? 'red' : 'green' }}">{{ $total }}%</strong> 
                                        </td>
                                    @endif

                                    @if($page == 'mocks')
                                        <td>
                                            @if($user->result_id)
                                                <div class="btn-group">
                                                    @if($permissions['mocks.add'])
                                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                                        class="btn btn-info" href="{{ URL::signedRoute('mocks.add', ['uid' => $user->user_id, 'result' => $user->result_id,'p_id' => $program->id]) }}">
                                                            <i class="fa fa-eye"></i>
                                                        </a>]

                                                    @endif
                                                    @if($permissions['mocks.add'])
                                                        <form action="{{ URL::signedRoute('mocks.destroy', ['mocks' => $user->result_id,  'p_id' => $user->program_id]) }}" method="POST" 
                                                            onsubmit="return confirm('Are you really sure?');">
                                                            {{ csrf_field() }}
                                                            {{method_field('DELETE')}}
                                                            <input type="hidden" name="id" value="{{ $user->result_id }}">
                                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                                    data-placement="top" title="Delete Result"> 
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    @else 
                                        @if(!$permissions['results.add'] && !$permissions['results.destroy'] && !$permissions['stopredotest'])
                                        @else
                                            <td>
                                                <div class="button-container">
                                                    @if(isset($user->result_id))
                                                        @if($user->redotest == 0)
                                                            @if (!empty($user->certification_test_details))
                                                                @if($permissions['results.add'])
                                                                    <a data-toggle="tooltip" data-placement="top" title="Update Test Scores:"
                                                                        class="btn btn-info btn-sm open-result-modal" 
                                                                        data-uid="{{ $user->user_id }}" data-pid="{{$user->program_id}}", data-p_id = {{ $user->program_id }}
                                                                        href="javascript:void(0)">
                                                                        <i class="fa fa-edit"> View/Update</i>
                                                                    </a>
                                                                    
                                                                    <!-- Result Modal -->
                                                                    <div class="modal fade sidebarModal" id="editResultModal" tabindex="-1" role="dialog" aria-labelledby="editResultModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-fullscreen-sm-down modal-dialog-slideout" role="document">
                                                                            <div class="modal-content" style="padding: 0px!important">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="editResultModalLabel">Update Test Scores</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div id="modalContent">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if($permissions['results.destroy'])
                                                            
                                                                    <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" 
                                                                        action="{{ URL::signedRoute('results.destroy', ['uid' => $user->user_id, 'result' => $user->result_id, 'p_id' => $user->program_id]) }}" method="POST">
                                                                        {{ csrf_field() }}
                                                                        {{method_field('DELETE')}}
                                                                        <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                                                        <input type="hidden" name="rid" value="{{ $user->result_id }}">
                                                                        <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                                                        <button type="submit" class="btn btn-danger btn-sm btn-sm w-100 mb-3"> 
                                                                            <i class="fa fa-redo"> Enable Resit</i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @else 
                                                                <button class="btn btn-danger btn-sm w-100 mb-3" style="display: block;" disabled>Resit In Progress!</button>
                                                                @if($permissions['results.add'])
                                                                    <a class="btn btn-info btn-sm w-100 mb-3" href="{{ URL::signedRoute('results.add', ['uid' => $user->user_id, 'pid'=>$user->program_id,'p_id' => $user->program_id]) }}">
                                                                        <i class="fa fa-eye"> View/Update </i>
                                                                    </a>
                                                                @endif
                                                                @if($permissions['results.destroy'])
                                                                    <form onsubmit="return confirm('This will delete this user certification test details and enable test to be re-taken. Are you sure you want to do this?');" 
                                                                        action="{{ URL::signedRoute('results.destroy', ['uid' => $user->user_id, 'result' => $user->result_id,'p_id' => $user->program_id]) }}" method="POST">
                                                                        {{ csrf_field() }}
                                                                        {{method_field('DELETE')}}
                                                                        <input type="hidden" name="uid" value="{{ $user->user_id }}">
                                                                        <input type="hidden" name="rid" value="{{ $user->result_id }}">
                                                                        <input type="hidden" name="pid" value="{{ $user->program_id }}">
                                                                        <input type="hidden" name="override_resit" value="yes">
                                                                        <button type="submit" class="btn btn-danger btn-sm w-100 mb-3"> 
                                                                            <i class="fa fa-redo"> Enable Resit</i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            @endif
                                                        @else
                                                            @if($user->redotest != 0)
                                                                @if($permissions['stopredotest'])
                                                                    <a onclick="return confirm('This will stop this user from access to take retest certification test. Are you sure you want to do this?');" 
                                                                    class="btn btn-warning btn-sm w-100 mb-3" href="{{ URL::signedRoute('stopredotest',['user_id'=>$user->user_id, 'result_id'=>$user->result_id,'p_id' => $user->program_id]) }}">
                                                                        <i class="fa fa-stop"></i> End resit
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @else
                                                        <button class="btn btn-danger btn-sm w-100 mb-3" disabled>No Test Taken!</button>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            {{-- @endif --}}
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
    $(document).ready(function () {
        $(document).on('click', '.open-result-modal', function () {
            const uid = $(this).data('uid'); 
            const pid = $(this).data('pid'); 
            const p_id = $(this).data('p_id'); 
            const modalContent = $('#modalContent');
            
            modalContent.html(`
                <div class="text-center my-3">
                    <i class="fas fa-spinner fa-spin fa-2x"></i> Loading...
                </div>
            `);

            const url = `{!! URL::signedRoute('results.add', ['uid' => '__uid__', 'pid' => '__pid__', 'p_id' => '__p_id__']) !!}`
                .replace('__uid__', uid)
                .replace('__pid__', pid)
                .replace('__p_id__', p_id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function (response) {
                    modalContent.html(response);

                    $('#editResultModal').modal('show');
                },
                error: function (xhr) {
                    console.error('Error loading modal content:', xhr.responseText);
                    modalContent.html(`
                        <div class="text-danger text-center my-3">
                            <i class="fas fa-exclamation-circle"></i> Failed to load data.
                        </div>
                    `);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Option --",
            allowClear: true
        });
    });
</script>
@endsection