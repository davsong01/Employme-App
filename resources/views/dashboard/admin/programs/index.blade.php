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

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show the dropdown content when the button is clicked */
        .dropdown:hover .dropdown-content {
            display: block;
        }

    </style>
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
                    <h5 class="card-title"> All Trainings <a href="{{route('programs.create')}}"><button type="button" class="btn btn-outline-primary">Add New Training</button></a></h5> 
                </div> 
                {{-- <div>
                    <h5 class="card-title"> Actions Legend:</h5>
                    <p style="color:green">1. Edit Training | 2. Close/Extend Registration | 3. Enable/Disable CRM | 4. Enable/Disable Result Availability| 5. Trash Training | 6. Close EarlyBird Payment(if applicable)  </p> 
                </div> --}}
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
                            @if(!empty(array_intersect(adminRoles(), Auth::user()->role())))
                            <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td> <img src="{{ url('/').'/'.$program->image }}" alt="banner" style="width: 85px;"> </td> 
                            <td>{{ $program->p_name }}<br>
                                <strong>Type:</strong> @if($program->off_season)Off Season @else Normal @endif <br>
                                @if($program->e_amount > 0)  <button class="btn btn-danger btn-xs">Discounted</button> @endif
                                <span class="child-parent-details" style="font-size:10px">
                                    @if($program->parent)
                                    <span style="color:blue"> <strong>Parent:</strong><a target="_blank" href="{{ route('programs.edit', $program->parent->id)}}">{{ $program->parent->p_name }}</span></a><br>
                                    @endif
                                    @if($program->subPrograms->count() > 0)
                                    <div class="dropdown">
                                        <button class="dropdown-button">View Children</button>
                                        <div class="dropdown-content">
                                            @foreach($program->subPrograms as $p)
                                            <a target="_blank" href="{{ route('programs.edit', $p->id)}}">{{ $p->p_name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                   
                                    {{-- {{dd($program->subPrograms)}}
                                    @foreach
                                    <span style="color:magenta"><strong>Children:</strong>{{ $program->parent->p_name }}</span><br> --}}
                                    @endif
                                </span>
                                <a href="{{ route('program.detailsexport', $program->id) }}"><span style="color:brown;"><i class="fa fa-download"></i> Export Participant's details</span></a>

                                @if($program->status == 1) <br><a  href="{{ url('/trainings').'/'.$program->id }}" target="_blank"> <i class="fa fa-eye"></i> Preview Program</a> @endif  <br>
                                @if(!empty(array_intersect(adminRoles(), Auth::user()->role())))
                                <a data-toggle="tooltip" data-placement="top" title="Edit Training"
                                    class="btn btn-info btn-xs" href="{{ route('programs.edit', $program->id)}}"><i
                                        class="fa fa-edit"></i> Edit
                                </a> 
                                @endif
                                @if(!empty(array_intersect(adminRoles(), Auth::user()->role())))
                                    @if($program->hascrm == 0)
                                        <a data-toggle="tooltip" onclick="return confirm('Are you really sure?');" data-placement="top" title="Enable CRM"
                                            class="btn btn-primary btn-xs" href="{{ route('crm.show', $program->id)}}" ><i
                                                class="far fa-comments"></i> Enable CRM
                                        </a>
                                        @else
                                        <a data-toggle="tooltip" onclick="return confirm('Are you really sure?');"  data-placement="top" title="Disable CRM"
                                            class="btn btn-primary btn-xs" href="{{ route('crm.hide', $program->id)}}" ><i class="fa fa-ban"> Disable CRM</i>
                                        </a>
                                        
                                    @endif
                                @endif
                                @if($program->hasresult == 0)
                                    <a data-toggle="tooltip" data-placement="top" title="Enable User Results"
                                        class="btn btn-success btn-xs" href="{{ route('results.enable', $program->id)}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-graduation-cap"></i> Enable result
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Disable User Results"
                                        class="btn btn-info btn-xs" href="{{ route('results.disable', $program->id)}}" ><i onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i> Disable Result
                                    </a>
                                @endif

                            </td>
                            
                            <td><strong>Normal Fee:</strong> {{ \App\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->p_amount) }} <br>
                               <strong>EarlyBird:</strong> {{ \App\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION'). number_format($program->e_amount) }}
                            </td>
                            <td> <strong>Start:</strong> {{ $program->p_start }} <br>
                                <strong>End: </strong>{{ $program->p_end }}
                            </td>
                            <td>Part: {{ $program->part_paid }} <br>
                                Full: {{ $program->fully_paid }}
                            </td>
                            <td>
                                @if( $program->status == 1 )
                                <button class="btn btn-success btn-xs">Published</button> 
                                @else
                                <button class="btn btn-danger btn-xs">Draft</button> 
                                @endif
                            </td>
                            @if(!empty(array_intersect(adminRoles(), Auth::user()->role())))
                            <td style="vertical-align: unset;">
                                <div class="" style="margin-bottom: 5px;">
                                    <a data-toggle="tooltip" data-placement="top" title="Reset Participant's password" class="btn btn-dark btn-xs" href="{{ route('password.reset', $program->id)}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i> Reset Password
                                    
                                    @if($program->close_registration == 0)
                                    <a data-toggle="tooltip" data-placement="top" title="Close registration" class="btn btn-danger btn-xs" href="{{ route('registration.close', $program->id)}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i> Close registration
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Registration"
                                        class="btn btn-success" href="{{ route('registration.open', $program->id)}}" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-window-restore"></i>
                                    </a>
                                    @endif
                                    @endif                                   
                                </div>
                                
                                @if(!empty(array_intersect(adminRoles(), Auth::user()->role())))
                                <div class="" style="margin-bottom: 5px;">
                                    <a data-toggle="tooltip" data-placement="top" title="Clone Training"
                                        class="btn btn-success btn-xs" style="background:#183153" href="{{ route('training.clone', $program->id)}}" onclick="return confirm('This will clone training materials, modules, questions, settings, etc?');"><i class="fa fa-copy"></i> Clone Training
                                    </a>
                                    <a data-toggle="tooltip" data-placement="top" title="Import Participants"
                                        class="btn btn-dark btn-xs" style="background:#183153" href="{{ route('training.import', $program->id)}}"><i class="fa fa-upload"></i> Bulk Import
                                    </a>
                                    <form action="{{ route('programs.destroy', $program->id) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to trash?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-warning btn-xs" data-toggle="tooltip"
                                            data-placement="top" title="Trash Training"> <i class="fa fa-recycle"></i> Trash
                                        </button>
                                    </form>
                                </div>
                                @if($program->e_amount > 0)
                                <div class="extra-actions" style="padding-top:10px">
                                    @if($program->close_earlybird == 1)
                                    <a data-toggle="tooltip" data-placement="top" title="Close Early Bird Payment"
                                            class="btn btn-info" href="{{ route('earlybird.close', $program->id)}}" ><i
                                            onclick="return confirm('Are you really sure?');" class="fa fa-folder-open"></i>
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Early Bird Payment"
                                            class="btn btn-info" href="{{ route('earlybird.open', $program->id)}}" ><i
                                            onclick="return confirm('Are you really sure?');" class="fa fa-folder"></i>
                                    </a>
                                    @endif
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
    <script>
        document.querySelector('.dropdown-button').addEventListener('click', function() {
            const dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    </script>
@endsection