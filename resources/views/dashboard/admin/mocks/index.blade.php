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
   
    .result-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: .65rem 1rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        font-size: 1rem;
        font-weight: 800;
        box-shadow: 0 8px 24px rgba(16, 185, 129, .2);
    }

    .search-form {
        background-color: #f8fafc;
        padding: 1.25rem;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
    }

    .search-form .form-control,
    .search-form .select2-selection--single {
        border-radius: 12px;
    }

    .result-filter-chip {
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 12px;
        font-weight: 700;
        padding: .65rem 1rem;
        border: 1px solid #dbe4ee;
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

    .results-page {
        overflow-x: hidden;
    }

    .results-page .result-toolbar .btn,
    .results-page .result-toolbar .badge {
        border-radius: 999px;
    }

    .results-page .table {
        table-layout: fixed;
        width: 100%;
    }

    .results-page .table td,
    .results-page .table th {
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .results-page .results-table-shell {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .results-page .results-table-shell table {
        min-width: 1120px;
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

    .results-page .result-summary-card {
        border-radius: 18px;
    }

    @media (max-width: 767.98px) {
        .results-page .crm-hero .card-body {
            padding: 1.25rem !important;
        }

        .results-page .result-toolbar {
            gap: .5rem !important;
        }

        .results-page .result-toolbar .btn,
        .results-page .result-toolbar .badge {
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
    }

</style>
@endsection
@section('title', 'All Results')
@section('content')
<div class="container-fluid results-page">
    @php
        $currentStatus = request('status');
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">{!! $title !!}</h1>
                            <p class="text-muted mb-0">Review result records, open score modals, and export test data from a cleaner dashboard shell.</p>
                        </div>
                        <div class="result-count-badge">{{ $records }}</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-4 result-toolbar">
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id]) }}" class="btn btn-outline-dark {{ is_null($currentStatus) ? 'active' : '' }}">All</a>
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id,'status' => 'yes']) }}" class="btn btn-outline-success {{ $currentStatus === 'yes' ? 'active' : '' }}">Has Tests</a>
                        <a href="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id,'status' => 'no']) }}" class="btn btn-outline-danger {{ $currentStatus === 'no' ? 'active' : '' }}">Pending Tests</a>
                        <a class="btn btn-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exportmodal"><i class="fa fa-download me-1"></i>Export {{ $page == 'results' ? 'Post' : 'Pre' }} Test Results</a>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form class="search-form" method="GET" action="{{ route($page == 'results' ? 'results.getgrades' : 'mocks.getgrades', ['id' => $program->id]) }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="{{ request('staffID') }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
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
                            @if(!$permissions['results.add'] && !$permissions['results.destroy'] && !$permissions['stopredotest'] && !$permissions['mocks.add'])
                            @else
                            <th>Actions</th>
                            @endif
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr id="result-row-{{ $user->id }}">
                            <td data-label="#">{{ $i++ }}</td>
                            @if($page == 'mocks')
                                    <td data-label="Actions">
                                        @if($user->result_id)
                                            <div class="btn-group">
                                                @if($permissions['mocks.add'])
                                                    <a data-bs-toggle="tooltip" data-placement="top" title="Update user scores"
                                                    class="btn btn-info btn-sm" href="{{route('mocks.add', ['uid' => $user->user_id, 'result' => $user->result_id,'p_id' => $program->id]) }}">
                                                        <i class="fa fa-eye"></i>
                                                    </a>]

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
                                    </td>
                                @else 
                                    @if(!$permissions['results.add'] && !$permissions['results.destroy'] && !$permissions['stopredotest'])
                                    @else
                                        <td data-label="Actions">
                                            <div class="button-container">
                                                @if (!empty($user->training_result))
                                                    @if($permissions['results.add'])
                                                        <a data-bs-toggle="tooltip" data-placement="top" title="Update Test Scores:"
                                                            class="btn btn-info btn-sm open-result-modal" 
                                                            data-id="{{ $user->id }}" data-uid="{{ $user->user_id }}" data-pid="{{$user->program_id}}", data-p_id = {{ $user->program_id }}
                                                            href="javascript:void(0)">
                                                            <i class="fa fa-edit"> View/Update</i>
                                                        </a>
                                                
                                                    @endif
                                                    
                                                @else
                                                    <button class="btn btn-danger btn-sm w-100 mb-3" disabled>No Test Taken!</button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                @endif
                                <td data-label="Date">
                                    @if($page == 'mocks')
                                        {{ $user->mocks->count() > 0 ? $user->mocks->last()->created_at->format('d/m/Y') : '' }}
                                    @else
                                        {{ $user->results->count() > 0 ? $user->results->last()->created_at->format('d/m/Y') : '' }}
                                    @endif
                                </td>
                                
                                <td data-label="Details">
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
                                    @endif

                                    <div class="button-container">
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
                                    <td data-label="Test Scores">
                                        @if(isset($user->training_result))
                                            @if($permissions['view-class-score'] && isset($score_settings->class_test) && $score_settings->class_test > 0)
                                                <div class="class-test-score">
                                                    <strong class="tit">Class Tests:</strong>
                                                    <span id="class_test_score{{ $user->id }}">{{ $user->training_result->class_test_score }}</span>% <br>
                                                </div>
                                            @endif

                                            @if($permissions['view-certification-score'] && isset($score_settings->certification) && $score_settings->certification > 0)
                                                <div class="certification-score">
                                                    <strong>Certification: </strong>
                                                    <span id="certification_test_score{{ $user->id }}">{{ $user->training_result->certification_test_score }}</span>%
                                                    @if($user->training_result->certification_test_score < $score_settings->certification)
                                                        @include('dashboard.admin.results.enable_resit')
                                                        @php
                                                            $histories = $user->certification_resits($user->program_id, $user->user_id);
                                                        @endphp
                                                        @if($histories->count() > 0)
                                                        {{-- <br> --}}
                                                        <span class="retake">RESITS</span>
                                                            <span style="background: aqua; padding: 5px 10px; border-radius: 50%; display: inline-block; text-align: center; width: 30px; height: 30px; line-height: 20px;" class="thread-count">
                                                                {{ $histories->count() }}
                                                            </span>
                                                            <a style="border-radius: 6px;color: white;" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#resitModal{{$user->id}}">
                                                            View  History
                                                            </a>
                                                        @endif
                                                        
                                                    @endif
                                                </div>
                                            @endif

                                            @if($permissions['view-roleplay-score'] && isset($score_settings->role_play) && $score_settings->role_play > 0)
                                                <div class="roleplay-score">
                                                    <strong class="tit">Role Play: </strong>
                                                    <span id="role_play_score{{ $user->id }}">{{ $user->training_result->roleplay_test_score }}</span>% <br>
                                                </div>
                                            @endif

                                            @if($permissions['view-crm-score'] && isset($score_settings->crm_test) && $score_settings->crm_test > 0)
                                                <div class="crm-test-score">
                                                    <strong class="tit">CRM Test: </strong>
                                                    <span id="crm_test_score{{ $user->id }}">{{ $user->training_result->crm_test_score }}</span>% <br>
                                                </div>
                                            @endif

                                            @if($permissions['view-email-score'] && isset($score_settings->email) && $score_settings->email > 0)
                                                <div class="email-test-score">
                                                    <strong>Email: </strong>
                                                    <span id="email_test_score{{ $user->id }}">{{ $user->training_result->email_test_score }}</span>%
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

                                <td data-label="Passmark">
                                    <strong class="tit" style="color:blue">{{ $score_settings->passmark }}%</strong> 
                                </td>
                                @if($permissions['view-total-score'])
                                    <td data-label="Total">
                                        @if(isset($user->training_result))
                                        <strong class="tit" id="total_score{{ $user->id }}" style="color:{{ $user->training_result->total_score < $score_settings->passmark ? 'red' : 'green' }}">{{ $user->training_result->total_score }}%</strong> 
                                        @else   
                                        0%
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            
                            <div class="modal fade" id="resitModal{{$user->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable modal-fullscreen-md-down">
                                    <div class="modal-content">
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
            const modalContent = $('#modalContent');
            
            modalContent.html(`
                <div class="text-center my-3">
                    <i class="fas fa-spinner fa-spin fa-2x"></i> Loading...
                </div>
            `);
            const url = `{!!route('results.add', ['id' => '__id__', 'p_id' => '__p_id__']) !!}`
                .replace('__id__', id)
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
