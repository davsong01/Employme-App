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
                            <div class="col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label>Training Title *</label>
                                <input type="text" name="p_name" value="{{ old('p_name') ?? $program->p_name}}" class="form-control" required>
                            </div>
                            <!--Gives the first error for input name-->
                            <div><small style="color:red">{{ $errors->first('p_name')}}</small></div>
                            <div class="form-group">
                                <label>Training Hashtag *</label>
                                <input type="text" name="p_abbr" value="{{ old('p_abbr') ?? $program->p_abbr }}" class="form-control" required>
                            </div>
                            <!--Gives the first error for input name-->
                            <div><small style="color:red">{{ $errors->first('p_abbr')}}</small></div>

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
                            <!--Gives the first error for input name-->
                            <div><small style="color:red">{{ $errors->first('e_amount')}}</small></div>

                            <div class="form-group">
                                <label>Start Date *</label>
                                <input type="date" name="p_start" value="{{ old('p_start') ?? $program->p_start }}" class="form-control"
                                    required>
                            </div>
                            <div><small style="color:red">{{ $errors->first('p_start') }}</small></div>
                            <div class="form-group">
                                <label>End Date *</label>
                                <input type="date" name="p_end" value="{{ old('p_end') ??  $program->p_end }}" class="form-control" required>
                            </div>
                            <!--Gives the first error for input name-->
                            <small style="color:red">{{ $errors->first('p_end')}}</small>             
                       
                        </div>
                        <div class="col-md-6 col-lg-6 col-sm-12">
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
                                <label>Status</label>
                                <select name="status" class="form-control" id="hasmock" required>
                                    <option value="1" {{ $program->status == 1 ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ $program->status == 0 ? 'selected' : '' }}>Draft</option>
                                </select>
                                <small style="color:red">{{ $errors->first('status')}}</small>
                            </div>

                            <div class="form-group">
                                <label>Change Program Banner</label>
                                <input type="file" name="image" value="{{ old('image') ??  $program->image }}" class="form-control">
                            </div>
                            <div><small style="color:red">{{ $errors->first('image')}}</small></div>
                            <div class="form-group">
                                <label>Change Booking form</label>
                                <input type="file" name="booking_form" value="{{ old('booking_form') }}" placeholder="{{ $program->booking_form }}" class="form-control">
                            </div>
                            <div><small style="color:red">{{ $errors->first('booking_form')}}</small></div>
                             
                        </div>
                        <div class="col-12">
                             <input type="submit" name="submit" value="Update" class="btn btn-primary" style="width:100%">
                        </div>
                        
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection