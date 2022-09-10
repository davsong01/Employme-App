<form action="{{ route('pictures.store') }}" method="POST" enctype="multipart/form-data" class="pb-2">
    <div class="form-group">
        <label>Caption *</label>
        <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
    </div>

    <!--Gives the first error for input name-->
    <div><small>{{ $errors->first('title')}}</small></div>

    <div class="form-group">
        <label>Select file</label>
        <input type="file" name="file" value="" class="form-control">
    </div>
    <div><small style="color:red">{{ $errors->first('file')}}</small></div>
    <input type="submit" name="submit" value="Submit" class="btn btn-primary" style="width:100%">

    {{ csrf_field() }}
</form>