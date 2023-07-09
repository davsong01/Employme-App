@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Program</h4>
                    </div> 
                    <form action="{{route('programs.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Training Title *</label>
                                    <input type="text" name="p_name" value="{{ old('p_name')}}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Training Hashtag *</label>
                                    <input type="text" name="p_abbr" value="{{ old('p_abbr')}}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Training Fee *</label>
                                    <input type="number" name="p_amount" value="{{ old('p_amount') }}" min="0"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Off Season Program?</label>
                                    <select name="off_season" class="form-control" id="off_season" required>
                                        <option value="1" {{ old('off_season') == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('off_season') == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                    <small style="color:red">{{ $errors->first('off_season')}}</small>
                                </div>
                                <div class="form-group">
                                    <label>Start Date *</label>
                                    <input type="date" name="p_start" value="{{ old('p_start') }}" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>End Date *</label>
                                    <input type="date" name="p_end" value="" class="form-control" required="">
                                </div>
                                <div class="form-group">
                                    <label>Show Catalogue Popup</label>
                                    <select name="show_catalogue_popup" class="form-control" id="show_catalogue_popup" required>
                                        <option value="yes" {{ old('show_catalogue_popup') == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ old('show_catalogue_popup') == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Does Program have pre class tests?</label>
                                    <select name="hasmock" class="form-control" id="hasmock" required>
                                        <option value="">--Select Option --</option>
                                        <option value="1" {{ old('hasmock') == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('hasmock') == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Enable Part Payment?</label>
                                    <select name="haspartpayment" class="form-control" id="hasmock" required>
                                        <option value="0" {{ old('haspartpayment') == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ old('haspartpayment') == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Enable Part Payment Restrictions?</label>
                                    <select name="allow_payment_restrictions" class="form-control" id="hasmock" required>
                                        <option value="yes" {{ old('allow_payment_restrictions') == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ old('allow_payment_restrictions') == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Early Bird Fee *</label>
                                    <input type="number" name="e_amount" value="{{ old('e_amount') ?? 0}}" min="0"
                                        class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" id="hasmock" required>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Published</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Draft</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Upload Program Banner</label>
                                    <input type="file" name="image" value="{{ old('image') }}" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Upload Booking form</label>
                                    <input type="file" name="booking_form" value="{{ old('booking_form') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                         <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Show Mode (Two payment modes only)</label>
                                     <select name="show_modes" class="form-control" id="show_modes" required>
                                        <option value="no" {{ old('show_modes') == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ old('show_modes') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_mode" style="display:none">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-mode"><i class="fa fa-plus"></i> Add Mode</button>
                                </div>
                            </div>
                        </section>
                        <section id="mode-holder" style="background: antiquewhite; padding: 0px 10px;margin-bottom:20px">
                            <div class="row" id="mode-0">
                            </div>
                        </section>
                        <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px;">
                                    <label>Show Location</label>
                                     <select name="show_locations" class="form-control" id="show_locations" required>
                                        <option value="no" {{ old('show_locations') == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ old('show_locations') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_location" style="display:none">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-course"><i class="fa fa-plus"></i> Add Location</button>
                                </div>
                            </div>
                        </section>
                        <section id="course-holder" style="background: #80c4ff;padding: 0 10px;">
                            <div class="row" id="course-0">
                            </div>
                        </section>

                      
                    <div class="col-md-12">
                        <button name="submit" class="btn btn-primary" style="width:100%">Submit</button>
                    </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#show_locations").on('change', function () {
        if($("#show_locations").val() == 'yes'){
            $("#add_location").show();
        }else{
            $("#add_location").hide();
        }
    });
    
    $("#show_modes").on('change', function () {
        if($("#show_modes").val() == 'yes'){
            $("#add_mode").show();
        }else{
            $("#add_mode").hide();
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

    $("#add-mode").on('click', function () {
        //get last ID
        var lastChild = $("#mode-holder").children().last();
        var countChildren = $("#mode-holder").children().length;
        
        if(countChildren > 2){
            return alert('You can only add 2 payment modes!');
        }

        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
       
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

</script>
@endsection