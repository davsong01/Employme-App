@extends('dashboard.admin.index')
@section('title', 'Trainings')
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
                            <th>S/N</th>
                            <th>Banner</th>
                            <th>Title</th>
                            <th>Fee</th>
                            <th>Dates</th>
                            <th>Payment Stats</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                                @if($program->parent)
                                <strong>Parent:</strong> <span style="color:red"> {{ $program->parent->p_name }}</span><br>
                                @endif
                                <a href="{{ route('program.detailsexport', $program->id) }}"><span style="color:brown;font-size: smaller;"><i class="fa fa-download"></i> Export Participant's details</span></a>
                                @if($program->status == 1) <br><a style="font-size: smaller;" href="{{ url('/trainings').'/'.$program->id }}" target="_blank"> <i class="fa fa-eye"></i> Preview Program</a> @endif  <br>
                                <a data-toggle="tooltip" data-placement="top" title="Edit Training"
                                    class="btn btn-info btn-xs" href="{{ route('programs.edit', $program->id)}}"><i
                                        class="fa fa-edit"></i> Edit
                                </a> 
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
                            <td style="vertical-align: unset;">
                                <div class="" style="margin-bottom: 5px;">
                                    
                                    @if($program->close_registration == 0)
                                    <a data-toggle="tooltip" data-placement="top" title="Close registration" class="btn btn-danger btn-xs" href="{{ route('registration.close', $program->id)}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i>Close registration
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Registration"
                                        class="btn btn-success" href="{{ route('registration.open', $program->id)}}" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-window-restore"></i>
                                    </a>
                                    @endif

                                   
                                </div>
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
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>
@endsection