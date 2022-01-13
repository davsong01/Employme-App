@extends('dashboard.admin.index')
@section('title', $user->name )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">{{$user->name}}</h4>
                    </div>
                    <form action="{{route('profiles.update', $user->id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <img src="{{ asset('profiles/'. $user->profile_picture  )}}" alt="{{ $user->profile_picture }}" class="rounded-circle" width="100"
                                    height="100">
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
                                    <input id="email" disabled class="form-control" value="{{ $user->email}}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
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
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control">
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="1" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="class">Available off season?</label>
                                    <select name="off_season_availability" id="class" class="form-control">
                                        <option value="" {{ is_null($user->off_season_availability) ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $user->off_season_availability == '1' ? 'selected' : ''}}>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                   <label for=""></label>
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                 </div>
                                 <div class="form-group">
                                    <label for=""></label>
                                 </div>
                                 <div class="form-group">
                                    
                                 </div>
                               
                                 <div class="form-group">
                                    <label for="t_phone">Phone</label>
                                    <input id="t_phone" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}">
                                </div>
                                <div class="form-group">
                                    <label>Change Profile Picture</label>
                                    <input type="file" name="file" value="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="earning_per_head">Earning per head ({{ \App\Settings::first()->value('DEFAULT_CURRENCY') }})</label>
                                    <input step="0.01"  disabled class="form-control" value="{{ $user->earning_per_head }}">
                                </div>
                                <div class="form-group">
                                    <label style="color:green" for="">Total Earnings : {{ \App\Settings::first()->value('DEFAULT_CURRENCY') }}{{ $user->earnings ? number_format($user->earnings) : 0 }}</label>
                                    
                                </div>
                              
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
                            <div class="col-12">
                                <div class="form-group">
                                        <label class="training">My Training(s):</label> <br>
                                        @foreach(auth()->user()->trainings as $program)
                                        {{ $program->programName->p_name }} <span style="color:blue">||</span> 
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
                                Submit
                            </button>
                        </div>
                        @if($students->count() > 0)
                        <div class="row" style="padding-top:20px">
                            <div class="col-md-12">
                                <h3>My Students </h3>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Avatar</th>
                                                <th>Joined</th>
                                                <th>Name</th>
                                                <th>Training</th>
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
                                                    <td>{{ $single_program->p_name }}</td>
                                                    <td>{{ $student->t_phone }}</td>
                                                </tr>
                                            @endforeach
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
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
