@extends('dashboard.admin.index')
@section('title', 'Add Assessment score')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div>
                            @include('layouts.partials.alerts')
                            <p style="color:red"><strong>Here you can set score parameters for individual training assesment.</strong> <br><br>
                            NOTE: You cannot edit assessment parameters for a program whose Modules have been enabled. <br>Total Scores for all assessment parameter for an individual training cannot be more or less than 100%</p>
                            <h5>Add Assessment Parameter</h5> 
                        </div>
                    </div>

                    <form action="{{ route('scoreSettings.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure?');" class="pb-2">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <!--Gives the first error for input name-->
                                <div class="form-group">
                                    <label for="class">Select Training</label>
                                    <select name="program" id="program" class="form-control" required>
                                        <option value="">Select an option</option>
                                      
                                        @foreach ($programs as $program)
                                        
                                            @if($program->counter == 0 && $program->settings_count == 0)
                                                <option value="{{ $program->id }}">{{$program->p_name}}</option>
                                            @endif
                
                                        @endforeach
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
                                </div>

                                <div class="form-group">
                                    <label>Set Maximum score for Class Tests<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="classtests" value="{{ old('classtests')}}"
                                        class=" form-control" min="0" max="100" required>
                                    <div><small style="color:red">{{ $errors->first('classtests')}}</small></div>
                                </div>
                                <div class="form-group">
                                    <label>Set Maximum score for Role Play<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="rolepalyscore" value="{{ old('rolepalyscore')}}"
                                        class="form-control" min="0" max="100">
                                </div>
                                <div><small style="color:red">{{ $errors->first('rolepalyscore')}}</small></div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Set Maximum score for Email<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="emailscore" value="{{ old('emailscore')}}"
                                        class="form-control" min="0" max="100" required>
                                </div>
                                <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>

                                <div class="form-group">
                                    <label>Set Maximum score for Certification<span style="color:green">(Max score = 100)</span></label>
                                    <input type="number" name="certificationscore" class="form-control" min="0"
                                        max="100" required value="{{ old('certificationscore')}}">
                                </div>
                                <div><small style="color:red">{{ $errors->first('certificationscore')}}</small></div>

                                <div class="form-group">
                                    <label style="color:red">Set Pass Mark <span style="color:green">(Max score = 100%)</span></label>
                                    <input type="number" name="passmark" value="{{ old('passmark')}}"
                                        class="form-control" min="0" max="100" required>
                                </div>
                                <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                            </div>
                        </div>
                        <div class="row">

                            <input type="submit" name="submit" value="Submit" class="btn btn-primary"
                                style="width:100%">
                        </div>

                </div>
            </div>
        </div>
    </div>
    @endsection 

