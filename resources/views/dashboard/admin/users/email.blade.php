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
                        <p>Please select an email type and type in the content of the mail you want to send, the content and then the send button</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3 type">
                            <label>Select Type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="">Choose option</option>
                                <option value="bulk">Program Participants</option>
                                <option value="selected">Selected Participants</option>
                                <option value="bulkrecipients">Bulk Email</option>
                            </select>
                            @if($errors->has('type'))
                                <div class="text-danger small mt-1">{{ $errors->first('type') }}</div>
                            @endif
                        </div>
                        <div class="mb-3 bulkemail">
                            <label>Choose Program</label>
                            <select name="program" id="program" class="form-select">
                                <option value="">Choose option</option>
                                @foreach($programs as $program)
                                    @if($program->users_count > 0)
                                    <option value="{{ $program->id }}">{{ $program->p_name }} ({{ $program->users_count }})</option>
                                    @endif
                                @endforeach
                            </select>
                            @if($errors->has('program'))
                                <div class="text-danger small mt-1">{{ $errors->first('program') }}</div>
                            @endif
                        </div>
                        <div class="mb-3 selectedemail">
                            <label>Select recipients</label>
                            <select name="selectedemail[]" id="selectedemail" class="select2 form-select mt-3" multiple="multiple" style="width: 100%;">
                                <option value="">Choose option</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->email }}">{{ $user->email }} ( {{ $user->name }} )</option>
                                @endforeach
                            </select>
                            @if($errors->has('selectedemail'))
                                <div class="text-danger small mt-1">{{ $errors->first('selectedemail') }}</div>
                            @endif
                        </div>

                        <div class="mb-3 bulkrecipients">
                            <textarea style="width:100%" name="bulkrecipients" id="bulkrecipients" rows="15" placeholder="Paste the emails here, each email on a new line"></textarea>
                            @if($errors->has('bulkrecipients'))
                                <div class="text-danger small mt-1">{{ $errors->first('bulkrecipients') }}</div>
                            @endif
                        </div>

                        
                        <div class="mb-3">

                            <label for="subject">Subject</label>

                            <input id="subject" type="text" class="form-control" name="subject"
                                value="{{ old('subject')}}" required autofocus>

                            @if ($errors->has('subject'))
                            <div class="text-danger small mt-1">{{ $errors->first('subject') }}</div>
                            @endif

                        </div>
                        <div class="mb-3">
                            <label>Type Email Content (<strong style="color:red">Dear {Participant's name} is automatically added at the top of this mail</strong>)</label>

                            <textarea class="form-control" id="summary-ckeditor" name="content"></textarea>
                        </div>
                        @if($errors->has('content'))
                            <div class="text-danger small mt-1">{{ $errors->first('content') }}</div>
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
                <div class="">
                    <table id="zero_config" class="">
                        <thead>
                            <tr>
                                <th>#</th>
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
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Edit email" class="btn btn-info"
                                                href="{{ route('updateemails.show', $email->id) }}"><i
                                                    class="fa fa-eye"></i>
                                            </a>
            
            
                                            {{-- <form action="{{ route('emails.destroy', $email->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Do you really want to Delete?');"> --}}
                                            {{--                                 
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }} --}}
            
                                            {{-- <button type="submit" class="btn btn-warning" data-bs-toggle="tooltip"
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

<script>
    CKEDITOR.replace('summary-ckeditor');
    
    var program = $('#program');
    var selectedemail = $('#selectedemail');
    var bulkrecipients = $('#bulkrecipients');

    $('#type').on('change', function(){
        console.log($('#type').val());
            
            if($('#type').val()=='bulk' ){
                $('.bulkemail').css('display','block');
                program.attr('required', true);
                $('.selectedemail').css('display','none');
                $('.bulkrecipients').css('display','none');
                selectedemail.attr('required', false);
                bulkrecipients.attr('required', false);
                
                
            }else if($('#type').val()=='selected'){
                $('.selectedemail').css('display','block');
                selectedemail.attr('required', true);
                $('.bulkemail').css('display','none');
                $('.bulkrecipients').css('display','none');
                program.attr('required', false);
                bulkrecipients.attr('required', false);

            }else if($('#type').val()=='bulkrecipients'){
                $('.bulkrecipients').css('display','block');
                bulkrecipients.attr('required', true);
                $('.bulkemail').css('display','none');
                $('.selectedemail').css('display','none');
                program.attr('required', false);
                selectedemail.attr('required', false);
               
            }
           
    });

</script> 
@endsection
