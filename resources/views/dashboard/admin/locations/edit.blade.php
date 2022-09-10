    @extends('dashboard.admin.index')
    @section('title', $location->title )
    @section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            @include('layouts.partials.alerts')

                        </div>
                        <form action="{{ route('locations.update', $location->id ) }}" method="POST" class="pb-2">
                            {{ csrf_field() }}
                             {{ method_field('PATCH') }}
                            <div class="form-group">

                                <label for="class">Title (e.g: Lagos)</label>

                                <input type="text" class="form-control" name="title" required value={{ old('value') ?? $location->title }}>

                                <div><small style="color:red">{{ $errors->first('title')}}</small></div>

                            </div>

                            <div class="form-group">

                                <label for="class">Select Training *</label>

                                <select name="program_id" id="program_id" class="form-control" required>
                                    @foreach ($programs as $program)

                                    <option value="{{ $program->id }}" {{ $program->id == $location->program_id ? 'selected' : ''}} >{{ $program->p_name }}</option>

                                    @endforeach
                                </select>

                                <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
            
                            </div>

                            <button type="submit" class="btn btn-primary"
                                style="width:100%">Update</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection