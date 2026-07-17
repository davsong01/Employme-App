@extends('dashboard.student.trainingsindex')
@section('title', 'Download Certificate')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <div class="pb-2">
                @if($certificate->allow_new_certificate_request && !$pendingRegenerationRequests)
                <!-- Button to Open Modal -->
                <a class="btn btn-info" style="color:white" data-bs-toggle="modal" data-bs-target="#dateIssuedModal">
                    Generate New Certificate
                </a>

                <!-- Modal -->
                <div class="modal fade" id="dateIssuedModal" tabindex="-1" aria-labelledby="dateIssuedModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="dateIssuedModalLabel">Enter Date Issued</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('participants.certificates.new.request', $certificate->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <label for="date_issued">Date Issued (Optional)</label>
                                    <input type="date" class="form-control" name="date_issued">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Request</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @endif
            </div>
            @if($regenerationRequests->count() > 0)
            <h5 class="card-title mt-4">Regeneration Request History</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Program</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regenerationRequests as $index => $request)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $request->program->p_name }}</td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span style="width: 60px;" class="badge bg-warning text-dark">Pending</span>
                                    @elseif($request->status == 'approved')
                                        <span style="width: 60px;" class="badge bg-success">Approved</span>
                                    @elseif($request->status == 'rejected')
                                        <span style="width: 60px;" class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No regeneration requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
            <h5 class="card-title">Certificate History</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
                <table id="" class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Training</th>
                            <th>Certificate Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $certificate->user->name }} <br>
                            </td>
                            <td>{{ $certificate->program->p_name }}</td>
                            <td style="color:{{ $certificate->show_certificate() == 'Disabled' ? 'red' : 'green'}}">
                                Certificate Status: <strong>{{ $certificate->show_certificate() }}</strong>
                                @if($certificate->certificate_number)
                                <br>Certificate No: <strong>{{ $certificate->certificate_number }}</strong> <br>
                                <div class="mb-3">
                                    <button id="copy-btn{{$certificate->id}}" class="btn btn-primary">
                                        <i class="fa fa-copy"></i> Copy Certificate Verification Link
                                    </button>
                                    <small id="copy-status{{$certificate->id}}" style="color: green; display: none;"></small>
                                </div>
                                <input type="text" id="verification-link{{$certificate->id}}" value="{{ env('WAACSP_CERTIFICATE_VERIFICATION_LINK').'?certificate_number='.$certificate->certificate_number }}" hidden>

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
                                <a data-bs-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-info" href="/download-certificate/{{ $certificate->file }}"><i
                                    class="fa fa-download"> Download Certificate</i></a>
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
