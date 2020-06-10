@extends('dashboard.admin.index')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix.css">
@endsection
@section('title', 'Email Participants')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div>
                <div>
                    <h5>Email Participants</h5>
                </div>
            </div>

            <form action="{{ route('user.sendmail') }}" method="POST">
                {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <p>Please select a program and type in the content of the mail you want to send, then click send
                        button to email all the participants of that program</p>
                </div>
            </div>
            <div class="row">
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('program') ? ' has-error' : '' }}">
                            <label>Choose Program</label>
                            <select name="program" id="program" class="form-control custom-select-value" required>
                                <option value="">Choose option</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->p_name }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('program'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('program') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Type Email Content</label>
                            <input id="x" type="hidden" name="content" class="form-control">
                            <trix-editor input="x"></trix-editor>
                        </div>
                        @if($errors->has('content'))
                            <span class="help-block">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                        @endif
                    </div>
            </div>
            <div class="row">

                <button type="submit" class="btn btn-primary" style="width:100%">

                    Send Email

                </button>

            </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix-core.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.0/trix.js"></script>
@endsection