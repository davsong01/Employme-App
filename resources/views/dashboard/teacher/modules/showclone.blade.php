    @extends('dashboard.admin.index')
    @section('title', $module->title )
    @section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            @include('layouts.partials.alerts')
                            <h4 class="card-title">Cloning: {{$module->title}} from <i style="color:red">
                                    {{$module->program->p_name}}</i></h4> to
                        </div>
                        <form action="{{ route('module.clone', $module ) }}" method="POST" class="pb-2">
                            {{ csrf_field() }}
                            <!--Gives the first error for input name-->
                            <div class="form-group">

                                <label for="class">Select Training *</label>

                                <select name="program_id" id="program_id" class="form-control" required>

                                    <option value="">--Select Option--</option>
                                    @foreach ($programs as $program)
                                        @if($program->id <> $module->program->id)
                                        <option value="{{ $program->id }}">{{$program->p_name}}</option>
                                        @endif
                                    @endforeach

                                </select>
                                <div><small style="color:red">{{ $errors->first('program_id')}}</small></div>
                                <input hidden type="text" name="title" value="{{$module->title}}">
                                <input hidden type="text" name="id" value="{{$module->id}}">
                            </div>

                            <button type="submit"class="btn btn-primary"
                                style="width:100%">Clone Now</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection