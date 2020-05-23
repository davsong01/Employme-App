<div class="row">
    <div class="col-md-6">

        <!--Gives the first error for input name-->
        <div class="form-group">
            <label>Training</label>
            <input type="text" name="" value="{{ $results->program->p_name }}" class=" form-control" disabled>
        </div>
        <div class="form-group">
            <label>Total Score</label>
            <input type="number" name="passmark" value="{{$results->total }}" class="form-control" min="0" max="100"
                required disabled>
        </div>
        <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
        <div class="form-group">
            <label>Pass Mark Set</label>
            <input type="number" name="passmark" value="{{ old('passmark') ?? $results->passmark }}"
                class="form-control" min="0" max="100" required disabled>
        </div>
        <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
    </div>

    <div class="col-md-6">
            <div class="form-group">
                    <label>Workbook Score*<span style="color:green">(Max score = 35)</span></label>
                    <input type="number" name="workbookscore" value="{{ old('workbookscore') ?? $results->workbookscore }}"
                        class=" form-control" min="0" max="35" required>
                    <div><small style="color:red">{{ $errors->first('workbookscore')}}</small></div>
                </div>
        <div class="form-group">
            <label>Email Score* <span style="color:green">(Max score = 20)</span></label>
            <input type="number" name="emailscore" value="{{ old('emailscore') ?? $results->emailscore }}"
                class="form-control" min="0" max="20">
        </div>
        <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>
        <div class="form-group">
            <label>Role Play Score* <span style="color:green">(Max score = 25)</span></label>
            <input type="number" name="roleplayscore" value="{{ old('roleplayscore') ?? $results->roleplayscore }}"
                class="form-control" min="0" max="25" required>
        </div>
        <div><small style="color:red">{{ $errors->first('roleplayscore')}}</small></div>

        <div class="form-group">
            <label>Certification Score*<span style="color:green">(Max score = 20)</span></label>
            <input type="number" name="certificationscore"
                value="{{ old('certificationscore') ?? $results->certificationscore }}" class="form-control" min="0"
                max="20" required>
        </div>
        <div><small style="color:red">{{ $errors->first('certificationscore')}}</small></div>


    </div>
</div>
<div class="row">

    <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">
</div>


{{ csrf_field() }}