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
                        @if(request()->prefix__ == 'admin')
                        <form action="{{ route('complains.store') }}" method="POST">
                        @else
                        <form action="{{ route('participant.complains.store') }}" method="POST">
                        @endif
                            @csrf
                            <!-- Section 1: Customer Personal Details -->
                            <fieldset class="border p-3 mb-4">
                                <legend class="text-primary fw-bold px-2">Customer Personal Details</legend>
                                <div class="row g-3">
                                    <!-- Select Training -->
                                    <input type="hidden" name="program_id" value="{{ $training->id }}">

                                    <!-- Customer Name -->
                                    <div class="col-md-4">
                                        <label for="name" class="form-label">Customer Name*</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" autofocus required>
                                        @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-4">
                                        <label for="email" class="form-label">E-Mail Address*</label>
                                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                        @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-4">
                                        <label for="phone" class="form-label">Phone*</label>
                                        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                        @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <!-- Gender -->
                                    <div class="col-md-3">
                                        <label for="gender" class="form-label">Gender*</label>
                                        <select name="gender" id="gender" class="form-control select2" required>
                                            <option value="" selected>Choose</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        @error('gender')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="col-md-6">
                                        <label for="address" class="form-label">Customer Address*</label>
                                        <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" required>
                                        @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-3">
                                        <label for="state" class="form-label">State*</label>
                                        <select name="state" id="state" class="form-control select2" required>
                                            <option value="" selected>- Select -</option>
                                            @foreach(['Abia', 'Adamawa', 'AkwaIbom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'] as $state)
                                            <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                            @endforeach
                                        </select>
                                        @error('state')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="other">Other Details</label>
                                        <input id="other" type="text" class="form-control" name="other"
                                            value="{{ old('other')}}" autofocus>
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Section 2: Query Details -->
                            <fieldset class="border p-3 mb-4">
                                <legend class="text-success fw-bold px-2">Query Details</legend>
                                <div class="row g-3">
                                    <!-- Type -->
                                    <div class="col-md-3">
                                        <label for="type" class="form-label">Type*</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="" selected>- Select -</option>
                                            <option value="Complain">Complain</option>
                                            <option value="Enquiry">Enquiry</option>
                                            <option value="Request">Request</option>
                                        </select>
                                        @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Issues -->
                                    <div class="col-md-3">
                                        <label for="issues" class="form-label">Issues*</label>
                                        <select name="issues" id="issues" class="form-control" required>
                                            <option value="" selected>- Select -</option>
                                        </select>
                                        @error('issues')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Priority -->
                                    <div class="col-md-3">
                                        <label for="priority" class="form-label">Priority*</label>
                                        <select name="priority" id="priority" class="form-control" required>
                                            <option value="" selected>- Select -</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                        @error('priority')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-3">
                                        <label for="status" class="form-label">Status*</label>
                                        <select name="status" id="status" class="form-control" required>
                                        </select>
                                        @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
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
                            </fieldset>

                            <!-- Section 3: Response -->
                            <fieldset class="border p-3 mb-4">
                                <legend class="text-danger fw-bold px-2">Response Details</legend>
                                <div class="row g-3">
                                    <!-- Query Content -->
                                    <div class="col-md-6">
                                        <label for="complain" class="form-label">Query Content*</label>
                                        <textarea id="ckeditor" name="complain" class="form-control" rows="8" required>{{ old('complain') }}</textarea>
                                        @error('complain')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Response -->
                                    <div class="col-md-6">
                                        <label for="response" class="form-label">Your Response*</label>
                                        <textarea id="summary-ckeditor" name="response" class="form-control" rows="8" required>{{ old('response') }}</textarea>
                                        @error('response')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-scripts')
<script>
    $(document).ready(function () {
        $('#type').on('change', function () {
            
            $('#issues').html('');
            $('#status').html(''); 

            const type = $('#type').val();

            if (type === 'Complain') {
                $('#issues').append('<option value="Drop Balance">Drop Balance</option>');
                $('#issues').append('<option value="Network Issues">Network Issues</option>');
                $('#issues').append('<option value="Recharge Issues">Recharge Issues</option>');
                $('#issues').append('<option value="Data Issues">Data Issues</option>');
                $('#issues').append('<option value="Late delivery">Late delivery</option>');
                $('#issues').append('<option value="Damages">Damages</option>');

                $('#status').append('<option value="Pending">Pending</option>');
                $('#status').append('<option value="In Progress">In Progress</option>');
                @if(checkRoleHas(['Admin', 'Facilitator']))
                $('#status').append('<option value="Resolved">Resolved</option>');
                @endif
            } else if (type === 'Enquiry') {
                $('#issues').append('<option value="Product Enquires">Product Enquires</option>');
                $('#issues').append('<option value="Recharge Enquires">Recharge Enquires</option>');
                $('#issues').append('<option value="Opening hours">Opening hours</option>');
                $('#issues').append('<option value="Office location">Office location</option>');
                $('#issues').append('<option value="Cost of product">Cost of product</option>');

                $('#status').append('<option value="Resolved">Resolved</option>');
            } else if (type === 'Request') {
                $('#issues').append('<option value="Product Request">Product Request</option>');
                $('#issues').append('<option value="Recharge Request">Recharge Request</option>');
                $('#issues').append('<option value="Home delivery">Home delivery</option>');
                $('#issues').append('<option value="Exchange (Size or colour)">Exchange (Size or colour)</option>');

                $('#status').append('<option value="Pending" selected>Pending</option>');
                $('#status').append('<option value="In Progress">In Progress</option>');
                @if(checkRoleHas(['Admin', 'Facilitator']))
                $('#status').append('<option value="Resolved">Resolved</option>');
                @endif
            }
        });

        CKEDITOR.replace('summary-ckeditor');
        CKEDITOR.replace('ckeditor');
    });
</script>
@endsection
