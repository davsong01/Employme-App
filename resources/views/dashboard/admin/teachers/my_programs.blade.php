@extends('dashboard.admin.index')
@section('title', 'All Facilitators')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title">Facilitators $ Graders <a href="{{route('teachers.create')}}"><button type="button" class="btn btn-outline-primary">Add New</button></a></h5> 
                </div>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Trainings</th>
                            <th>Direct Students</th>
                            <th>Earnings</th>
                            <th>Off Season</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td><img src="{{ asset('profiles/'. $user->profile_picture  )}}" alt="{{ $user->profile_picture }}" class="rounded-circle" width="50"
                                height="50"></td>
                            <td>
                                <small>
                                <strong>Name: </strong> {{ $user->name }}<br>
                                <strong>Email: </strong> {{ $user->email }}<br>
                                <strong>Phone: </strong> {{ $user->t_phone }}<br>
                                
                                <strong>Assigned trainings: </strong>{{ $user->trainings->count() }} <br>
                                @if($user->license)
                                <strong style="color:green">WTN License: </strong> <span style="color:green">{{ $user->license }}</span> <br>
                                @endif
                                </small>
                            </td>
                           
                            <td> <button class="disabled btn btn-{{ $user->role_id == 'Facilitator' ? 'primary' : 'info'}} btn-sm">{{ $user->role_id}}</button></td>
                            <td>
                                <small>
                                    @foreach($user->p_names as $index=>$names)
                                    <strong style="color:red">@if($index < count($user->p_names))|| @endif</strong>{{ $names }} @if($index < count($user->p_names)-1)<br>@endif
                                    @endforeach
                                </small>
                            </td>
                            <td>{{ $user->students_count }}</td>
                            
                            <td>{{ \App\Settings::first()->value('DEFAULT_CURRENCY') }}{{ $user->earnings ? number_format($user->earnings) : 0 }}</td>
                          
                            <td>{{ $user->off_season_availability == 1 ? 'Yes' : 'No' }}</td>
                                          
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit facilitator"
                                        class="btn btn-info" href="{{ route('teachers.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>                                   
                                    <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                    class="btn btn-warning" href="{{ route('impersonate', $user->id) }}"><i
                                        class="fa fa-unlock"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete facilitator"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endforeach
                    </tbody>
                   
                </table>
            </div>

        </div>
    </div>
</div>
{{-- Payment modal --}}
<!-- Button to Open the Modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
    Open modal
  </button>
  
  <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
  
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Modal Heading</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <!-- Modal body -->
        <div class="modal-body">
          Modal body..
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
  
      </div>
    </div>
  </div>
  {{-- end payment modal --}}
@endsection