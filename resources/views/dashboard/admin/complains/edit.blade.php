@extends($extend)
@section('title', 'View/Update Complain')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{route('complains.update', $complain->id)}}" method="POST" class="pb-2">
                        {{ method_field('PATCH') }}
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <h4>Customer Details</h4>
                                </div>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Customer Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{$complain->name }}" disabled autofocus>
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email"
                                        value="{{ $complain->email }}" disabled>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                        value="{{ $complain->phone}}" disabled autofocus>
                                    @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <input id="address" type="text" class="form-control" name="gender"
                                        value="{{ old('gender') ?? $complain->gender}}" disabled autofocus>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>

                                <div class="form-group{{ $errors->has('Address') ? ' has-error' : '' }}">
                                    <label for="address">Customer Address</label>
                                    <input id="address" type="text" class="form-control" name="address"
                                        value="{{ old('address') ?? $complain->address}}" disabled autofocus>
                                    @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                                    <label for="state">State *</label>
                                    <input id="state" type="text" class="form-control" name="state"
                                        value="{{$complain->state}}" autofocus disabled>
                                    @if ($errors->has('state'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('state') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('LGA') ? ' has-error' : '' }}">
                                    <label for="LGA">LGA *</label>
                                    <input id="state" type="text" class="form-control" name="state"
                                        value="{{$complain->lga}}" disabled>

                                    @if ($errors->has('LGA'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('LGA') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('other') ? ' has-error' : '' }}">
                                    <label for="other">Other Details</label>
                                    <input id="other" type="text" class="form-control" name="other"
                                        value="{{ $complain->other}}" autofocus disabled>
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
                                    <label for="mode">Mode *</label>
                                    <select name="mode" id="class" class="form-control">
                                        <option value="Phone Call"
                                            {{ $complain->mode == 'Phone Call' ? 'selected' : ''}}>Phone Call</option>
                                        <option value="Email" {{ $complain->mode == 'Email' ? 'selected' : ''}}>Email
                                        </option>
                                        <option value="Whatsapp" {{ $complain->mode == 'Whatsapp' ? 'selected' : ''}}>
                                            Whatsapp</option>
                                        <option value=Twitter {{ $complain->mode == 'Twitter' ? 'selected' : ''}}>
                                            Twitter</option>
                                        <option value="Facebook" {{ $complain->mode == 'Facebook' ? 'selected' : ''}}>
                                            Facebook</option>
                                        <option value="Instagram" {{ $complain->mode == 'Instagram' ? 'selected' : ''}}>
                                            Instagram</option>
                                        <option value="Other" {{ $complain->mode == 'other' ? 'selected' : ''}}>Other
                                        </option>
                                    </select>
                                    @if ($errors->has('mode'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mode') }}</strong>
                                    </span>
                                    @endif
                                </div>


                                <div class="form-group">
                                    <label for="class">Type</label>
                                    <select name="type" id="class" class="form-control">
                                        <option value="Drop Balance"
                                            {{ $complain->type == 'Drop Balance' ? 'selected' : ''}}>Drop Balance
                                        </option>
                                        <option value="Network Issues"
                                            {{ $complain->type == 'Network Issues' ? 'selected' : ''}}>Network Issues
                                        </option>
                                        <option value="Recharge Issues"
                                            {{ $complain->type == 'Recharge Issues' ? 'selected' : ''}}>Recharge Issues
                                        </option>
                                        <option value="Data Issues"
                                            {{ $complain->type == 'Data Issues' ? 'selected' : ''}}>Data Issues</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Priority</label>
                                    <select name="priority" id="class" class="form-control" required>
                                        <option value="Low" {{ $complain->priority == 'Low' ? 'selected' : ''}}>Low
                                        </option>
                                        <option value="Medium" {{ $complain->priority == 'Medium' ? 'selected' : ''}}>
                                            Medium</option>
                                        <option value="High" {{ $complain->priority == 'Hight' ? 'selected' : ''}}>High
                                        </option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('priority')}}</small></div>
                                </div>

                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="class" class="form-control">
                                        <option value="" selected="selected">- Select -</option>
                                        <option value="Pending" {{ $complain->status == 'Pending' ? 'selected' : ''}}>
                                            Pending</option>
                                        <option value="In Progress"
                                            {{ $complain->status == 'In Progress' ? 'selected' : ''}}>In Progress
                                        </option>

                                        @if(Auth::user()->role_id == "Admin")
                                        <option value="Resolved" {{ $complain->status == 'Resolved' ? 'selected' : ''}}>
                                            Resolved</option>
                                        @endif


                                    </select>
                                    <div><small style="color:red">{{ $errors->first('status')}}</small></div>
                                </div>

                                <div class="form-group{{ $errors->has('teamlead') ? ' has-error' : '' }}">
                                    <label for="teamlead">Team Lead</label>
                                    <input id="teamlead" type="text" class="form-control" name="teamlead"
                                        value="{{ old('teamlead') ?? $complain->teamlead}}" autofocus>
                                    @if ($errors->has('teamlead'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('teamlead') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('complain') ? ' has-error' : '' }}">
                                    <label for="complain">Complain Content</label>

                                    <textarea id="complain" type="text" class="form-control" name="content"
                                        value="{{ old('content') ?? $complain->content}}"
                                        autofocus>{{ old('content') ?? $complain->content}}</textarea>
                                    @if ($errors->has('content'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('content') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('response') ? ' has-error' : '' }}">
                                    <label for="complain">Complain Response</label>

                                    <textarea id="response" type="text" class="form-control" name="response"
                                        value="{{ old('response') ?? $complain->response}}"
                                        autofocus>{{ old('response') ?? $complain->response}}</textarea>
                                    @if ($errors->has('response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('response') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Update
                            </button>
                        </div>
                        {{ csrf_field() }}
                </div>
            </div>
        </div>
    </div>
    @endsection