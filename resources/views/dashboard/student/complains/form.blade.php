<div class="form-group">
    <label>Title *</label>
    <input type = "text" name="title" value = "{{ old('title') }}" class="form-control" required>
</div>

<!--Gives the first error for input name-->
<div><small>{{ $errors->first('title')}}</small></div>

<div class="form-group">
    <label for="class">Select Training *</label>
    <select name="program_id" id="program_id" class="form-control">
            <option value=""></option>
            @foreach ($programs as $program)
            <option value="{{ $program->id }}">{{$program->p_name}}</option>
        @endforeach
    </select>
    <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
</div>
<div class="form-group">
    <label>Select file</label>
    <input type="file" name="file" value="" class="form-control">
</div>
<div><small style="color:red">{{ $errors->first('file')}}</small></div>
<input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

{{ csrf_field() }}