@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('css')
<style>
    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table-image {
        width: 85px;
        border-radius: 5px;
        object-fit: cover;
    }
    .btn {
        border-radius: 5px;
        margin: 2px 0;
    }

    .actions-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .export-link {
        color: brown;
        font-weight: bold;
    }

    .export-link:hover {
        text-decoration: underline;
        color: darkred;
    }

    .dropdown {
        position: relative;
        display: block;
    }
    .dropdown-button {
        background-color: #17a2b8;
        color: white;
        padding: 4px 4px;
        font-size: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .dropdown-button:hover {
        background-color: #138496; /* Slightly darker shade for hover */
    }
    /* Dropdown content (hidden by default) */
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    /* Links inside the dropdown */
    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 100%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        border-radius: 50%;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    .modal-backdrop {
        position: relative;
    }

    .flashing-red {
        color: red;
        font-weight: bold;
        animation: flashRed 1s infinite;
    }

    @keyframes flashRed {
        0%   { opacity: 1; }
        50%  { opacity: 0; }
        100% { opacity: 1; }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Trainings 
                        @if(canUserAccessPermission(['programs.create'])['programs.create'])
                        <a href="{{route('programs.create')}}"><button type="button" class="btn btn-outline-primary">Add New Training</button></a>
                        @endif
                    </h5> 
                </div> 
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Banner</th>
                            <th>Title</th>
                            <th>Fee</th>
                            <th>Dates</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        @php
                            $permissionsToCheck = [
                                'program.detailsexport',
                                'programs.edit',
                                'crm.hide',
                                'crm.show',
                                'results.disable',
                                'results.enable',
                                'password.reset', 
                                'registration.close', 
                                'registration.open',
                                'training.clone', 
                                'training.import',
                                'programs.destroy',
                                'earlybird.close',
                                'earlybird.open'
                            ];
                            
                            $program->permissions = checkTrainingHasPermissions($program->id, $permissionsToCheck);
                        @endphp
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td> <img src="{{ url('/').'/'.$program->image }}" alt="banner" style="width: 85px;"> </td> 
                            <td><strong>{{ $program->p_name }}</strong> <br> 
                                @if($program->parent)
                                    <span style="color:#d97000"><strong>Parent: </strong>
                                        <a target="_blank" href="{{ route('programs.edit', $program->parent->id) }}">
                                            {{ $program->parent->p_name }} </a> </span>
                                @endif
                                
                                @if(empty($program->resolve_to_ids))
                                    <span class="flashing-red">(No resolve)</span>
                                @else
                                    <span style="font-size:10px;color:#004d2b">Resolves to: 
                                        @foreach($program->resolve_to_programs as $index => $prog)
                                            {{ $prog->p_name }}@if(!$loop->last), @endif
                                        @endforeach
                                    </span>
                                @endif
                                <br>
                                <strong>Type:</strong> @if($program->off_season)Off Season @else Normal @endif 
                                @if($program->early_bird_status == 1) <br> <button class="btn btn-danger btn-xs">Discounted</button> @endif
                                <span class="child-parent-details" style="font-size:10px">
                                    
                                    @if($program->subPrograms->count() > 0)
                                    <div class="dropdown">
                                        <button class="dropdown-button">View Children</button>
                                        <div class="dropdown-content">
                                            @foreach($program->subPrograms as $p)
                                            <a target="_blank" href="{{ route('programs.edit', $p->id)}}">{{ $p->p_name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </span>
                                
                                @if($program->permissions['program.detailsexport'])
                                <br> <a href="{{ route('program.detailsexport', ['p_id'=> $program->id, 'id'=> $program->id]) }}"><span style="color:brown;"><i class="fa fa-download"></i> Export Participant's details</span></a>
                                @endif

                                @if($program->status == 1) <br>
                                <a  href="{{ url('/trainings').'/'.$program->id }}" target="_blank"> <i class="fa fa-eye"></i> Preview Training</a> @endif  <br>
                                @if($program->permissions['programs.edit'])
                                <a data-toggle="tooltip" data-placement="top" title="Edit Training"
                                    class="btn btn-info btn-xs" href="{{ route('programs.edit', ['p_id'=> $program->id, 'program'=> $program->id])}}"><i
                                        class="fa fa-edit"></i> Edit
                                </a> 
                                @endif
                                @if($program->hascrm == 0)
                                    @if($program->permissions['crm.show'])
                                    <a data-toggle="tooltip" onclick="return confirm('Are you really sure?');" data-placement="top" title="Enable CRM"
                                        class="btn btn-primary btn-xs" href="{{ route('crm.show', ['p_id'=> $program->id, 'crm'=> $program->id])}}" ><i
                                            class="far fa-comments"></i> Enable CRM
                                    </a>
                                    @endif
                                @else
                                    @if($program->permissions['crm.hide'])
                                    <a data-toggle="tooltip" onclick="return confirm('Are you really sure?');"  data-placement="top" title="Disable CRM"
                                        class="btn btn-primary btn-xs" href="{{ route('crm.hide', ['p_id'=> $program->id, 'crm'=> $program->id])}}" ><i class="fa fa-ban"> Disable CRM</i>
                                    </a>
                                    @endif
                                @endif
                                @if($program->hasresult == 0)
                                    @if($program->permissions['results.enable'])
                                    <a data-toggle="tooltip" data-placement="top" title="Enable User Results"
                                        class="btn btn-success btn-xs" href="{{ route('results.enable', ['p_id'=> $program->id, 'id'=> $program->id])}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-graduation-cap"></i> Enable result
                                    </a>
                                    @endif
                                @else
                                    @if($program->permissions['results.disable'])
                                    <a data-toggle="tooltip" data-placement="top" title="Disable User Results"
                                        class="btn btn-info btn-xs" href="{{ route('results.disable', ['p_id'=> $program->id, 'id'=> $program->id])}}" ><i onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i> Disable Result
                                    </a>
                                    @endif
                                @endif

                            </td>
                            
                            <td><strong>Normal Fee:</strong> {{ \App\Models\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->p_amount) }} <br>
                            <strong>EarlyBird:</strong> {{ \App\Models\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->e_amount) }}
                            </td>
                            <td> <strong>Start:</strong> {{ $program->p_start }} <br>
                                <strong>End: </strong>{{ $program->p_end }}
                            </td>
                            
                            <td>Part: {{ $program->part_paid }} <br>
                                Full: {{ $program->fully_paid }} <br>
                                Materials: {{ $program->materials->count() }} <br>
                                Modules: {{ $program->modules->count() }}
                            </td>
                            <td>
                                @if( $program->status == 1 )
                                <button class="btn btn-dark btn-xs">Published</button> 
                                @else
                                <button class="btn btn-muted btn-xs">Draft</button> 
                                @endif
                            </td>
                            <td style="vertical-align: unset;">
                                <div class="" style="margin-bottom: 5px;">
                                    @if($program->permissions['password.reset'])
                                    <a data-toggle="tooltip" data-placement="top" title="Reset Participant's password" class="btn btn-dark btn-xs" href="{{ route('admin.password.reset', ['p_id'=> $program->id, 'id'=> $program->id])}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i> Reset Password </a>
                                    @endif
                                    @if($program->close_registration == 0)
                                        @if($program->permissions['registration.close'])
                                        <a data-toggle="tooltip" data-placement="top" title="Close registration" class="btn btn-danger btn-xs" href="{{ route('registration.close', ['p_id'=> $program->id, 'id'=> $program->id])}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i> Close registration
                                        </a>
                                        @endif
                                    @else
                                        @if($program->permissions['registration.open'])
                                        <a data-toggle="tooltip" data-placement="top" title="Extend Registration"
                                            class="btn btn-success btn-xs" href="{{ route('registration.open', ['p_id'=> $program->id, 'id'=> $program->id])}}"><i
                                            onclick="return confirm('Are you really sure?');" class="fa fa-window-restore"></i> Extend Registration
                                        </a>
                                        @endif
                                    @endif                                 
                                </div>
                                
                                <div class="" style="margin-bottom: 5px;">
                                    @if($program->permissions['training.clone'])
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cloneTraining{{ $program->id }}" data-placement="top" title="Clone Training"
                                            class="btn btn-success btn-xs" style="background:#183153"><i class="fa fa-copy"></i> Clone Training
                                        </a>

                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#importData{{ $program->id }}" data-placement="top" title="Import Data from another training"
                                            class="btn btn-success btn-xs"><i class="fa fa-aplace"></i> Import Data
                                        </a>
                                        
                                    @endif

                                    @if($program->permissions['training.import'])
                                        <a data-toggle="tooltip" data-placement="top" title="Import Participants"
                                            class="btn btn-dark btn-xs" style="background:#183153" href="{{ route('training.import', ['p_id'=> $program->id, 'program'=> $program->id])}}"><i class="fa fa-upload"></i> Bulk Import
                                        </a>
                                    @endif
                                    <form action="{{ route('programs.destroy', ['p_id'=> $program->id, 'program' => $program->id]) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to trash?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}
                                        @if($program->permissions['programs.destroy'])
                                        <button type="submit" class="btn btn-warning btn-xs" data-toggle="tooltip"
                                            data-placement="top" title="Trash Training"> <i class="fa fa-recycle"></i> Trash
                                        </button>
                                        @endif

                                    </form>
                                </div>
                                @if($program->e_amount > 0)
                                    <div class="extra-actions" style="padding-top:0px">
                                        @if($program->early_bird_status == 1)
                                            @if($program->permissions['earlybird.close'])
                                                <a data-toggle="tooltip" data-placement="top" title="Close Early Bird Payment" class="btn btn-info btn-xs" href="{{ route('earlybird.close', ['id' => $program->id])}}"><i
                                                        onclick="return confirm('Are you really sure?');" class="fa fa-folder-open"></i> Close Earlybird
                                                </a>
                                            @endif
                                        @else
                                            @if($program->permissions['earlybird.open'])
                                            <a data-toggle="tooltip" data-placement="top" title="Extend Early Bird Payment"
                                                    class="btn btn-info btn-xs" href="{{ route('earlybird.open', ['id' => $program->id])}}" ><i
                                                    onclick="return confirm('Are you really sure?');" class="fa fa-folder"></i> Extend Earlybird
                                            </a>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @if($program->permissions['training.clone'])
                        <div class="modal fade" id="cloneTraining{{ $program->id }}" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="batchModalLabel">Clone {{ $program->p_name }}</h5>
                                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form onsubmit="return confirm('This will clone training');" action="{{ route('training.clone', ['p_id'=> $program->id, 'training'=> $program->id]) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="clone_options{{$program->id}}" class="form-label">Select Clone Options</label> <br>
                                            <select name="clone_options[]" class="select2 form-control m-t-15" multiple="multiple" required id="clone_options{{$program->id}}" style="width: 100%;">
                                                <option value="training_materials">Training Materials</option>
                                                <option value="modules">Modules</option>
                                                <option value="score_settings">Score Settings</option>
                                                <option value="certificate_settings">Certificate Settings</option>
                                                <option value="all">All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success" id="generate-button">
                                        Clone
                                    </button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="importData{{ $program->id }}" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="batchModalLabel">Import Data</h5>
                                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form onsubmit="return confirm('This will import data from another training');" action="{{ route('training.import.data', ['p_id'=> $program->id, 'training'=> $program->id]) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="import_options{{$program->id}}" class="form-label">Select Clone Options</label> <br>
                                            <select name="import_options[]" class="select3 form-control m-t-15" multiple="multiple" required id="import_options{{$program->id}}" style="width: 100%;">
                                                <option value="training_materials">Training Materials</option>
                                                <option value="modules">Modules</option>
                                                <option value="score_settings">Score Settings</option>
                                                <option value="certificate_settings">Certificate Settings</option>
                                                <option value="all">All</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            {{-- {{dd($programs)}} --}}
                                            <label for="" class="form-label">Select Training to Import from</label> <br>
                                            <select name="import_from" class="select4 form-control m-t-15" required style="width: 100%;">
                                                <option value="">Select</option>
                                                @foreach ($programs as $prog)
                                                    @if($prog->id != $program->id)
                                                        <option value="{{ $prog->id }}">{{ $prog->p_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success" id="import-button">
                                        Import
                                    </button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>


@endsection
@section('extra-scripts')
    <script>
        $(document).ready(function() {
            $('.select3').select2({
                dropdownParent: $('body'),
                width: '100%'
            });

            $('.select4').select2({
                dropdownParent: $('body'),
                width: '100%',
                minimumResultsForSearch: 0
            });
        });
        
        document.querySelector('.dropdown-button').addEventListener('click', function() {
            const dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    </script>
@endsection