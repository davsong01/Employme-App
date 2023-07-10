@extends('dashboard.admin.index')
@section('title', $program->p_name )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Update {{ $program->p_name }}</h4>
                    </div> 
                   <form action="{{route('programs.update', $program->id)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                         <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label>Training Title *</label>
                                    <input type="text" name="p_name" value="{{ old('p_name') ?? $program->p_name}}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Training Hashtag *</label>
                                    <input type="text" name="p_abbr" value="{{ old('p_abbr') ?? $program->p_abbr }}" class="form-control" required>
                                </div>
                                <!--Gives the first error for input name-->

                                <div class="form-group">
                                    <label>Training Fee *</label>
                                    <input type="number" name="p_amount" value="{{ old('p_amount') ??  $program->p_amount}}" min="0"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Early Bird Fee *</label>
                                    <input type="number" name="e_amount" value="{{ old('e_amount') ??  $program->e_amount}}" min="0"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Start Date *</label>
                                    <input type="date" name="p_start" value="{{ old('p_start') ?? $program->p_start }}" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>End Date *</label>
                                    <input type="date" name="p_end" value="{{ old('p_end') ??  $program->p_end }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Off Season Program?</label>
                                    <select name="off_season" class="form-control" id="off_season" required>
                                        <option value="1" {{ $program->off_season == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $program->off_season == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                           
                                <div class="form-group">
                                    <label>Show Catalogue Popup</label>
                                    <select name="show_catalogue_popup" class="form-control" id="show_catalogue_popup" required>
                                        <option value="yes" {{ $program->show_catalogue_popup == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->show_catalogue_popup == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Replace Program Banner</label> <br>
                                    <img src="{{ url('/').'/'.$program->image }}" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                    <input type="file" name="image" value="{{ old('image') ??  $program->image }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    @if($program->booking_form)
                                    <label>Replace Booking form</label>
                                    <i data-toggle="tooltip" title="{{$program->booking_form }}" class="fa fa-paperclip" style="width: 70px;padding-bottom: 10px;"></i>
                                    @else
                                    <label>Upload Booking form</label>
                                    @endif
                                    <input type="file" name="booking_form" value="{{ old('booking_form') }}" placeholder="{{ $program->booking_form }}" class="form-control">
                                </div>
                                <div><small style="color:red">{{ $errors->first('booking_form')}}</small></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                <label>Does Program have pre class tests?</label>
                                <select name="hasmock" class="form-control" id="hasmock" required>
                                    <option value="1" {{ $program->hasmock == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $program->hasmock == 0 ? 'selected' : '' }}>No</option>
                                </select>
                                <small style="color:red">{{ $errors->first('p_end')}}</small>
                            </div>
                            
                            <div class="form-group">
                                <label>Enable Part Payment?</label>
                                <select name="haspartpayment" class="form-control" id="hasmock" required>
                                    <option value="1" {{ $program->haspartpayment == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $program->haspartpayment == 0 ? 'selected' : '' }}>No</option>
                                </select>
                                <small style="color:red">{{ $errors->first('haspartpayment')}}</small>
                            </div>
                            <div class="form-group">
                                    <label>Enable Part Payment Restrictions for materials?</label>
                                    <select name="allow_payment_restrictions_for_materials" class="form-control" id="allow_payment_restrictions_for_materials" required>
                                        <option value="yes" {{ $program->allow_payment_restrictions_for_materials == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->allow_payment_restrictions_for_materials == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Enable Part Payment Restrictions for Pre class tests?</label>
                                    <select name="allow_payment_restrictions_for_pre_class_tests" class="form-control" id="allow_payment_restrictions_for_pre_class_tests" required>
                                        <option value="yes" {{ $program->allow_payment_restrictions_for_pre_class_tests == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->allow_payment_restrictions_for_pre_class_tests == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Enable Part Payment Restrictions for Post class tests?</label>
                                    <select name="allow_payment_restrictions_for_post_class_tests" class="form-control" id="allow_payment_restrictions_for_post_class_tests" required>
                                        <option value="yes" {{ $program->allow_payment_restrictions_for_post_class_tests == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->allow_payment_restrictions_for_post_class_tests == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            
                                <div class="form-group">
                                    <label>Enable Part Payment Restrictions for Completed Tests?</label>
                                    <select name="allow_payment_restrictions_for_completed_tests" class="form-control" id="allow_payment_restrictions_for_completed_tests" required>
                                        <option value="yes" {{ $program->allow_payment_restrictions_for_completed_tests == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->allow_payment_restrictions_for_completed_tests == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Enable Part Payment Restrictions for Results?</label>
                                    <select name="allow_payment_restrictions_for_results" class="form-control" id="allow_payment_restrictions_for_results" required>
                                        <option value="yes" {{ $program->allow_payment_restrictions_for_results == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->allow_payment_restrictions_for_results == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Enable Part Payment Restrictions for Certificates?</label>
                                    <select name="allow_payment_restrictions_for_certificates" class="form-control" id="allow_payment_restrictions_for_certificates" required>
                                        <option value="yes" {{ $program->allow_payment_restrictions_for_certificates == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ $program->allow_payment_restrictions_for_certificates == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                           
                            
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" id="status" required>
                                    <option value="1" {{ $program->status == 1 ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ $program->status == 0 ? 'selected' : '' }}>Draft</option>
                                </select>
                                <small style="color:red">{{ $errors->first('status')}}</small>
                            </div>
                            </div>
                        </div>
                         <section>
                            <div class="row" style="margin-top: 20px;">                                   
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Sub Trainings?</label>
                                     <select name="show_sub" class="form-control" id="show_sub" required>
                                         <option value="yes" {{ (isset($program->subPrograms) && !empty($program->subPrograms)) ? 'selected' : '' }}>Yes</option>
                                         <option value="no" {{ $program->show_sub == 'no' ? '' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_sub" style="display:{{ (isset($program->subPrograms) && !empty($program->subPrograms)) ? 'block':'none' }}">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-sub"><i class="fa fa-plus"></i> Add Sub Training</button>
                                </div>
                            </div>
                        </section>
                        <section id="sub-holder" style="background: antiquewhite; padding-top:15px !important; padding: 0px 10px;margin-bottom:20px">
                            <div class="row" id="sub-0">
                            </div>
                            <?php $sub_counter = 1?>
                            @if(isset($program->subPrograms) && !empty($program->subPrograms))
                            @foreach($program->subPrograms as $sub)
                            <?php $counter = $sub_counter++ ?>
                                    <input type="hidden" name="sub_program_id[]" value="{{ $sub->id }}">
                                    <div class="row" id="oldsub-{{ $counter }}">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="sub_name">Sub Program Name</label>
                                                <input type="text" class="form-control" value="{{ $sub->p_name }}"
                                                    name="sub_name[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="sub_amount">Sub Program Amount</label>
                                                <input type="text" class="form-control" id="amount" value="{{ $sub->p_amount }}" name="sub_amount[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mode_name">Status</label>
                                                <select name="sub_status[]" class="form-control" id="sub_status" required>
                                                    <option value="1" {{  $sub->status == 1 ? 'selected' : '' }}>Published</option>
                                                    <option value="0" {{  $sub->status == 0 ? 'selected' : '' }}>Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                                                <button class="btn btn-danger removeold-sub" data-program-id="{{ $sub->id }}" id="removeold-sub-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </section>
                        <section>
                            <div class="row" style="margin-top: 20px;">                                   
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Show Mode (2 payment modes only)</label>
                                     <select name="show_modes" class="form-control" id="show_modes" required>
                                        <option value="no" {{ $program->show_modes == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ $program->show_modes == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_mode" style="display:{{ $program->show_modes == 'yes' ? 'block':'none' }}">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-mode"><i class="fa fa-plus"></i> Add Mode</button>
                                </div>
                            </div>
                        </section>
                        <section id="mode-holder" style="background: antiquewhite; padding: 0px 10px;margin-bottom:20px">
                            <div class="row" id="mode-0">
                            </div>
                            <?php $mode_counter = 1?>
                            @if($program->show_modes == 'yes' && isset($program->modes) && !empty($program->modes))
                                @foreach(json_decode($program->modes, true) as $key=>$value)
                                    <?php $counter = $mode_counter++ ?>
                                    <div class="row" id="oldmode-{{ $counter }}">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="mode_name">Mode Name</label>
                                               
                                                <input type="select" class="form-control" value="{{ $key }}"
                                                    name="mode_name[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="mode_amount">Mode Amount</label>
                                                <input type="text" class="form-control" id="unit" value="{{ $value }}"name="mode_amount[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                                                <button class="btn btn-danger removeold-mode" id="removeold-mode-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </section>
                        
                         <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px;">
                                    <label>Show Location</label>
                                    <select name="show_locations" class="form-control" id="show_locations" required>
                                        <option value="no" {{ $program->show_locations == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ $program->show_locations == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_location" style="display:{{ $program->show_locations == 'yes' ? 'block':'none' }}">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-course"><i class="fa fa-plus"></i> Add Location</button>
                                </div>
                            </div>
                        </section>
                        <section id="course-holder" style="background: #80c4ff;padding: 0 10px; margin-bottom: 10px;">
                            <div class="row" id="course-0">
                            </div>
                            <?php $location_counter = 1 ?>
                            @if($program->show_locations == 'yes' && isset($program->locations) && !empty($program->locations))
                                @foreach(json_decode($program->locations, true) as $key=>$value)
                                <?php $counter = $location_counter++ ?>
                                <div class="row" id="oldcourse-{{ $counter }}">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="location_name">Location Name</label>
                                            <input type="text" class="form-control" value="{{ $key }}"
                                                name="location_name[]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="location_address">Location Address</label>
                                            <input type="text" class="form-control" id="unit" value="{{ $value }}"name="location_address[]" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="mark" style="color:#80c4ff">sdsdsddsdssd</label>
                                            <button class="btn btn-danger remove-old-course" id="oldcourse-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </section>

                       
                        <div class="col-12">
                            <input type="submit" name="submit" value="Update" class="btn btn-primary" style="width:100%">
                        </div>
                        
                    </form>
            </div>
        </div>
    </div>
</div>
<script>
    $("#show_locations").on('change', function () {
        if($("#show_locations").val() == 'yes'){
            $("#add_location").show();
            $("#course-holder").show();
            
        }else{
            $("#add_location").hide();
            $("#course-holder").hide();

        }
    });
    
    $("#show_modes").on('change', function () {
        if($("#show_modes").val() == 'yes'){
            $("#add_mode").show();
            $("#mode-holder").show();
            
        }else{
            $("#add_mode").hide();
            $("#mode-holder").hide();
        }
    });

    $("#show_sub").on('change', function () {
    if($("#show_sub").val() == 'yes'){
        $("#add_sub").show();
        $("#sub-holder").show();
        
    }else{
        $("#add_sub").hide();
        $("#sub-holder").hide();
    }
    });

    $("#add-course").on('click', function () {
        //get last ID
        var lastChild = $("#course-holder").children().last();
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        var child = `<div class="row" id="course-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_name">Location Name</label>
                        <input type="text" class="form-control" value="{{ old('location_name') }}"
                            name="location_name[]" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_address">Location Address</label>
                        <input type="text" class="form-control" id="unit" value="{{ old('location_address')}}"name="location_address[]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:#80c4ff">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-course" id="remove-course-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#course-holder").append(child);      
        });

    $("#course-holder").on('click','.remove-course', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#course-"+id).remove();
    });

    $("#course-holder").on('click','.remove-old-course', function(e) {
        var removeId = $(e.target).attr('id');
        $("#"+removeId).remove();
    });

    $("#mode-holder").on('click','.removeold-mode', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#oldmode-"+id).remove();
    });

    $("#sub-holder").on('click','.removeold-sub', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var program_id =  $(e.target).attr('data-program-id');
        var id = removeId[2];
        // validate and remove program from database
        $.get("/admin-remove-sub-program/"+program_id,function(data, status){
            if(data.status == 'success'){
                $("#oldsub-"+id).remove();
                alert(data.message);
            }else{
                alert(data.message);
            }
        });
    });

    $("#add-mode").on('click', function () {
        //get last ID
        var lastChild = $("#mode-holder").children().last();
        var countChildren = $("#mode-holder").children().length;
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        if(countChildren > 2){
            return alert('You can only add 2 payment modes!');
        }

        var child = `<div class="row" id="mode-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="mode_name">Mode Name</label>
                        <select name="mode_name[]" class="form-control" id="mode_name" required>
                            <option value="" selected>Select mode</option>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_address">Mode Amount</label>
                        <input type="text" class="form-control" id="unit" value="{{ old('mode_amount')}}"name="mode_amount[]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-mode" id="remove-mode-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#mode-holder").append(child);      
        });

    $("#mode-holder").on('click','.remove-mode', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#mode-"+id).remove();
    });

    $("#add-sub").on('click', function () {
        //get last ID
        var lastChild = $("#sub-holder").children().last();
        var countChildren = $("#sub-holder").children().length;
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        
        var child = `<div class="row" id="sub-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="sub_name">Sub Program Name</label>
                        <input type="text" class="form-control" id="sub_name" value="{{ old('sub_name')}}"name="sub_name[]" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sub_amount">Sub Program Amount</label>
                        <input type="text" class="form-control" id="sub_amount" value="{{ old('sub_amount')}}"name="sub_amount[]" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mode_name">Status</label>
                        <select name="sub_status[]" class="form-control" id="sub_status" required>
                            <option value="1" selected>Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-sub" id="remove-sub-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#sub-holder").append(child);      
        });

    $("#sub-holder").on('click','.remove-sub', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
       
        $("#sub-"+id).remove();
    });

</script>
@endsection