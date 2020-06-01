    @extends($extend)
    @section('title', 'Add New Complain')
    @section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            @include('layouts.partials.alerts')
                        </div>
                        <form action="{{route('complains.store')}}" method="POST" class="pb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <h4>Customer Details</h4>
                                    </div>
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Customer Name</label>
                                        <input id="name" type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" autofocus>
                                        @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email">E-Mail Address</label>
                                        <input id="email" type="email" class="form-control" name="email"
                                            value="{{ old('email') }}">
                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone">Phone</label>
                                        <input id="phone" type="text" class="form-control" name="phone"
                                            value="{{ old('phone')}}" autofocus>
                                        @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group{{ $errors->has('Address') ? ' has-error' : '' }}">
                                        <label for="address">Customer Address</label>
                                        <input id="address" type="text" class="form-control" name="address"
                                            value="{{ old('address')}}" autofocus>
                                        @if ($errors->has('address'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                        @endif
                                    </div>


                                    <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                                        <label for="state">State *</label>
                                        <select name="state" id="state" class="form-control">
                                            <option value="" selected="selected">- Select -</option>
                                            <option value='Abia'>Abia</option>
                                            <option value='Adamawa'>Adamawa</option>
                                            <option value='AkwaIbom'>AkwaIbom</option>
                                            <option value='Anambra'>Anambra</option>
                                            <option value='Bauchi'>Bauchi</option>
                                            <option value='Bayelsa'>Bayelsa</option>
                                            <option value='Benue'>Benue</option>
                                            <option value='Borno'>Borno</option>
                                            <option value='Cross River'>Cross River</option>
                                            <option value='Delta'>Delta</option>
                                            <option value='Ebonyi'>Ebonyi</option>
                                            <option value='Edo'>Edo</option>
                                            <option value='Ekiti'>Ekiti</option>
                                            <option value='Enugu'>Enugu</option>
                                            <option value='FCT'>FCT</option>
                                            <option value='Gombe'>Gombe</option>
                                            <option value='Imo'>Imo</option>
                                            <option value='Jigawa'>Jigawa</option>
                                            <option value='Kaduna'>Kaduna</option>
                                            <option value='Kano'>Kano</option>
                                            <option value='Katsina'>Katsina</option>
                                            <option value='Kebbi'>Kebbi</option>
                                            <option value='Kogi'>Kogi</option>
                                            <option value='Kwara'>Kwara</option>
                                            <option value='Lagos'>Lagos</option>
                                            <option value='Nasarawa'>Nasarawa</option>
                                            <option value='Niger'>Niger</option>
                                            <option value='Ogun'>Ogun</option>
                                            <option value='Ondo'>Ondo</option>
                                            <option value='Osun'>Osun</option>
                                            <option value='Oyo'>Oyo</option>
                                            <option value='Plateau'>Plateau</option>
                                            <option value='Rivers'>Rivers</option>
                                            <option value='Sokoto'>Sokoto</option>
                                            <option value='Taraba'>Taraba</option>
                                            <option value='Yobe'>Yobe</option>
                                            <option value='Zamfara'>Zamafara</option>
                                        </select>

                                        @if ($errors->has('state'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('state') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group{{ $errors->has('LGA') ? ' has-error' : '' }}">
                                        <label for="LGA">LGA *</label>
                                        <select name="lga" id="lga" class="form-control" required>
                                        </select>

                                        @if ($errors->has('LGA'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('LGA') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="mode">Mode *</label>
                                        <select name="mode" id="mode" class="form-control">
                                            <option value="" selected="selected">- Select -</option>
                                            <option value='Phone Call'>Phone Call</option>
                                            <option value='Email'>Email</option>
                                            <option value='Whatsapp'>Whatsapp</option>
                                            <option value='Twitter'>Twitter</option>
                                            <option value='Facebook'>Facebook</option>
                                            <option value='Instagram'>Instagram</option>
                                            <option value='Other'>Other</option>
                                        </select>
                                        @if ($errors->has('mode'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('mode') }}</strong>
                                        </span>
                                        @endif
                                    </div>


                                    <div class="form-group{{ $errors->has('other') ? ' has-error' : '' }}">
                                        <label for="other">Other Details</label>
                                        <input id="other" type="text" class="form-control" name="other"
                                            value="{{ old('other')}}" autofocus>
                                        @if ($errors->has('other'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div><small style="color:red">{{ $errors->first('class')}}</small></div>

                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <h4>Complain Details</h4>
                                    </div>

                                    <div class="form-group">
                                        <label for="class">Type</label>
                                        <select name="type" id="class" class="form-control">
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Drop Balance">Drop Balance</option>
                                            <option value="Network Issues">Network Issues</option>
                                            <option value="Recharge Issues">Recharge Issues</option>
                                            <option value="Data Issues">Data Issues</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class">Priority</label>
                                        <select name="priority" id="class" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('priority')}}</small></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class">Status</label>
                                        <select name="status" id="class" class="form-control">
                                            <option value="Pending" selected>Pending</option>
                                            <option value="In Progress">In Progress</option>
                                            @if(Auth::user()->role_id == "Admin")
                                            <option value="Resolved">Resolved</option>
                                            @endif
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('status')}}</small></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class">Gender</label>
                                        <select name="gender" id="class" class="form-control">
                                            <option value="" selected>Select gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                    </div>

                                    <div class="form-group{{ $errors->has('teamlead') ? ' has-error' : '' }}">
                                        <label for="teamlead">Team Lead</label>
                                        <input id="teamlead" type="text" class="form-control" name="teamlead"
                                            value="{{ old('teamlead') }}" autofocus>
                                        @if ($errors->has('teamlead'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('teamlead') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <div class="form-group{{ $errors->has('complain') ? ' has-error' : '' }}">
                                        <label for="complain">Complain Content</label>

                                        <textarea id="complain" type="text" class="form-control" name="complain"
                                            value="{{ old('complain') }}" rows="8" autofocus></textarea>
                                        @if ($errors->has('complain'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('complain') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    Submit
                                </button>
                            </div>
                            {{ csrf_field() }}
                    </div>
                </div>
            </div>
        </div>
        @endsection