    @extends($extend)
    @section('title', 'Add New Query')
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
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <h4 style="text-align: center; color: blue">Customer Personal Details</h4>
                                </div>
                                @if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) )
                                <div class="col-md-12 mb-2">
                                    <div class="form-group">
                                        <label for="program_id">Select Training*</label>
                                        <select name="program_id" id="" class="form-control">
                                            @foreach ($programs as $program)
                                            <option value="{{ $program->program_id }}" {{ old('program_id') == $program->program_id ? 'selected' : '' }} required>{{ $program->p_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                               
                                @else
                                <input type="hidden" name="program_id" value="{{ $program->id }}">
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Customer Name*</label>
                                        <input id="name" type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" autofocus>
                                        @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                   
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email">E-Mail Address*</label>
                                        <input id="email" type="email" class="form-control" name="email"
                                            value="{{ old('email') }}" required>
                                        @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone">Phone*</label>
                                        <input id="phone" type="text" class="form-control" name="phone"
                                            value="{{ old('phone')}}" autofocus required>
                                        @if ($errors->has('phone'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('phone') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Gender*</label>
                                        <select name="gender" id="class" class="form-control" required>
                                            <option value="" selected>Choose</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group{{ $errors->has('Address') ? ' has-error' : '' }}">
                                        <label for="address">Customer Address*</label>
                                        <input id="address" type="text" class="form-control" name="address"
                                            value="{{ old('address')}}" autofocus>
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
                                        <select name="state" id="state" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value='Abia' {{ old('state') == 'Abia' ? 'selected' :'' }}>Abia</option>
                                            <option value='Adamawa'  {{ old('state') == 'Adamawa' ? 'selected' :'' }}>Adamawa</option>
                                            <option value='AkwaIbom'  {{ old('state') == 'AkwaIbom' ? 'selected' :'' }}>AkwaIbom</option>
                                            <option value='Anambra'  {{ old('state') == 'Anambra' ? 'selected' :'' }}>Anambra</option>
                                            <option value='Bauchi' {{ old('state') == 'Bauchi' ? 'selected' :'' }}>Bauchi</option>
                                            <option value='Bayelsa {{ old('state') == 'Bayelsa' ? 'selected' :'' }}'>Bayelsa</option>
                                            <option value='Benue' {{ old('state') == 'Benue' ? 'selected' :'' }}>Benue</option>
                                            <option value='Borno' {{ old('state') == 'Borno' ? 'selected' :'' }}>Borno</option>
                                            <option value='Cross River' {{ old('Cross River') == 'Abia' ? 'selected' :'' }}>Cross River</option>
                                            <option value='Delta' {{ old('state') == 'Delta' ? 'selected' :'' }}>Delta</option>
                                            <option value='Ebonyi' {{ old('state') == 'Ebonyi' ? 'selected' :'' }}>Ebonyi</option>
                                            <option value='Edo' {{ old('state') == 'Edo' ? 'selected' :'' }}>Edo</option>
                                            <option value='Ekiti {{ old('state') == 'Ekiti' ? 'selected' :'' }}'>Ekiti</option>
                                            <option value='Enugu' {{ old('state') == 'Enugu' ? 'selected' :'' }}>Enugu</option>
                                            <option value='FCT' {{ old('state') == 'FCT' ? 'selected' :'' }}>FCT</option>
                                            <option value='Gombe' {{ old('state') == 'Gombe' ? 'selected' :'' }}>Gombe</option>
                                            <option value='Imo' {{ old('state') == 'Imo' ? 'selected' :'' }}>Imo</option>
                                            <option value='Jigawa' {{ old('state') == 'Jigawa' ? 'selected' :'' }}>Jigawa</option>
                                            <option value='Kaduna' {{ old('state') == 'Kaduna' ? 'selected' :'' }}>Kaduna</option>
                                            <option value='Kano' {{ old('state') == 'Kano' ? 'selected' :'' }}>Kano</option>
                                            <option value='Katsina' {{ old('state') == 'Katsina' ? 'selected' :'' }}>Katsina</option>
                                            <option value='Kebbi' {{ old('state') == 'Kebbi' ? 'selected' :'' }}>Kebbi</option>
                                            <option value='Kogi' {{ old('state') == 'Kogi' ? 'selected' :'' }}>Kogi</option>
                                            <option value='Kwara' {{ old('state') == 'Kwara' ? 'selected' :'' }}>Kwara</option>
                                            <option value='Lagos' {{ old('state') == 'Lagos' ? 'selected' :'' }}>Lagos</option>
                                            <option value='Nasarawa' {{ old('state') == 'Nasarawa' ? 'selected' :'' }}>Nasarawa</option>
                                            <option value='Niger' {{ old('state') == 'Niger' ? 'selected' :'' }}>Niger</option>
                                            <option value='Ogun' {{ old('state') == 'Ogun' ? 'selected' :'' }}>Ogun</option>
                                            <option value='Ondo' {{ old('state') == 'Ondo' ? 'selected' :'' }}>Ondo</option>
                                            <option value='Osun' {{ old('state') == 'Osun' ? 'selected' :'' }}>Osun</option>
                                            <option value='Oyo' {{ old('state') == 'Oyo' ? 'selected' :'' }}>Oyo</option>
                                            <option value='Plateau {{ old('state') == 'Plateau' ? 'selected' :'' }}'>Plateau</option>
                                            <option value='Rivers' {{ old('state') == 'Rivers' ? 'selected' :'' }}>Rivers</option>
                                            <option value='Sokoto' {{ old('state') == 'Sokoto' ? 'selected' :'' }}>Sokoto</option>
                                            <option value='Taraba' {{ old('state') == 'Taraba' ? 'selected' :'' }}>Taraba</option>
                                            <option value='Yobe' {{ old('state') == 'Yobe' ? 'selected' :'' }}>Yobe</option>
                                            <option value='Zamfara' {{ old('state') == 'Zamfara' ? 'selected' :'' }}>Zamafara</option>
                                        </select>

                                        @if ($errors->has('state'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('state') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
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
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="mode">Mode *</label>
                                        <select name="mode" id="mode" class="form-control" required>
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
                                </div>
                                <div class="col-md-2">
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
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <h4 style="text-align: center; color: blue">Query Details</h4>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Type*</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Complain">Complain</option>
                                            <option value="Enquiry">Enquiry</option>
                                            <option value="Request">Request</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="class">Issues*</label>
                                        <select name="issues" id="issues" id="class" class="form-control">
                                            <option value="" selected="selected">- Select -</option>
                                                                                
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('type')}}</small></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Priority*</label>
                                        <select name="priority" id="class" class="form-control" required>
                                            <option value="" selected="selected">- Select -</option>
                                            <option value="Low">High</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">Low</option>
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('priority')}}</small></div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="class">Status*</label>
                                        <select name="status" id="status" class="form-control" required>
                                           
                                        </select>
                                        <div><small style="color:red">{{ $errors->first('status')}}</small></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                </div>
                            </div>

                            <div class="row">            
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('complain') ? ' has-error' : '' }}">
                                        <label for="complain" style="color:red">Query Content*</label>
                                        <textarea id="ckeditor" type="text" class="form-control" name="complain"
                                            value="{{ old('complain') }}" rows="8" autofocus required></textarea>
                                        @if ($errors->has('complain'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('complain') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('response') ? ' has-error' : '' }}">
                                        <label for="response" style="color:green">Your Response*</label>
                                        <textarea required id="summary-ckeditor" type="text" class="form-control" name="response"
                                            value="{{ old('response') }}" rows="8" autofocus></textarea>
                                        @if ($errors->has('response'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('response') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div> 
                            </div>
                           
                            {{-- @if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) )
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                                        <label for="notes" style="color:green">Supervisor's Note</label>
                                        <textarea class="form-control" name="notes"
                                            value="{{ old('notes') }}" rows="8" autofocus></textarea>
                                        @if ($errors->has('notes'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('notes') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                               
                            </div>
                            @endif --}}
                            <div class="row">
                                <button type="submit" class="btn btn-primary" style="width:100%">
                                    Submit
                                </button>
                            </div>
                        </form>
                       
                    </div>
                </div>
            </div>
        </div>
        @section('extra-scripts')
        <script>
            $('#type').on('change', function(){
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
                    @if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())))
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
                    $('#issues').append('<option value="Exchange (Size or colour)">Exchange (Size or colour)</option>');
                    
                    $("#status").html("");
                    $('#status').append('<option value="Pending" selected>Pending</option>');
                    $('#status').append('<option value="In Progress">In Progress</option>');
                    @if(!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())))
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
    </div>

    @endsection
    @endsection