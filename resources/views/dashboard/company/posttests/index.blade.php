@php
    $currentStatus = request('status');
@endphp
@extends('dashboard.company.index')
@section('title', 'Test Results')
@section('css')
@include('dashboard.company.partials.company_extra_css')
<link rel="stylesheet" href="{{ asset('modal.css') }}" />
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

    .select2-container--default .select2-selection--multiple {
        width: 100% !important;
    }

    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: black;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: black;
    }

    .select2-container--default .select2-results__option {
        color: black;
    }

    .badge {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: auto !important;
        min-width: 0;
        height: auto;
        background-color: #4CAF50;
        border-radius: 999px;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: .5rem .8rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
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
            <div class="card-title">
                @include('layouts.partials.alerts')
                <div class="card-header">
                    <h5 class="card-title"> 
                        {!! $title !!}
                    </h5>
                    <br>
                    <div class="row">
                        <div class="card-body">
                            <div class="row mb-3">
                                <!-- Badge Display -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <div class="badge bg-secondary w-100 text-center">
                                        <span class="transaction-count">{{ $records }}</span>
                                    </div>
                                </div>
                                <!-- All Tests Button -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <a href="{{ route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id, 'p_id' => $program->id]) }}">
                                        <button class="btn btn-dark w-100 {{ is_null($currentStatus) ? 'active' : '' }}">All</button>
                                    </a>
                                </div>

                                <!-- Has Tests Button -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <a href="{{ route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id,'p_id' => $program->id, 'status' => 'yes']) }}">
                                        <button class="btn btn-success w-100 {{ $currentStatus === 'yes' ? 'active' : '' }}">Has Tests</button>
                                    </a>
                                </div>

                                <!-- Pending Tests Button -->
                                <div class="col-md-3 col-lg-2 mb-2">
                                    <a href="{{ route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id,'p_id' => $program->id, 'status' => 'no']) }}">
                                        <button class="btn btn-danger w-100 {{ $currentStatus === 'no' ? 'active' : '' }}">Pending Tests</button>
                                    </a>
                                </div>

                                <!-- Export Button -->
                                <div class="col-md-3 col-lg-3 mb-2">
                                    <a class="btn btn-info w-100" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exportmodal">
                                        <i class="fa fa-download"></i> Export {{ $page == 'results' ? 'Post' : 'Pre'}} Test Results
                                    </a>
                                </div>

                                
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mt-4">
                            <form class="row search-form" method="GET" action="{{ route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id' => $program->id]) }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">

                                {{-- Name Field --}}
                                <div class="col-md-4 mb-3">
                                    <div class="mb-3">
                                        <label for="name">Enter Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                                    </div>
                                </div>
                                {{-- Search Button --}}
                                <div class="col-md-3">
                                    <label for="" style="color:transparent;display:block">Searching</label>
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 table-responsive">
                <table class="table table-striped table-bordered responsive"> 
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Details</th>
                            <th>Test Scores</th>
                            <th>Passmark</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        {{-- @if($user->passmark) --}}
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>
                                @if($page == 'mocks')
                                {{(($user->mocks->count() > 0)) ? $user->mocks->last()->created_at->format('d/m/Y') : ''}}
                                @else 
                                {{(($user->results->count() > 0)) ? $user->results->last()->created_at->format('d/m/Y') : ''}}
                                @endif
                            </td>
                            <td>
                                {{ $user->user->name }}
                                @if(isset($user->user->staffID))
                                    <br><b>StaffID</b>: <i>{{ $user->user->staffID }}</i>
                                @endif
                            </td>
                            
                            <td>
                                @php
                                    $total = ((!empty($score_settings->certification) && $score_settings->certification > 0) ? $user->total_cert_score : 0)
                                            + ((!empty($score_settings->class_test) && $score_settings->class_test > 0) ? $user->final_ct_score : 0)
                                            + ((!empty($score_settings->email) && $score_settings->email > 0) ? $user->total_email_test_score : 0)
                                            + ((!empty($score_settings->role_play) && $score_settings->role_play > 0) ? $user->total_role_play_score : 0)
                                            + ((!empty($score_settings->crm_test) && $score_settings->crm_test > 0) ? $user->total_crm_test_score : 0);
                                @endphp
                                @if(isset($user->training_result))
                                    @if(isset($score_settings->class_test) && $score_settings->class_test > 0)
                                        <div class="class-test-score">
                                            <strong class="tit">Class Tests:</strong>
                                            <span id="class_test_score{{ $user->id }}">{{ $user->training_result->class_test_score }}</span>% <br>
                                        </div>
                                    @endif

                                    @if(isset($score_settings->certification) && $score_settings->certification > 0)
                                        <div class="certification-score">
                                            <strong>Certification: </strong>
                                            <span id="certification_test_score{{ $user->id }}">{{ $user->training_result->certification_test_score }}</span>%
                                            
                                        </div>
                                    @endif

                                    @if(isset($score_settings->role_play) && $score_settings->role_play > 0)
                                        <div class="roleplay-score">
                                            <strong class="tit">Role Play: </strong>
                                            <span id="role_play_score{{ $user->id }}">{{ $user->training_result->roleplay_test_score }}</span>% <br>
                                        </div>
                                    @endif

                                    @if(isset($score_settings->crm_test) && $score_settings->crm_test > 0)
                                        <div class="crm-test-score">
                                            <strong class="tit">CRM Test: </strong>
                                            <span id="crm_test_score{{ $user->id }}">{{ $user->training_result->crm_test_score }}</span>% <br>
                                        </div>
                                    @endif

                                    @if(isset($score_settings->email) && $score_settings->email > 0)
                                        <div class="email-test-score">
                                            <strong>Email: </strong>
                                            <span id="email_test_score{{ $user->id }}">{{ $user->training_result->email_test_score }}</span>%
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <strong class="tit" style="color:blue">{{ $score_settings->passmark }}%</strong> 
                            </td>
                            <td>
                                @if(isset($user->training_result))
                                <strong class="tit" id="total_score{{ $user->id }}" style="color:{{ $user->training_result->total_score < $score_settings->passmark ? 'red' : 'green' }}">{{ $user->training_result->total_score }}%</strong> 
                                @else   
                                0%
                                @endif
                            </td>
                        </tr>
                        {{-- @endif --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->appends(request()->all())->links() }}
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
                    <form action="{{route($page == 'results' ? 'company.results.getgrades' : 'company.mocks.getgrades', ['id'=>$program->id, 'p_id'=>$program->id])}}" method="POST" class="pb-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="columns">User Data to Export</label>
                                    <select name="columns[]" id="columns" class="form-control select2 w-100" multiple="multiple" required>
                                        <option value="name">Name</option>
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
    @endsection
    
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Select Option --",
            allowClear: true
        });
    });
</script>

