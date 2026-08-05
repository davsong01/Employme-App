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

    .transaction-count {
        text-align: center;
    }

    .button-container .btn {
        border-radius: 8px;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease; 
        white-space: nowrap;
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

    .results-page .retake {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-right: .35rem;
        margin-top: .25rem;
    }

    .results-page .thread-count {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        min-height: 28px;
        padding: 0 .45rem !important;
        line-height: 1 !important;
        font-size: .75rem;
        font-weight: 800;
        vertical-align: middle;
    }

    .results-page .certification-score .btn,
    .results-page .class-test-score .btn {
        margin-top: .5rem;
        margin-right: .35rem;
        white-space: nowrap;
    }

    .results-page .resit-status {
        line-height: 1.35;
    }

    .results-page {
        overflow-x: hidden;
    }

    .results-page .results-table-shell {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .results-page .results-table-shell table {
        min-width: 980px;
    }

    .results-page .button-container {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    .results-page .button-container .btn,
    .results-page .button-container form,
    .results-page .button-container .btn-group {
        flex: 0 0 auto;
        margin-bottom: 0 !important;
    }

    .results-page .button-container .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .results-page .results-toolbar .btn,
    .results-page .results-toolbar .badge {
        border-radius: 999px;
    }

    @media (max-width: 767.98px) {
        .results-page .crm-hero .card-body {
            padding: 1.25rem !important;
        }

        .results-page .results-toolbar {
            gap: .5rem !important;
        }

        .results-page .results-toolbar .btn,
        .results-page .results-toolbar .badge {
            width: auto;
            justify-content: center;
        }

        .results-page .results-table-shell {
            border-radius: 14px;
        }

        .results-page .button-container {
            gap: .35rem;
        }

        .results-page .button-container .btn {
            padding-left: .75rem;
            padding-right: .75rem;
        }

        .results-page .certification-score .btn,
        .results-page .class-test-score .btn {
            width: 100%;
            margin-right: 0;
            white-space: normal;
        }

        .results-page .retake {
            display: inline-flex;
            margin-top: .5rem;
            margin-bottom: .35rem;
        }

        .results-page .thread-count {
            min-width: 26px;
            min-height: 26px;
            font-size: .7rem;
        }

        .results-page .resit-status {
            display: block !important;
            margin-top: .25rem;
        }

        .results-page .crm-mobile-stack th:nth-child(4),
        .results-page .crm-mobile-stack td:nth-child(4),
        .results-page .crm-mobile-stack th:nth-child(5),
        .results-page .crm-mobile-stack td:nth-child(5) {
            display: none;
        }
    }

    .answer-box {
        max-width: 100%;
        overflow-x: auto;
        word-wrap: break-word;
        word-break: break-word;
    }

    .answer-box * {
        max-width: 100% !important;
    }

    table {
        table-layout: fixed;
        width: 100%;
    }

    td {
        overflow-wrap: break-word;
        word-break: break-word;
    }
</style>
@endsection
@section('title', 'All Results')
@section('content')
<div class="container-fluid results-page">
    @php
        $currentStatus = request('status');
        $resultRoute = route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id]);
        $exportLabel = $page == 'results' ? 'Post' : 'Pre';
        $activeTab = is_null($currentStatus) ? 'all' : $currentStatus;
        $passedTest = request('passed_test');
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">{!! $title !!}</h1>
                            <p class="text-muted mb-0">Review test scores, manage resits, and export data without leaving the dashboard shell.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $records }} records</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $page === 'results' ? 'Post' : 'Pre' }} tests</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 results-toolbar">
                        <a href="{{ $resultRoute }}" class="btn btn-outline-dark {{ $activeTab === 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id,'status' => 'yes']) }}" class="btn btn-outline-success {{ $currentStatus === 'yes' ? 'active' : '' }}">Has Tests</a>
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id,'status' => 'no']) }}" class="btn btn-outline-danger {{ $currentStatus === 'no' ? 'active' : '' }}">Pending Tests</a>
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id, 'passed_test' => 'yes']) }}" class="btn btn-outline-info {{ $passedTest === 'yes' ? 'active' : '' }}">Passed</a>
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id, 'passed_test' => 'no']) }}" class="btn btn-outline-danger {{ $passedTest === 'yes' ? 'active' : '' }}">Failed</a>

                        <a class="btn btn-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exportmodal">
                            <i class="fa fa-download me-1"></i> Export {{ $exportLabel }} Test Results
                        </a>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ $resultRoute }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="staffID">Staff ID</label>
                        <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="{{ request('staffID') }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label small text-uppercase fw-semibold text-muted" for="phone">Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone" value="{{ request('phone') }}">
                    </div>
                    <div class="col-12 d-grid d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-4">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive results-table-shell">
                <table class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
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
                                <td data-label="#">
                                    {{ paginationIndex($users, $loop) }}
                                </td>
                                <td data-label="Details">
                                    
                                    @if(canUserAccessPermission(['users.edit'])['users.edit'])                            
                                        <a target="_blank" href="{{ route('users.edit', $user->user_id) }}">
                                            {{ $user->user->name }} <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        {{ $user->user->name }}
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
                                        @endif
                                        <br>
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
                                                        <a data-bs-toggle="tooltip" data-placement="top" title="Update user scores"
                                                        class="btn btn-info" href="{{route('mocks.add', ['uid' => $user->user_id, 'result' => $user->result_id,'p_id' => $program->id]) }}">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if($permissions['mocks.add'])
                                                        <form action="{{route('mocks.destroy', ['mocks' => $user->result_id,  'p_id' => $user->program_id]) }}" method="POST" 
                                                            onsubmit="return confirm('Are you really sure?');">
                                                            {{ csrf_field() }}
                                                            {{method_field('DELETE')}}
                                                            <input type="hidden" name="id" value="{{ $user->result_id }}">
                                                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
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
                                            @php
                                                $canManageResults = $permissions['results.add'] || $permissions['results.destroy'] || $permissions['stopredotest'];
                                                $certifiableTests = $user->results->whereNotNull('certification_test_details')->first();
                                            @endphp

                                            @if($canManageResults)
                                                
                                                @if (!empty($user->training_result))

                                                    @if (!empty($certifiableTests))
                                                        @if ($permissions['results.add'])
                                                            <a href="javascript:void(0)"
                                                            class="btn btn-info btn-sm open-result-modal"
                                                            data-bs-toggle="tooltip" data-placement="top"
                                                            title="Update Test Scores:"
                                                            data-id="{{ $user->id }}"
                                                            data-r_id="{{ $certifiableTests->id }}"
                                                            data-uid="{{ $user->user_id }}"
                                                            data-pid="{{ $user->program_id }}"
                                                            data-p_id="{{ $user->program_id }}">
                                                                <i class="fa fa-edit"></i> View/Update
                                                            </a>
                                                        @endif

                                                    @else
                                                        <button class="btn btn-danger btn-sm w-100 mb-3" disabled>
                                                            Certification Test Not Taken!
                                                        </button>
                                                    @endif

                                                @else
                                                    <button class="btn btn-danger btn-sm w-100 mb-3" disabled>
                                                        No Test Taken!
                                                    </button>
                                                @endif

                                            @endif

                                        @endif

                                        @if($menuPermissions['impersonate'])
                                            <a target="_blank" data-bs-toggle="tooltip" data-placement="top" title="Impersonate User"
                                            class="btn btn-dark btn-sm" href="{{ route('impersonate', $user->user_id) }}">
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
                                    <td data-label="Test Details">
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
                                                                View History  <span style="background: aqua;padding: 6px 6px;border-radius: 50%;display: inline-block;text-align: center;line-height: 12px;color: red;font-weight: bold" class="">{{$classtest_histories->count()}}
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
                                    <td data-label="Admin Details">
                                        @if(isset($user->training_result))
                                            <small>
                                                <strong class="tit">Certification Marked by: <br> </strong><span id="certification_facilitator{{ $user->id }}"> {{ $user->training_result->certification_facilitator ?: 'N/A' }}</span><br>
                                                <strong class="tit">Certification Graded by: <br></strong> <span id="certification_grader{{ $user->id }}">{{ $user->training_result->certification_grader ?: 'N/A'}}</span><br>
                                                Last updated on: <span id="updated_at{{ $user->id }}">{{ !empty($user->training_result->last_updated_at) ? \Carbon\Carbon::parse($user->training_result->last_updated_at)->format('jS F, Y, h:iA') : ''}}</span>
                                            </small>
                                        @endif
                                    </td>
                                @endif
                                @if($permissions['view-total-score'])
                                    <td data-label="Total">
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
                                        @php
                                            $histories = $user->certification_resits($user->program_id, $user->user_id);
                                        @endphp

                                        @if($histories && $histories->count())
                                            <div class="accordion" id="historyAccordion{{$user->id}}">
                                                @foreach($histories as $key => $result)

                                                    @php
                                                        $testDetails = json_decode($result->certification_test_details, true) ?? [];
                                                        $questions = App\Models\Question::whereIn('id', array_keys($testDetails))->get();
                                                    @endphp

                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="heading-{{$user->id}}-{{ $key }}">
                                                            <button 
                                                                class="accordion-button {{ $key == 0 ? '' : 'collapsed' }}" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#collapse-{{$user->id}}-{{ $key }}">
                                                                Submitted on: {{ $result->submitted_on }}
                                                            </button>
                                                        </h2>

                                                        <div id="collapse-{{$user->id}}-{{ $key }}" 
                                                            class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}"
                                                            data-bs-parent="#historyAccordion{{$user->id}}">

                                                            <div class="accordion-body">

                                                                {{-- @foreach($questions as $question)
                                                                    <div class="mb-3">
                                                                        <p><strong>QUESTION {{ $loop->iteration }}</strong></p>
                                                                        <p><strong>Module:</strong> {{ optional($result->module)->title }}</p>
                                                                        <p><strong>Question:</strong> {!! $question->title !!}</p>

                                                                        <hr>

                                                                        <h5>Participant's Answer:</h5>
                                                                        <div class="answer-box">

                                                                            {!! $testDetails[$question->id] ?? '' !!}
                                                                        </div>
                                                                    </div>
                                                                @endforeach --}}
                                                                @php
                                                                    $testDetails =$result->certification_test_details ?? [];
                                                                @endphp

                                                                @foreach($questions as $question)
                                                                    @php
                                                                        $answer = $testDetails[$question->id] ?? '';
                                                                        $answer = preg_replace('/width:\s*\d+%/i', '', $answer);
                                                                    @endphp

                                                                    <div class="mb-3">
                                                                        <p><strong style="color:green">QUESTION {{ paginationIndex($questions, $loop) }}:</strong></p>
                                                                        <p><strong style="color:green">Module:</strong> {{ $result->module->title }}</p>
                                                                        <p><strong style="color:green">Question:</strong> {!! $question->title !!}</p>
                                                                        <hr>

                                                                        <h5 style="color:#0056b3">Participant's Answer:</h5>

                                                                        <div class="answer-box">
                                                                            <div style="min-width:600px;">
                                                                                {!! $answer !!}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach

                                                                <p><strong>Facilitator:</strong> {{ $result->marked_by ?? 'N/A' }}</p>
                                                                <p><strong>Facilitator Comment:</strong> {!! $result->facilitator_comment !!}</p>

                                                                <p><strong>Grader:</strong> {{ $result->graded_by ?? 'N/A' }}</p>
                                                                <p><strong>Grader Comment:</strong> {!! $result->grader_comment !!}</p>

                                                                <p><strong>Score:</strong> {{ $result->certification_test_score }}</p>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <form action="{{route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id'=>$program->id, 'id'=>$program->id])}}" method="POST" class="pb-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
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
