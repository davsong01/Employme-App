@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <h5 class="card-title" style="color:green"> Certificate Generation Requests </h5><br>
                @include('layouts.partials.alerts')
            </div>
        
            <div class="card-body">
                <div class="pb-2">
                    <!-- Button to Open Modal -->
                    <a class="btn btn-info" style="color:white" data-bs-toggle="modal" data-bs-target="#dateIssuedModal">
                        Generate New Certificate
                    </a>
    
                    <!-- Modal -->
                    <div class="modal fade" id="dateIssuedModal" tabindex="-1" aria-labelledby="dateIssuedModalLabel" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="dateIssuedModalLabel">Enter Date Issued</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form onsubmit="return confirm('Are you sure?')" action="{{ route('admin.create.certificate.request') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group mb-3">
                                            <label for="program_id">Select User</label>
                                            <select name="user_id" id="user_id" class="form-control select2" required>
                                                <option value="">-- Select User --</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{$user->email}}) - {{ $user->certificates_count}}Certificates</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="program_id">Select Program</label>
                                            <select name="program_id" id="program_id" class="form-control select2" required>
                                                <option value="">-- Select Program --</option>
                                                @foreach($programs as $program)
                                                    <option value="{{ $program->id }}">{{ $program->p_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    
                                        <div class="form-group mb-3">
                                            <label for="date_issued">Date Issued (Optional)</label>
                                            <input type="date" class="form-control" name="date_issued">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Submit Request</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Program Title</th>
                            <th>Date Requested</th>
                            <th>Participant</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regenerationRequests as $index => $request)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $request->program->p_name }}</td>
                            <td>{{ $request->created_at->format('d M Y') }}</td>
                            <td>{{ $request->user->getName() }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <span style="width: 60px;" class="badge bg-warning text-dark">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span style="width: 60px;" class="badge bg-success">Approved</span>
                                @elseif($request->status == 'declined')
                                    <span style="width: 60px;" class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                {{-- download certificate --}}
                                @if($request->status == 'approved')
                                <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-primary btn-sm" href="/download-certificate/{{ $request->certificate->file }}"><i
                                    class="fa fa-download"> Download Certificate</i></a>
                                @endif
                                @if($request->status == 'pending')
                                    <form action="{{ route('certificate.requests.update', $request->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="certificate_id" value="{{ $request->certificate_id }}">
                                        <input type="hidden" name="user_id" value="{{ $request->user_id }}">
                                        <input type="hidden" name="program_id" value="{{ $request->program_id }}">
                                        <button class="btn btn-success btn-sm" onclick="return confirm('Approve this request?')">Accept</button>
                                    </form>
                    
                                    <form action="{{ route('certificate.requests.update', $request->id) }}" method="POST" style="display:inline-block; margin-left: 5px;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="reject">
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Reject this request?')">Decline</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
    
                    @endforeach
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('#dateIssuedModal').on('shown.bs.modal', function () {
        $('#program_id').select2({
            width: '100%',
            dropdownParent: $('#dateIssuedModal'),
            placeholder: 'Select a program'
        });

        $('#user_id').select2({
            width: '100%',
            dropdownParent: $('#dateIssuedModal'),
            placeholder: 'Select a user'
        });
    });
</script>
@endsection