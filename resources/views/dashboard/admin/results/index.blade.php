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
    .certification-score {
        background: #c3dbd8;
        padding: 10px;
        border-bottom: 1px solid black;
    }

    .class-test-score {
        background: #e0f7fa;  /* Light blue for class test */
        padding: 10px;
        border-bottom: 1px solid black;
    }

    .roleplay-score {
        background: #fff9c4;  /* Light yellow for role play */
        padding: 10px;
        border-bottom: 1px solid black;
    }

    .crm-test-score {
        background: #ffe082;  /* Light orange for CRM test */
        padding: 10px;
        border-bottom: 1px solid black;
    }

    .email-test-score {
        background: #c8e6c9;  /* Light green for email */
        padding: 10px;
        border-bottom: 1px solid black;
    }

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
@section('title', 'All Results')
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
                            <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id]) }}">
                                <button class="btn btn-dark rounded {{ is_null($currentStatus) ? 'active' : '' }}">All</button>
                            </a>
                            <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id,'status' => 'yes']) }}">
                                <button class="btn btn-success rounded {{ $currentStatus === 'yes' ? 'active' : '' }}">Has Tests</button>
                            </a>
                            <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id,'status' => 'no']) }}">
                                <button class="btn btn-danger rounded {{ $currentStatus === 'no' ? 'active' : '' }}">Pending Tests</button>
                            </a>

                            <a class="btn btn-info rounded" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exportmodal"><i class="fa fa-download"></i> Export {{ $page == 'results' ? 'Post' : 'Pre'}} Test Results</a>

                            {{-- <a onclick="return confirm('Are you really sure?');" href="{{ route('result.clear.duplicates', ['id' => $program->id, 'p_id' => $program->id, 'program_id' => $program->id])}}" class="btn btn-danger rounded">Clear Duplicates</a> --}}


                            <div class="badge float-right">
                                <span class="transaction-count">{{ $records }}</span> <!-- Number of transactions -->
                            </div>
                        </div>
                        <div class="mt-4">
                            <form class="search-form" method="GET" action="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id]) }}">
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
                            {{-- @if(!$permissions['results.add'] && !$permissions['results.destroy'] && !$permissions['stopredotest'] && !$permissions['mocks.add'])
                            @else
                            <th>Actions</th>
                            @endif
                             --}}
                            <th>Details</th>

                            @if(!$permissions['view-certification-score'] && !$permissions['view-roleplay-score'] && 
                                !$permissions['view-email-score'] && !$permissions['view-crm-score'] && !$permissions['view-class-score'])
                            @else
                                <th style="width: 35%;">Test Details</th>
                            @endif

                            @if($page == 'results')
                                <th>Admin Details</th>
                            @endif

                            @if($permissions['view-total-score'])
                                <th>Total</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr id="result-row-{{ $user->id }}">
                                <td>{{ $i++ }}</td>
                                <td>
                                    
                                    @if(canUserAccessPermission(['users.edit'])['users.edit'])                            
                                        <a target="_blank" href="{{ route('users.edit', $user->user_id) }}">
                                            {{ $user->user->name }} <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        {{  $user->user->name }}
                                    @endif

                                    @if(canUserAccessPermission(['users.edit'])['users.edit'])  
                                        @if(isset($user->user->staffID))
                                            <br><b>StaffID</b>: <i>{{ $user->user->staffID }}</i>
                                        @endif
                                        <br><b>Email:</b> <i>{{ $user->user->email }}</i>
                                        @if($user->phone)
                                            <br><b>Phone</b> <i>{{ $user->user->phone }}</i>
                                        @endif
                                        @if($user->user->last_login)
                                            <br><span style="color:green"><strong>Last Login:</strong> 
                                                {{ date("M jS, Y H:i", strtotime($user->user->last_login)) }}
                                            </span>
                                        @endif
                                            
                                        <br>Certificate Access:
                                        @if(isset($user->show_certificate))
                                            <strong style="color:{{ $user->show_certificate == 1 ? 'green' : 'red' }}">
                                                {{ $user->show_certificate == 1 ? 'Enabled' : 'Disabled' }}
                                            </strong>
                                        @else
                                            Not Uploaded/Test Not Taken
                                        @endif
                                        <br>
                                        @endif
                                            @if($page == 'mocks')
                                        <strong>Date Submitted: </strong>{{ $user->mocks->count() > 0 ? $user->mocks->last()->created_at->format('d/m/Y') : '' }}
                                        @else
                                        <strong>Date Submitted: </strong>{{ $user->results->count() > 0 ? $user->results->last()->created_at->format('d/m/Y') : '' }}
                                        @endif
                                    <br>
                                    <div class="button-container">
                                        @if($page == 'mocks')
                                            @if($user->result_id)
                                                <div class="btn-group">
                                                    @if($permissions['mocks.add'])
                                                        <a data-toggle="tooltip" data-placement="top" title="Update user scores"
                                                        class="btn btn-info" href="{{route('mocks.add', ['uid' => $user->user_id, 'result' => $user->result_id,'p_id' => $program->id]) }}">
                                                            <i class="fa fa-eye"></i>
                                                        </a>]

                                                    @endif
                                                    @if($permissions['mocks.add'])
                                                        <form action="{{route('mocks.destroy', ['mocks' => $user->result_id,  'p_id' => $user->program_id]) }}" method="POST" 
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
                                        @else 
                                            @if(!$permissions['results.add'] && !$permissions['results.destroy'] && !$permissions['stopredotest'])
                                            @else
                                                {{-- <div class="button-container"> --}}
                                                    @if (!empty($user->training_result))
                                                        @if($permissions['results.add'])
                                                            <a data-toggle="tooltip" style="display:block;width:50%" data-placement="top" title="Update Test Scores:"
                                                                class="btn btn-info btn-sm open-result-modal" 
                                                                data-id="{{ $user->id }}" data-r_id="{{$user->results->whereNotNull('certification_test_details')->first()?->id }}" data-uid="{{ $user->user_id }}" data-pid="{{$user->program_id}}", data-p_id ="{{ $user->program_id }}"
                                                                href="javascript:void(0)">
                                                                <i class="fa fa-edit"> View/Update</i>
                                                            </a>
                                                    
                                                        @endif
                                                    @else
                                                        <button class="btn btn-danger btn-sm w-100 mb-3" disabled>No Test Taken!</button>
                                                    @endif
                                                {{-- </div> --}}
                                            @endif
                                        @endif

                                        @if($menuPermissions['impersonate'])
                                            <a target="_blank" data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                            class="btn btn-dark btn-sm w-50 mb-3" href="{{ route('impersonate', $user->user_id) }}">
                                                <i class="fa fa-unlock"> Peek</i>
                                            </a>
                                        @endif

                                        <span id="formSuccessSpan-{{ $user->id }}" style="display:none">
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
                                        @if(isset($user->training_result))
                                            @if($permissions['view-class-score'] && isset($score_settings->class_test) && $score_settings->class_test > 0)
                                                <div class="class-test-score">
                                                    <strong class="tit">Class Tests:</strong>
                                                    <span id="class_test_score{{ $user->id }}" style="font-weight: bold; color: #007BFF;">{{ $user->training_result->class_test_score }}</span> / <span style="font-weight: bold; color: #000;">{{ $score_settings->class_test }}</span>% <br>
                                                    {{-- <span id="class_test_score{{ $user->id }}">{{ $user->training_result->class_test_score }}/{{$score_settings->class_test}}</span>% <br> --}}
                                                        @if($user->training_result->class_test_score < $score_settings->class_test)
                                                            @include('dashboard.admin.results.enable_class_test_resit')
                                                            @php
                                                                $classtest_histories = $user->classtests_resits($user->program_id, $user->user_id);
                                                            @endphp
                                                            @if($classtest_histories->count() > 0) 
                                                            <span class="retake">RESITS</span>
                                                                {{-- <span style="background: aqua; padding: 5px 10px; border-radius: 50%; display: inline-block; text-align: center; width: 30px; height: 30px; line-height: 20px;" class="thread-count">
                                                                    {{ $classtest_histories->count() }}
                                                                </span> --}}
                                                                <br>
                                                                <a style="border-radius: 6px;color: white;" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#classTestResitModal{{$user->id}}">
                                                                View  History  <span style="background: aqua;padding: 6px 6px;border-radius: 50%;display: inline-block;text-align: center;line-height: 12px;color: red;font-weight: bold" class="">{{$classtest_histories->count()}}
                                                                </a>
                                                            @endif
                                                        @endif
                                                </div>
                                            @endif

                                            @if($permissions['view-certification-score'] && isset($score_settings->certification) && $score_settings->certification > 0)
                                                <div class="certification-score">
                                                    <strong>Certification: </strong>
                                                    <span id="certification_test_score{{ $user->id }}" style="font-weight: bold; color: #007BFF;">{{ $user->training_result->certification_test_score }}</span> / <span style="font-weight: bold; color: #000;">{{ $score_settings->certification }}</span>%

                                                    @if($user->training_result->certification_test_score < $score_settings->certification)
                                                        @include('dashboard.admin.results.enable_resit')
                                                        @php
                                                            $histories = $user->certification_resits($user->program_id, $user->user_id);

                                                        @endphp
                                                        @if($histories->count() > 0)
                                                            
                                                            <a style="border-radius: 6px;color: white;" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#resitModal{{$user->id}}">
                                                            View  History <span style="background: aqua;padding: 6px 6px;border-radius: 50%;display: inline-block;text-align: center;line-height: 12px;color: red;font-weight: bold" class="thread-count">
                                                                {{ $histories->count() }}
                                                            </span> 
                                                            </a>
                                                        @endif
                                                        
                                                    @endif
                                                </div>
                                            @endif

                                            @if($permissions['view-roleplay-score'] && isset($score_settings->role_play) && $score_settings->role_play > 0)
                                                <div class="roleplay-score">
                                                    <strong class="tit">Role Play: </strong>
                                                    <span id="role_play_score{{ $user->id }}"  style="font-weight: bold; color: #007BFF;">{{ $user->training_result->roleplay_test_score }}</span> / <span style="font-weight: bold; color: #000;">{{ $score_settings->role_play }}</span>%
                                                </div>
                                            @endif

                                            @if($permissions['view-crm-score'] && isset($score_settings->crm_test) && $score_settings->crm_test > 0)
                                                <div class="crm-test-score">
                                                    <strong class="tit">CRM Test: </strong>
                                                    
                                                    <span id="crm_test_score{{ $user->id }}" style="font-weight: bold; color: #007BFF;">{{ $user->training_result->crm_test_score }}</span> / <span style="font-weight: bold; color: #000;">{{ $score_settings->crm_test }}</span>%
                                                </div>
                                            @endif

                                            @if($permissions['view-email-score'] && isset($score_settings->email) && $score_settings->email > 0)
                                                <div class="email-test-score">
                                                    <strong>Email: </strong>
                                                    
                                                    <span id="email_test_score{{ $user->id }}" style="font-weight: bold; color: #007BFF;">{{ $user->training_result->email_test_score }}</span> / <span style="font-weight: bold; color: #000;">{{ $score_settings->email }}</span>%
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endif

                                @if($page == 'results')
                                    <td>
                                        @if(isset($user->training_result))
                                            <small>
                                                <strong class="tit">Certification Marked by: <br> </strong><span id="certification_facilitator{{ $user->id }}"> {{ $user->training_result->certification_facilitator ?: 'N/A' }}</span><br>
                                                <strong class="tit">Certification Graded by: <br></strong> <span id="certification_grader{{ $user->id }}">{{ $user->training_result->certification_grader ?: 'N/A'}}</span><br>
                                                Last updated on: <span id="updated_at{{ $user->id }}">{{ $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('jS F, Y, h:iA') : ''}}</span>
                                            </small>
                                        @endif
                                    </td>
                                @endif
                                @if($permissions['view-total-score'])
                                    <td>
                                        <strong>Obtainable: </strong><strong class="tit" style="color:blue">{{ $score_settings->passmark }}%</strong>
                                        @if(isset($user->training_result)) <br>
                                        <strong>Obtained: </strong><strong class="tit" id="total_score{{ $user->id }}" style="color:{{ $user->training_result->total_score < $score_settings->passmark ? 'red' : 'green' }}">{{ $user->training_result->total_score }}%</strong> 
                                        @else   
                                        0%
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            
                            <div class="modal fade" id="resitModal{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
                                    <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5>Resit History for: {{ $user->user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        @if(isset($histories) && !empty($histories))
                                            <div class="accordion" id="historyAccordion">
                                            @foreach($histories as $key => $result)
                                                @php
                                                $testDetails = $result->certification_test_details;
                                                $testDetails = json_decode($testDetails, true);
                                                $allDetails = array_keys($testDetails);
                                                $questions = App\Models\Question::whereIn('id', $allDetails)->get();
                                                @endphp

                                                @if($questions)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-{{ $key }}">
                                                    <button 
                                                        class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse-{{ $key }}" 
                                                        aria-expanded="{{ $key == 0 ? 'true' : 'false' }}" 
                                                        aria-controls="collapse-{{ $key }}">
                                                        Submitted on: {{ $result->submitted_on }}
                                                    </button>
                                                    </h2>
                                                    <div id="collapse-{{ $key }}" class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}" 
                                                    aria-labelledby="heading-{{ $key }}" 
                                                    data-bs-parent="#historyAccordion">
                                                    <div class="accordion-body">
                                                        @foreach($questions as $question)
                                                        <div class="mb-3">
                                                            <p><strong style="color:green">QUESTION {{ $loop->iteration }}:</strong></p>
                                                            <p><strong style="color:green">Module:</strong> {{ $result->module->title }}</p>
                                                            <p><strong style="color:green">Question:</strong> {!! $question->title !!}</p>
                                                            <hr>
                                                            <p><strong><h5 style="color:#0056b3">Participant's Answer:</h5></strong>{!! $testDetails[$question->id] !!}</p>
                                                        </div>
                                                        @endforeach
                                                        <p><strong style="color:green">Facilitator's Comment</strong> ({{ $result->marked_by }}): {!! $result->facilitator_comment !!}</p>
                                                        <p><strong style="color:green">Grader's Comment:</strong> ({{ $result->grader_comment }}): {!! $result->grader_comment !!}</p>
                                                        <p><strong style="color:green">Score:</strong> {{ $result->certification_test_score }}</p>
                                                    </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                            </div>
                                        @else
                                            <p>No histories found.</p>
                                        @endif
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="classTestResitModal{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
                                    <div class="modal-content" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5>Resit History for: {{ $user->user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        @if(isset($classtest_histories) && !empty($classtest_histories))
                                            <div class="accordion" id="classhistoryAccordion">
                                            @foreach($classtest_histories as $result)
                                                @php
                                                    $childrenDetails = json_decode($result->class_test_details, true);
                                                @endphp
                                                
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-{{ $result->id }}">
                                                    <button 
                                                        class="accordion-button {{ $result->id == 0 ? '' : 'collapsed' }}" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse-{{ $result->id }}" 
                                                        aria-expanded="{{ $result->id == 0 ? 'true' : 'false' }}" 
                                                        aria-controls="collapse-{{ $result->id }}">
                                                        Submitted on: {{ $result->submitted_on }}
                                                    </button>
                                                    </h2>
                                                    <div id="collapse-{{ $result->id }}" class="accordion-collapse collapse {{ $result->id == 0 ? 'show' : '' }}" 
                                                    aria-labelledby="heading-{{ $result->id }}" 
                                                    data-bs-parent="#classhistoryAccordion">
                                                        <div class="accordion-body">
                                                            @if(!empty($childrenDetails) && count($childrenDetails) > 0 )
                                                                @foreach($childrenDetails as $child)
                                                                    @php
                                                                        if(!empty($child['module_id'])){
                                                                            $module = App\Models\Module::find($child['module_id']);
                                                                        }
                                                                    @endphp

                                                                    <div class="mb-3">
                                                                        <p>
                                                                            <strong style="color:green">Module:</strong> {{ $module->title }}
                                                                            @if(!empty($child['class_test_score']))
                                                                            <br>
                                                                            <strong style="color:green">Score:</strong> {{ $child['class_test_score'] ?? ''}}
                                                                            @endif
                                                                            @if(!empty($child['submitted_on']))
                                                                            <br>
                                                                            <strong style="color:green">Completed On:</strong> {{ Carbon\Carbon::parse($child['submitted_on'])->format('F j, Y, g:i A')}}
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                    <hr>
                                                                @endforeach  
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </div>
                                        @else
                                            <p>No histories found.</p>
                                        @endif
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->appends(request()->all())->links() }}
        </div>
    </div>
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
                    
                    <form action="{{route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id'=>$program->id, 'id'=>$program->id])}}" method="POST" class="pb-2">
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
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.open-result-modal', function () {
            // const uid = $(this).data('uid'); 
            const id = $(this).data('id'); 
            const pid = $(this).data('pid'); 
            const p_id = $(this).data('p_id'); 
            const r_id = $(this).data('r_id'); 
            const modalContent = $('#modalContent');
            
            modalContent.html(`
                <div class="text-center my-3">
                    <i class="fas fa-spinner fa-spin fa-2x"></i> Loading...
                </div>
            `);
            const url = `{!!route('results.add', ['id' => '__id__', 'p_id' => '__p_id__','r_id' => '__r_id__']) !!}`
                .replace('__id__', id)
                .replace('__p_id__', p_id)
                .replace('__r_id__', r_id);

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