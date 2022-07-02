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
                        <h4 class="card-title">Edit details for: {{$user->name}}</h4>
                    </div>
                    <form action="{{route('teachers.update', $user->id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <img src="{{ asset('profiles/'. $user->profile_picture  )}}" alt="{{ $user->profile_picture }}" class="rounded-circle" width="100"
                                    height="100">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <table class="table table-bordered">
                                        <th><strong>Trainings</strong><a href="{{ route('teachers.programs', $user->id) }}" target="_blank" class="btn btn-info btn-sm view"> View</a></th>
                                        <th><strong>Downlines</strong><a href="{{ route('teachers.students', $user->id) }}" class="btn btn-info btn-sm view" target="_blank"> View</a></th>
                                        <th><strong>WTN License</strong></th>
                                        <th><strong>Off season</strong></th>
                                        <th><strong>Total Earnings</strong> <a href="{{ route('teachers.earnings', $user->id) }}" class="btn btn-info btn-sm view" target="_blank"> View</a> </th>
                                        
                                        <tr>
                                           <td>{{ $programs->count() }}</td>
                                           <td>{{  $user->students_count }} </td>
                                           <td>{{ $user->license }}</td>
                                           <td>{{ isset($user->off_season_availability) ? 'Yes' : 'No'}}</td>
                                           <td>{{ \App\Settings::first()->value('DEFAULT_CURRENCY') }}{{ $user->earnings }}</td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                           
                        </div>
                         <div class="row" style="margin-top:20px">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="class">Role*</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Facilitator" {{ $user->role_id == 'Facilitator' ? 'selected' : ''}}>Facilitator</option>
                                        <option value="Grader" {{ $user->role_id == 'Grader' ? 'selected' : ''}}>Grader</option>
                                        <option value="Admin" {{ $user->role_id == 'Admin' ? 'selected' : ''}}>Admin</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                               
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}" autofocus >
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email}}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="phone" class="form-control" name="phone" value="{{ old('phone') ?? $user->phone}}">
                                    @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Change Profile Picture</label>
                                    <input type="file" name="file" value="" class="form-control">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="earning_per_head">Earning per head ({{ \App\Settings::first()->value('DEFAULT_CURRENCY') }})</label>
                                    <input id="earning_per_head" type="number" step="0.01" class="form-control" name="earning_per_head" value="{{ old('earning_per_head') ?? $user->earning_per_head}}">
                                    @if ($errors->has('earning_per_head'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('earning_per_head') }}</strong>
                                    </span>
                                    @endif
                                </div> --}}
                            
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password">Password: </label><span class="help-block">
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
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;" required>
                                        @foreach($allprograms as $allprogram)
                                             <option value="{{ $allprogram->id }}" {{ in_array($allprogram->id, $user->trainings->pluck('program_id')->toArray()) ? 'selected' : ''}} >{{ $allprogram->p_name . $allprogram->id }}</option>
                                        @endforeach
                                        </select>
                                    <div>
                                        @if ($errors->has('training'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('training') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                <div>
                            </div>
                        </div>
                       {{ csrf_field() }}
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                        {{-- @if($students->count() > 0)
                        <div class="row" style="padding-top:20px">
                            <div class="col-md-12">
                                <h3>Assigned Students</h3>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Avatar</th>
                                                <th>Joined</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                            @foreach($students as $student)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td><img src="{{ asset('profiles/'. $student->profile_picture  )}}" alt="{{ $student->profile_picture }}" class="rounded-circle" width="50"
                                                        height="50"></td>
                                                    <td>{{ $student->created_at->format('d/m/Y') }}</td>
                                                    <td>
                                                        <strong>{{ $student->name }}</strong><br>
                                                    </td>
                                                    <td>{{ $student->t_phone }}</td>
                                                    
                                                </tr>
                                            @endforeach
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif --}}
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('ckeditor');
</script>
@endsection
