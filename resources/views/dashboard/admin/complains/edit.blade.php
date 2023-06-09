@extends($extend)
@section('title', 'Edit Query')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                    </div>
                    <form action="{{route('complains.update',  ['complain'=>$complain->id] ) }}" method="POST" class="pb-2">
                        {{ method_field('PATCH') }}

                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <h4 style="text-align: center; color: blue">Customer Personal Details</h4>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Customer Name</label>
                                    <input id="name" type="text" class="form-control" name="name"
                                        value="{{ $complain->name }}" disabled " autofocus>
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                               
                            </div>
                            <div class="col-md-4">
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
                            </div>
                            <div class="col-md-2">
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                    value="{{ $complain->phone }}" disabled autofocus>
                                    @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Gender</label>
                                    <input id="gender" type="text" class="form-control" name="gender"
                                        value="{{ old('gender') ?? $complain->gender}}" disabled autofocus>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group{{ $errors->has('Address') ? ' has-error' : '' }}">
                                    <label for="address">Customer Address</label>
                                    <input id="address" type="text" class="form-control" name="address"
                                    value="{{ $complain->address}}" disabled autofocus>
                                    @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                                    <label for="state">State *</label>
                                    <input id="state" type="text" class="form-control" name="state"
                                    value="{{$complain->state}}" autofocus disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group{{ $errors->has('LGA') ? ' has-error' : '' }}">
                                    <label for="LGA">LGA *</label>
                                    <input id="state" type="text" class="form-control" name="state"
                                    value="{{$complain->lga}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                            </div>
                            <div class="col-md-2">
                                <div class="form-group{{ $errors->has('other') ? ' has-error' : '' }}">
                                    <label for="other">Other Details</label>
                                    <input id="other" type="text" class="form-control" name="other"
                                        value="{{ $complain->other }}" autofocus>
                                    @if ($errors->has('other'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div><small style="color:red">{{ $errors->first('class')}}</small></div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <h4 style="text-align: center; color: blue">Query Details</h4>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="Complain" {{ $complain->type == 'Complain' ? 'selected' : ''}}>Complain</option>
                                        <option value="Enquiry" {{ $complain->type == 'Enquiry' ? 'selected' : ''}}>Enquiry</option>
                                        <option value="Request" {{ $complain->type == 'Request' ? 'selected' : ''}}>Request</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="class">Issues</label>
                                    <select name="issues" id="issues" id="class" class="form-control">
                                        <option value="{{ $complain->issues }}" selected="selected">{{ $complain->issues }}</option>
                                        
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="class">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="{{ $complain->status }}" selected="selected">{{ $complain->status }}</option>
                                        @if(Auth::user()->role_id == "Admin" || Auth::user()->role_id == "Facilitator")
                                        <option value="Resolved" {{ $complain->status == 'Resolved' ? 'selected' : ''}}>
                                            Resolved</option>
                                        @endif

                                    </select>
                                    <div><small style="color:red">{{ $errors->first('status')}}</small></div>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            </div>
                        </div>

                        <div class="row">            
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('complain') ? ' has-error' : '' }}">
                                    
                                    <label for="complain" style="color:red">Query Content</label>
                                    <textarea id="ckeditor" type="text" class="form-control" name="content" value="{{ old('complain') ?? $complain->content }}" rows="8" autofocus>{!!  $complain->content !!}</textarea>

                                    @if ($errors->has('complain'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('complain') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('response') ? ' has-error' : '' }}">
                                    <label for="response" style="color:green">Your Response</label>
                                    <textarea  id="summary-ckeditor" type="text" class="form-control" name="response"
                                        value="{{ old('response') ?? $complain->response }}" rows="8" autofocus>{!!  $complain->response !!}</textarea>
                                    @if ($errors->has('response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('response') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div> 
                        </div>
                        @if(Auth::user()->role_id == "Admin" || auth()->user()->role_id == "Facilitator" )
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                                    <label for="notes" style="color:green">Supervisor's Note</label>
                                    <textarea class="form-control" name="notes"
                                        value="{{ old('notes') ?? $complain->notes }}" rows="8" autofocus >{{ $complain->notes }}</textarea>
                                    @if ($errors->has('notes'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('notes') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(Auth::user()->role_id == "Student")
                        @if($complain->notes)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                                    <label for="notes" style="color:green">Supervisor's Note</label>
                                    <textarea class="form-control" rows="8" readonly >{{ $complain->notes }}</textarea>
                                    @if ($errors->has('notes'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('notes') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
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
    @section('extra-scripts')
    <script>
        $('#type').on('click', function(){
        console.log($('#type').val());
            $('#issues').html('');
            if($('#type').val()=='Complain'){
                
                $('#issues').append('<option value="Drop Balance">Drop Balance</option>');
                $('#issues').append('<option value="Network Issues">Network Issues</option>');
                $('#issues').append('<option value="Recharge Issues">Recharge Issues</option>');
                $('#issues').append('<option value="Data Issues">Data Issues</option>');
                $('#issues').append('<option value="Late delivery">Late delivery</option>');
                $('#issues').append('<option value="Damages">Damages</option>');
                
                $("#status").html("");
               
                $('#status').append('<option value="Pending">Pending</option>');
                $('#status').append('<option value="In Progress">In Progress</option>');
                @if(Auth::user()->role_id == "Admin" || Auth::user()->role_id == "Facilitator")
                    $('#status').append('<option value="Resolved">Resolved</option>');
                @endif
            }if($('#type').val()=='Enquiry'){
                $('#issues').append('<option value="Product Enquires">Product Enquires</option>');
                $('#issues').append('<option value="Recharge Enquires">Recharge Enquires</option>');
                $('#issues').append('<option value="Opening hours">Opening hours</option>');
                $('#issues').append('<option value="Office location">Office location</option>');
                $('#issues').append('<option value="Cost of product">Cost of product</option>');
                
                $("#status").html("");
                $('#status').append('<option value="Resolved">Resolved</option>');
            }if($('#type').val()=='Request'){
                $('#issues').append('<option value="Product Request">Product Request</option>');
                $('#issues').append('<option value="Recharge Request">Recharge Request</option>');
                $('#issues').append('<option value="Home delivery">Home delivery</option>');
                $('#issues').append('<option value="Exchange ( Size or colour )">Exchange (Size or colour)</option>');
                
                $("#status").html("");
                $('#status').append('<option value="Pending" selected>Pending</option>');
                $('#status').append('<option value="In Progress">In Progress</option>');
                @if(Auth::user()->role_id == "Admin" || Auth::user()->role_id == "Facilitator")
                    $('#status').append('<option value="Resolved">Resolved</option>');
                @endif
            }
            
        });
    </script>
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('summary-ckeditor');
    </script>
    <script>
        CKEDITOR.replace('ckeditor');
    </script>
    @endsection
    @endsection