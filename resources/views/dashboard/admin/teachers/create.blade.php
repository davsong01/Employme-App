@extends('dashboard.admin.index')
@section('title', 'Add Facilitator')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    <div class="card-title">
                        <h4 class="card-title">Add new Facilitator/Grader</h4>
                    </div>
                    <form action="{{route('teachers.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name')}}" autofocus required >
                                    @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Role *</label>
                                    <select name="role" id="role" class="form-control" >
                                        <option value="">Assign Role</option>
                                        <option value="Facilitator" {{ old('role') == 'Facilitator' ? 'selected' : '' }}>Facilitator</option>
                                        <option value="Grader" {{ old('role') == 'Grader' ? 'selected' : '' }}>Grader</option>
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>
                                 <div class="form-group">

                                    <label for="password">Password</label>

                                    <input id="password" type="text" class="form-control" name="password"
                                        value="{{ old('password') ?? 12345 }}" autofocus>

                                    @if ($errors->has('password'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('password') }}</strong>

                                    </span>

                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group row selecttraining">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-control m-t-15" multiple="multiple" style="height: 30px;width: 100%;">
                                            @foreach ($programs as $program)
                                            <option value="{{ $program->id }}">
                                                {{$program->p_name}}</option>
                                            @endforeach
                                        </select>
                                    <div>
                                        @if ($errors->has('training'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('training') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                <div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                
                                <div class="form-group row">
                                    <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                                </div>
                            </div>
                        </div>
                        {{ csrf_field() }}
                    
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('extra-scripts')
<script>                                
    $('#role').on('change', function(){
        console.log($('#role').val());
            
            if($('#role').val()=='Facilitator' || $('#role').val()=='Grader' ){
                $('.selecttraining').css('display','block');
                
            }else if($('#role').val()=='Admin'){
                $('.selecttraining').css('display','none');
                
            }
    });
</script>
@endsection
@endsection