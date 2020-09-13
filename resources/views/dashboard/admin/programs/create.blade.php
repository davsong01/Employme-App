@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Program</h4>
                    </div>
                    <form action="{{route('programs.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Training Title *</label>
                            <input type="text" name="p_name" value="{{ old('p_name')}}" class="form-control" required>
                        </div>
                        <!--Gives the first error for input name-->
                        <div><small style="color:red">{{ $errors->first('p_name')}}</small></div>
                        <div class="form-group">
                            <label>Training Hashtag *</label>
                            <input type="text" name="p_abbr" value="{{ old('p_abbr')}}" class="form-control" required>
                        </div>
                        <!--Gives the first error for input name-->
                        <div><small style="color:red">{{ $errors->first('p_abbr')}}</small></div>

                        <div class="form-group">
                            <label>Training Fee *</label>
                            <input type="number" name="p_amount" value="{{ old('p_amount') }}" min="0"
                                class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Early Bird Fee *</label>
                            <input type="number" name="e_amount" value="{{ old('e_amount') }}" min="0"
                                class="form-control" required>
                        </div>
                        <!--Gives the first error for input name-->
                        <div><small style="color:red">{{ $errors->first('e_amount')}}</small></div>

                        <div class="form-group">
                            <label>Start Date *</label>
                            <input type="date" name="p_start" value="{{ old('p_start') }}" class="form-control"
                                required>
                        </div>
                        <!--Gives the first error for input name-->
                        <div><small style="color:red">{{ $errors->first('p_start') }}</small></div>
                        <div class="form-group">
                            <label>End Date *</label>
                            <input type="date" name="p_end" value="{{ old('p_end') }}" class="form-control" required>
                        </div>
                        <!--Gives the first error for input name-->
                        <small style="color:red">{{ $errors->first('p_end')}}</small>
                        <div class="form-group">
                        <label>Does Training have pre class tests?</label>
                        <select name="hasmock" class="form-control" id="hasmock" required>
                            <option value="">--Select Option --</option>
                            <option value="1" {{ old('hasmock') == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('hasmock') == 0 ? 'selected' : '' }}>No</option>
                        </select>
                        </div>
                        <small style="color:red">{{ $errors->first('p_end')}}</small>
                        <div class="form-group">
                            <label>Upload Booking form</label>
                            <input type="file" name="booking_form" value="{{ old('booking_form') }}" class="form-control">
                        </div>
                        <div><small style="color:red">{{ $errors->first('booking_form')}}</small></div>
                        <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">
               
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection