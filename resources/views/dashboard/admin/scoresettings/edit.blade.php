@extends('dashboard.admin.index')
@section('title', 'Update Assessment settings')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div>
                            @include('layouts.partials.alerts')
                            <h3>Add Assessment Parameter</h3>
                            <h5>Update score parameters for: {{ $scoreSetting->program->p_name }}</h5>
                                    <p style="color:red"><br>Total Scores for all assessment parameter for an individual training cannot be
                                    more than 100%</p>
                        </div>
                    </div>

                    <form action="{{ route('scoreSettings.update', $scoreSetting->id) }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                       
                            <input type="hidden" name="program" value="{{ old('program') ?? $scoreSetting->program->id }}" placeholder="{{$scoreSetting->program->id }}" class=" form-control"
                                min="0" max="100" required readonly>
                            

                        <div class="form-group">
                            <label>Set Maximum score for Class Tests<span style="color:green">(Max score =
                                    100)</span></label>
                            <input type="number" name="classtests" value="{{ old('classtests') ?? $scoreSetting->class_test }}" class=" form-control"
                                min="0" max="100" required>
                            <div><small style="color:red">{{ $errors->first('classtests')}}</small></div>
                        </div>
                        <div class="form-group">
                            <label>Set Maximum score for Role Play<span style="color:green">(Max score =
                                    100)</span></label>
                            <input type="number" name="rolepalyscore" value="{{ old('rolepalyscore') ?? $scoreSetting->role_play}}"
                                class="form-control" min="0" max="100">
                        </div>
                        <div><small style="color:red">{{ $errors->first('rolepalyscore')}}</small></div>
                        
                    <div class="form-group">
                        <label>Set Maximum score for Email<span style="color:green">(Max score = 100)</span></label>
                        <input type="number" name="emailscore" value="{{ old('emailscore') ?? $scoreSetting->email }}" class="form-control"
                            min="0" max="100" required>
                    </div>
                    <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>

                    <div class="form-group">
                        <label>Set Maximum score for Certification<span style="color:green">(Max score =
                                100)</span></label>
                        <input type="number" value="{{ old('certificationscore') ?? $scoreSetting->certification }}" name="certificationscore" class="form-control" min="0" max="100" required>
                    </div>
                    <div><small style="color:red">{{ $errors->first('certificationscore')}}</small></div>

                    <div class="form-group">
                        <label>Set Pass Mark <span style="color:green">(Max score = 100)</span></label>
                        <input type="number" name="passmark" value="{{ old('passmark') ?? $scoreSetting->passmark }}" class="form-control" min="0"
                            max="100" required>
                    </div>
                    <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
                </div>
            </div>
            <div class="row">

                <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">
            </div>

        </div>
    </div>
</div>
</div>
@endsection