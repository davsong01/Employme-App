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
                    <h3>Email Participants</h3>
                </div>
            </div>

            <form action="{{ route('user.sendmail') }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-12">
                        <p>Please select a program and type in the content of the mail you want to send, then click send
                            button to email all the participants of that program</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div
                            class="form-group{{ $errors->has('program') ? ' has-error' : '' }}">
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
                        <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">

                            <label for="subject">Subject</label>

                            <input id="subject" type="text" class="form-control" name="subject"
                                value="{{ old('subject')}}" required autofocus>

                            @if ($errors->has('subject'))
                            <span class="help-block">
                                <strong>{{ $errors->first('subject') }}</strong>
                            </span>

                            @endif

                        </div>
                        <div class="form-group">
                            <label>Type Email Content (<strong style="color:red">Dear {Participant's name} is automatically added at the top of this mail</strong>)</label>

                            <textarea class="form-control" id="summary-ckeditor" name="content"></textarea>
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
            <div class="row">
                <div class="card-title" style="margin-top:30px">
                    <h3>Emails History</h3>
                </div>
                <div class="table-responsive">
                    <table id="zero_config" class="">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Training</th>
                                <th>Sender</th>
                                <th>Subject</th>
                                <th>No of Recipients</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($updateemails as $email)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $email->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $email->program }}</td>
                                    <td>{{ $email->sender }}</td>
                                    <td>{{ $email->subject }}</td>
                                    <td>{{ $email->noofemails }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a data-toggle="tooltip" data-placement="top" title="Edit email" class="btn btn-info"
                                                href="{{ route('updateemails.show', $email->id) }}"><i
                                                    class="fa fa-eye"></i>
                                            </a>
            
            
                                            {{-- <form action="{{ route('emails.destroy', $email->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Do you really want to Delete?');"> --}}
                                            {{--                                 
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }} --}}
            
                                            {{-- <button type="submit" class="btn btn-warning" data-toggle="tooltip"
                                                data-placement="top" title="Trash Training"> <i class="fa fa-trash"></i>
                                            </button> --}}
                                            {{-- </form> --}}
                                        </div>
            
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
   
</div>
@endsection
@section('extra-scripts')

<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('summary-ckeditor');
</script>


@endsection