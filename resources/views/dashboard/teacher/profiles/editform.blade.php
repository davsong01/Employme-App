<div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">Name</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}"
                    autofocus>
                @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email">E-Mail Address</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email }}"
                    disabled>
                @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('t_phone') ? ' has-error' : '' }}">
                <label for="t_phone">Phone</label>
                <input id="t_phone" type="text" class="form-control" name="t_phone" value="{{ old('t_phone') ?? $user->t_phone }}"
                    autofocus>
                @if ($errors->has('t_phone'))
                <span class="help-block">
                    <strong>{{ $errors->first('t_phone') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                <label for="location">Location</label>
                <input id="location" type="text" class="form-control" name="location"
                    value="{{ old('location') ?? $user->t_location }}" autofocus disabled>
                @if ($errors->has('location'))
                <span class="help-block">
                    <strong>{{ $errors->first('location') }}</strong>
                </span>
                @endif
            </div>
          
            <div class="form-group">
                <label>Profile Picture</label>
                <input type="file" name="file" value="" class="form-control">
            </div>
            <div><small style="color:red">{{ $errors->first('file')}}</small></div>
            <div><small style="color:red">{{ $errors->first('class')}}</small></div>
            
        </div>
        <div class="col-md-6">
                <div class="form-group">
                        <label for="training">Current Training</label>
                        <input id="training" type="text" class="form-control" name="training" value="{{ $user->program->p_name }}"
                            autofocus disabled>
                        @if ($errors->has('training'))
                        <span class="help-block">
                            <strong>{{ $errors->first('training') }}</strong>
                        </span>
                        @endif
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
                    
            <div class="form-group">
                <label for="class">Role</label>
                <input id="role_id" type="text" class="form-control" name="role_id" value="{{ $user->role_id }}" autofocus
                    disabled>
                <div><small style="color:red">{{ $errors->first('role')}}</small></div>
            </div>
            <div class="form-group">
                <label for="class">Gender</label>
                <select name="gender" id="class" class="form-control" required>
                    <option value="" disabled>Select Gender</option>
                    <option value="1" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                    <option value="0" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
    
                </select>
                <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
            </div>
        </div>
    </div>
    <div class="row">
        <button type="submit" class="btn btn-primary" style="width:100%">
            Submit
        </button>
    </div>
    {{ csrf_field() }}