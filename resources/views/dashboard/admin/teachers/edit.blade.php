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
                        <h4 class="card-title">{{$user->name}}</h4>
                         @if(!empty(array_intersect(facilitatorRoles(), $user->role())))  
                        <p>Referal link: <b id="link" style="color:blue">{{ url('/') .'/'.'?facilitator='. $user->license}}</b> <br>
                            WAACSP Profile link: <b>{{ $user->waaccsp_link }}</b>
                        </p>
                        @endif
                    </div>
                    <form action="{{route('teachers.update', $user->id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <img src="{{ $user->image }}" alt="avatar" class="rounded-circle" width="100" height="100">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <table class="table table-bordered">
                                        <th><strong>Trainings</strong><a href="{{ route('teachers.programs', $user->id) }}" target="_blank" class="btn btn-info btn-sm view"> View</a></th>
                                        @if(!empty(array_intersect(facilitatorRoles(), $user->role())))
                                        <th><strong>Students</strong><a href="{{ route('teachers.students', $user->id) }}" class="btn btn-info btn-sm view" target="_blank"> View</a></th>
                                        <th><strong>WTN License</strong></th>
                                        <th><strong>Off season</strong></th>
                                        <th><strong>Total Earnings</strong> <a href="{{ route('teachers.earnings', $user->id) }}" class="btn btn-info btn-sm view" target="_blank"> View</a> </th>
                                        @endif
                                        <tr>
                                           <td>{{ $programs->count() }}</td>
                                           @if(!empty(array_intersect(facilitatorRoles(), $user->role())))    
                                           <td>{{  $user->students_count }} </td>
                                           <td>{{ $user->license }}</td>
                                           <td>{{ $user->off_season_availability == 1 ? 'Yes' : 'No'}}</td>
                                           <td>{{ $user->payment_modes->currency_symbol ?? 'NGN' }}{{ number_format($user->earnings) }}</td>
                                           @endif
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                           
                        </div>
                         <div class="row" style="margin-top:20px">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role*</label>
                                    <select name="role[]" id="role" class="select2 role form-control" multiple="multiple" style="height: 30px;width: 100%;">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Facilitator" {{ !empty(array_intersect(facilitatorRoles(), $user->role()))  ? 'selected' : ''}}>Facilitator</option>
                                        <option value="Grader" {{ !empty(array_intersect(graderRoles(), $user->role())) ? 'selected' : ''}}>Grader</option>
                                        <option value="Admin" {{ !empty(array_intersect(adminRoles(), $user->role())) ? 'selected' : ''}}>Admin</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label for="status">Status</label>
                                    <select name="status" id="type" class="form-control" required>
                                        <option value="active" {{ $user->status == 'active' ? 'selected':'' }}>Active</option> 
                                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected':'' }}>Inactive</option>
                                    </select>
                                </div>
                                @if(!empty(array_intersect(facilitatorRoles(), $user->role())))  
                                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label for="payment_mode">Payment Mode</label>
                                    <select name="payment_mode" id="payment_mode" class="form-control" required>
                                        <option value="">...Select</option>
                                        @foreach($payment_modes as $mode)
                                        <option value="{{ $mode->id }}" {{ $user->payment_mode == $mode->id ? 'selected':'' }}>{{ ucFirst($mode->name) }}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}" autofocus >
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email}}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="phone" class="form-control" name="phone" value="{{ old('phone') ?? $user->t_phone}}">
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
                                    <label for="earning_per_head">Earning per head ({{ \App\Models\Settings::first()->value('DEFAULT_CURRENCY') }})</label>
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
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;">
                                        @foreach($allprograms as $allprogram)
                                             <option value="{{ $allprogram->id }}" {{ in_array($allprogram->id, $user->trainings->pluck('program_id')->toArray()) ? 'selected' : ''}}>{{ $allprogram->p_name }}</option>
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
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12">
                                <span><h6>Admin Menu Permissions</h6></span>
                            </div>
                            <?php
                                $a_menu = $user->menu_permissions ?? '';
                                $a_menu = explode(',', $a_menu);
                            ?>
                            @foreach(app('app\Http\Controllers\Controller')->adminMenus() as $menu)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="menu_permissions[]" value="{{$menu['id']}}" id="{{$menu['id']}}" {{ in_array($menu['id'], $a_menu) ? 'checked' : ''}}>
                                    <label class="form-check-label" for="{{$menu['id']}}">
                                        {{$menu['name']}}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                       {{ csrf_field() }}
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                      
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
