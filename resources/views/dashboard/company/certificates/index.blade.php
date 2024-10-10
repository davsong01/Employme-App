@extends('dashboard.student.trainingsindex')
@section('title', 'Download Certificate')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="">
                <table id="" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Training</th>
                            <th>Certificate Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <div class="text-center">
                            <h5 class="card-title">Please Download your certificate below</h5>
                        </div>
                        <tr>
                            <td>{{ $certificate->user->name }} <br>
                            </td>
                            <td>{{ $certificate->program->p_name }}</td>
                            <td style="color:{{ $certificate->show_certificate() == 'Disabled' ? 'red' : 'green'}}">
                                Certificate Status: <strong>{{ $certificate->show_certificate() }}</strong>
                                @if($certificate->certificate_number)
                                <br>Certificate No: <strong>{{ $certificate->certificate_number }}</strong> <br>
                                <div class="form-group mb-3">
                                    <button id="copy-btn{{$certificate->id}}" class="btn btn-primary">
                                        <i class="fa fa-copy"></i> Copy Certificate Verification Link
                                    </button>
                                    <small id="copy-status{{$certificate->id}}" style="color: green; display: none;"></small>
                                </div>
                                <input type="text" id="verification-link{{$certificate->id}}" value="{{url('/api/verify-certificate').'?certificate_number='.$certificate->certificate_number }}" hidden>
                                <script>
                                    $('#copy-btn{{$certificate->id}}').click(function() {
                                        var verificationLink = $('#verification-link{{$certificate->id}}').val();
                                        
                                        var tempInput = $('<input>');
                                        $('body').append(tempInput);
                                        tempInput.val(verificationLink).select();
                                        document.execCommand("copy");
                                        tempInput.remove(); 

                                        $('#copy-status{{$certificate->id}}').text("{{$certificate->certificate_number}} Copied");
                                        $('#copy-status{{$certificate->id}}').fadeIn().delay(2000).fadeOut();
                                    });
                                </script>
                                @endif
                            </td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-info" href="/certificate/{{ $certificate->file }}"><i
                                    class="fa fa-download"> Download Certificate</i></a>
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    // $('#zero_config').DataTable();
</script>
@endsection