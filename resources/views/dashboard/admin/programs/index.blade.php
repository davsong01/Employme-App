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
                </div> <div>
                    <h5 class="card-title"> Actions Legend:</h5>
                    <p style="color:green">1. Edit Training | 2. Close/Extend Registration | 3. Enable/Disable CRM | 4. Enable/Disable Result Availability| 5. Trash Training | 6. Close EarlyBird Payment(if applicable)  </p> 
                </div>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Program Title</th>
                            <th>Training Fee</th>
                            <th>Early Bird Fee</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Partly Paid</th>
                            <th>Fully Paid</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $program->p_name }}<br><span
                                    style="color:red">{{ config('app.url') }}/paystack?id={{ $program->id }}&t=</span>
                            </td>
                            <td>₦{{ $program->p_amount }}</td>
                            <td>₦{{ $program->e_amount }}</td>
                            <td>{{ config('app.currency') }}{{ $program->p_start }}</td>
                            <td>{{ $program->p_end }}</td>
                            <td>{{ $program->part_paid }}</td>
                            <td>{{ $program->fully_paid }}</td>
                            <td>{{ $program->status == 1 ? 'Published' : 'Draft'}}</td>
                            <td>
                                <div class="btn-group">

                                    <a data-toggle="tooltip" data-placement="top" title="Edit Training"
                                        class="btn btn-info" href="{{ route('programs.edit', $program->id)}}" onclick="return confirm('Are you really sure?');"><i
                                            class="fa fa-edit"></i>
                                    </a>

                                    @if($program->close_registration == 0)
                                    <a data-toggle="tooltip" data-placement="top" title="Close registration" class="btn btn-danger" href="{{ route('registration.close', $program->id)}}" onclick="return confirm('Are you really sure?');"><i class="fa fa-window-close"></i>
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Extend Registration"
                                        class="btn btn-success" href="{{ route('registration.open', $program->id)}}" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-window-restore"></i>
                                    </a>
                                    @endif

                                    @if($program->hascrm == 0)
                                    <a data-toggle="tooltip" data-placement="top" title="Enable CRM"
                                        class="btn btn-primary" href="{{ route('crm.show', $program->id)}}" onclick="return confirm('Are you really sure?');"><i
                                            class="far fa-comments"></i>
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Disable CRM"
                                        class="btn btn-info" href="{{ route('crm.hide', $program->id)}}" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i>
                                    </a>
                                    @endif
                                    @if($program->hasresult == 0)
                                    <a data-toggle="tooltip" data-placement="top" title="Enable User Results"
                                        class="btn btn-success" href="{{ route('results.enable', $program->id)}}" onclick="return confirm('Are you really sure?');"><i class="fas fa-user-graduate"></i>
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Disable User Results"
                                        class="btn btn-info" href="{{ route('results.disable', $program->id)}}" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('programs.destroy', $program->id) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to trash?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-warning" data-toggle="tooltip"
                                            data-placement="top" title="Trash Training"> <i class="fa fa-recycle"></i>
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