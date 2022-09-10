    @extends('dashboard.admin.index')
    @section('title', $material->title )
    @section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            @include('layouts.partials.alerts')
                            <h4 class="card-title">Cloning: {{$material->title}} from <i style="color:red">
                                    {{$material->program->p_name}}</i></h4> to
                        </div>
                        <form action="{{ route('material.clone', $material ) }}" method="POST" class="pb-2">
                            {{ csrf_field() }}
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
                                <input hidden type="text" name="title" value="{{$material->title}}">
                                <input hidden type="text" name="file" value="{{$material->file}}">
                            </div>

                            <input type="submit" name="submit" value="Clone Material" class="btn btn-primary"
                                style="width:100%">

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection