@extends('dashboard.admin.index')
@section('title', 'Settings' )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Update App Settings</h4>
                    </div>
                   <form action="{{route('settings.update', 1)}}" method="POST"
                        enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                         {{ csrf_field() }}
                        <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="form-group">
                            <label>Official Email *</label>
                            <input type="text" name="OFFICIAL_EMAIL" value="{{ old('OFFICIAL_EMAIL') ?? $setting->OFFICIAL_EMAIL}}" class="form-control" required>
                        </div>

                        <!--Gives the first error for input name-->
                        <div><small style="color:red">{{ $errors->first('OFFICIAL_EMAIL')}}</small></div>

                        <div class="form-group">
                            <label>Address on receipt (You can use html tags)*</label>
                            <textarea name="ADDRESS_ON_RECEIPT" value="{{ old('ADDRESS_ON_RECEIPT') ??  $setting->ADDRESS_ON_RECEIPT}}"
                                class="form-control" required>{{ old('ADDRESS_ON_RECEIPT') ?? $setting->ADDRESS_ON_RECEIPT}}</textarea>
                        </div>
                        <div><small style="color:red">{{ $errors->first('ADDRESS_ON_RECEIPT')}}</small></div>

                        <div class="form-group">
                            <label>Currency Abbreviation *</label>
                            <input type="text" name="CURR_ABBREVIATION" value="{{ old('CURR_ABBREVIATION') ??  $setting->CURR_ABBREVIATION}}"value="{{ old('CURR_ABBREVIATION') ?? $setting->CURR_ABBREVIATION}}"
                                class="form-control" required>
                        </div>
                        <div><small style="color:red">{{ $errors->first('CURR_ABBREVIATION')}}</small></div>
                        <div class="form-group">
                            <label>Default Currency code(Change this to dropdown of naira ghana cedes and dollar) *</label>
                            <input type="text" name="DEFAULT_CURRENCY" value="{!! old('DEFAULT_CURRENCY') ??  $setting->DEFAULT_CURRENCY !!}"value="{!! old('DEFAULT_CURRENCY') ?? $setting->DEFAULT_CURRENCY !!}"
                                class="form-control" required>
                        </div>
                        <div><small style="color:red">{{ $errors->first('DEFAULT_CURRENCY')}}</small></div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="primary_color">Primary colour</label>
                                <input type="text" class="form-control" id="primary_color" minlength="7" maxlength="7" name="primary_color" placeholder="Eg #000000" title="accepts only color code" pattern="#[\da-f]{6}" value="{{ old('primary_color') ?? $setting->primary_color }}" required>
                                <div><small style="color:red">{{ $errors->first('primary_color')}}</small></div>
                            </div>
                            <div class="form-group col-md-6">
                                 <label for="secondary_color">Secondary colour</label>
                                <input type="text" class="form-control" id="secondary_color" minlength="7" maxlength="7" name="secondary_color" placeholder="Eg #000000" title="accepts only color code" pattern="#[\da-f]{6}" value="{{ old('secondary_color') ?? $setting->secondary_color }}" required>
                                <div><small style="color:red">{{ $errors->first('secondary_color')}}</small></div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12">
                            <div class="form-group">
                                <label>Upload logo</label>
                                <input type="file" name="logo"  class="form-control">
                            </div>
                            <div><small style="color:red">{{ $errors->first('logo')}}</small></div>

                        </div>
                        <div class="col-12">
                             <input type="submit" class="btn btn-primary" style="width:100%">
                        </div>

                    </form>
            </div>
        </div>
    </div>
</div>
@endsection