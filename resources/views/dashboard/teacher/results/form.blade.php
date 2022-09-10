<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="class">Select Student *</label>
            <select name="id" id="id" class="form-control" required>
                <option value=""></option>
                @foreach ($users as $user)
                <option value="{{ $user->id }}">{{$user->name}}</option>
                @endforeach
            </select>
            <div><small style="color:red">{{ $errors->first('student')}}</small></div>
        </div>
        <!--Gives the first error for input name-->
        <div class="form-group">
            <label for="class">Select Training *</label>
            <select name="program_id" id="program_id" class="form-control" required>
                <option value=""></option>
                @foreach ($programs as $program)
                <option value="{{ $program->id }}">{{$program->p_name}}</option>
                @endforeach
            </select>
            <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
        </div>

        <div class="form-group">
            <label>Workbook Score*<span style="color:green">(Max score = 35)</span></label>
            <input type="number" name="workbookscore" value="{{ old('workbookscore')}}" class=" form-control" min="0"
                max="100" required>
            <div><small style="color:red">{{ $errors->first('workbookscore')}}</small></div>
        </div>
        <div class="form-group">
                <label>Email Score* <span style="color:green">(Max score = 20)</span></label>
                <input type="number" name="emailscore" value="{{ old('emailscore')}}" class="form-control" min="0"
                    max="100">
            </div>
            <div><small style="color:red">{{ $errors->first('emailscore')}}</small></div>
    </div>

    <div class="col-md-6">
       

        <div class="form-group">
            <label>Role Play Score* <span style="color:green">(Max score = 25)</span></label>
            <input type="number" name="roleplayscore" value="{{ old('roleplayscore')}}" class="form-control" min="0"
                max="100" required>
        </div>
        <div><small style="color:red">{{ $errors->first('roleplayscore')}}</small></div>

        <div class="form-group">
            <label>Certification Score*<span style="color:green">(Max score = 20)</span></label>
            <input type="number" name="certificationscore" class="form-control" min="0" max="100" required>
        </div>
        <div><small style="color:red">{{ $errors->first('certificationscore')}}</small></div>

        <div class="form-group">
            <label>Set Pass Mark*</label>
            <input type="number" name="passmark" value="{{ old('passmark')}}" class="form-control" min="0" max="100"
                required>
        </div>
        <small><small style="color:red">{{ $errors->first('passmark')}}</small></small>
    </div>
</div>
<div class="row">

    <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">
</div>


{{ csrf_field() }}