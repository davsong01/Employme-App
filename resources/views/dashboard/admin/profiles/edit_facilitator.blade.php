@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
<style>
    .select2-container--default .select2-selection--multiple {
        line-height: 27px;
        overflow: scroll;
        height: 150px;
    }
    .view{
        margin: 0 10px;
        border-radius: 10%;
    }
</style>

@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Edit details for: {{$user->name}} <br>
                            <small>Referal link: <b id="link" style="color:blue">{{ url('/') .'/'.'?facilitator='. $user->license}}</b></small>
                        </h4>
                    </div>
                    <form action="{{route('profiles.update', $user->id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <img src="{{ (filter_var(Auth::user()->profile_picture, FILTER_VALIDATE_URL) !== false) ? Auth::user()->profile_picture : url('/'). '/avatars'.'/'.Auth::user()->profile_picture }}" class="rounded-circle" width="100"
                                    height="100">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <table class="table table-bordered">
                                        <th><strong>Trainings</strong><a href="{{ route('teachers.programs', $user->id) }}" target="_blank" class="btn btn-info btn-sm view"> View</a></th>
                                        <th><strong>Students</strong><a href="{{ route('teachers.students', $user->id) }}" class="btn btn-info btn-sm view" target="_blank"> View</a></th>
                                        <th><strong>WTN License</strong></th>
                                        <th><strong>Off season</strong></th>
                                        <th><strong>Total Earnings</strong> <a href="{{ route('teachers.earnings', $user->id) }}" class="btn btn-info btn-sm view" target="_blank"> View</a> </th>
                                        
                                        <tr>
                                           <td>{{ $user->programCount }}</td>
                                           <td>{{  $user->students_count }} </td>
                                           <td>{{ $user->license }}</td>
                                           <td>{{ $user->off_season_availability == 1 ? 'Yes' : 'No'}}</td>
                                           <td>{{ \App\Models\Settings::first()->value('DEFAULT_CURRENCY') }}{{ $user->earnings }}</td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                           
                        </div>
                         <div class="row" style="margin-top:20px">
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}" autofocus >
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Available for off Season Programs?</label>
                                    <select name="off_season" class="form-control" id="off_season" required>
                                        <option value="1" {{ $user->off_season_availability == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $user->off_season_availability == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">Change Password: </label><span class="help-block">
                                        <strong>Default: 12345</strong>
                                    </span>
                                    <input id="password" type="text" class="form-control" name="password" value="{{ old('password') ?? '' }}"
                                        autofocus>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                               
                                <div class="form-group{{ $errors->has('t_phone') ? ' has-error' : '' }}">
                                    <label for="t_phone">t_Phone</label>
                                    <input id="t_phone" type="t_phone" class="form-control" name="t_phone" value="{{ old('t_phone') ?? $user->t_phone}}">
                                    @if ($errors->has('t_phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('t_phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Change Profile Picture</label>
                                    <input type="file" name="image" value="" class="form-control">
                                </div>
                               
                                
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group{{ $errors->has('profile') ? ' has-error' : '' }}">
                                    
                                    <label for="profile" style="color:red">Profile overview</label>
                                    <textarea id="ckeditor" type="text" class="form-control" name="profile" value="{{ old('profile') ?? $user->profile }}" rows="8" autofocus>{!!  $user->profile !!}</textarea>

                                    @if ($errors->has('profile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('profile') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                       {{ csrf_field() }}
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Update
                            </button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    CKEDITOR.replace('ckeditor');
</script>
<script>
    function copy(){
        var copyTextarea = document.getElementById("link");
        copyTextarea.textContent.select(); //select the text area
        document.execCommand(copyTextarea); //copy to clipboard
    }
</script>
@endsection

