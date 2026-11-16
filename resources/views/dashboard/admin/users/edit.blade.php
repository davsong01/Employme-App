@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
<style>
.select2-container--default.select2-container--focus .select2-selection--multiple {
    height: 200px !important;
}
.participant-dashboard .metric-card { border: 1px solid #e5e7eb; border-radius: 16px; }
.participant-dashboard .metric-value { font-size: 1.7rem; font-weight: 700; line-height: 1.1; }
.participant-dashboard .section-card { border: 1px solid #e5e7eb; border-radius: 16px; }
.participant-dashboard .program-actions { display: flex; flex-wrap: wrap; gap: .4rem; }
.participant-dashboard .dashboard-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
@media (max-width: 767.98px) {
    .participant-dashboard .program-table, .participant-dashboard .program-table tbody,
    .participant-dashboard .program-table tr, .participant-dashboard .program-table td { display: block; width: 100%; }
    .participant-dashboard .program-table thead { display: none; }
    .participant-dashboard .program-table tr { border: 1px solid #e5e7eb; border-radius: 12px; padding: .75rem; margin-bottom: .75rem; }
    .participant-dashboard .program-table td { border: 0; padding: .3rem 0; }
    .participant-dashboard .program-table td::before { content: attr(data-label); display: block; color: #6b7280; font-size: .75rem; font-weight: 600; text-transform: uppercase; }
    .participant-dashboard .program-actions .btn { flex: 1 1 auto; }
}
</style>
   
@endsection
@section('content')
<div class="container-fluid">
    @php
        $enrollments = $participantPrograms->unique('program_id');
        $pretestTotal = $pretestCounts->sum();
        $posttestTotal = $posttestCounts->sum();
    @endphp
    <div class="participant-dashboard mb-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <span class="badge bg-light text-primary mb-2">Participant dashboard</span>
                        <h1 class="h3 mb-1">{{ $user->name }}</h1>
                        <div class="text-muted">{{ $user->email }} @if($user->phone) · {{ $user->phone }} @endif</div>
                        <div class="small text-muted mt-1">Staff ID: {{ $user->staffID ?: 'Not set' }} · Status: {{ ucfirst($user->status ?: 'active') }}</div>
                    </div>
                    <div class="dashboard-actions">
                        @if(checkRoleHas(['Admin', 'Facilitator']))
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('pretest.select', ['email' => $user->email]) }}">All pretests</a>
                        @endif
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('posttest.results', ['email' => $user->email]) }}">All posttests</a>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('certificates.index', ['search' => $user->email]) }}">All certificates</a>
                        <a href="#edit-details" class="btn btn-primary btn-sm">Edit details</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            @foreach(['Programs' => $enrollments->count(), 'Pretest records' => $pretestTotal, 'Posttest records' => $posttestTotal, 'Certificates' => $certificates->count()] as $label => $count)
                <div class="col-6 col-lg-3"><div class="card metric-card h-100"><div class="card-body"><div class="text-muted small mb-2">{{ $label }}</div><div class="metric-value">{{ number_format($count) }}</div></div></div></div>
            @endforeach
        </div>

        <div class="card section-card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="h5 mb-0">Programs and results</h2>
                    @if(!empty(canUserAccessPermission(['payments.index'])['payments.index']))
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('payments.index', ['email' => $user->email]) }}">Payment history</a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle program-table mb-0">
                        <thead><tr><th>Program</th><th>Pretests</th><th>Posttests</th><th>Certificates</th><th>Open records</th></tr></thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                @php($programId = $enrollment->program_id)
                                <tr>
                                    <td data-label="Program" class="fw-semibold">{{ $enrollment->program?->p_name ?? 'Program unavailable' }}</td>
                                    <td data-label="Pretests">{{ number_format($pretestCounts[$programId] ?? 0) }}</td>
                                    <td data-label="Posttests">{{ number_format($posttestCounts[$programId] ?? 0) }}</td>
                                    <td data-label="Certificates">{{ number_format($certificateCounts[$programId] ?? 0) }}</td>
                                    <td data-label="Open records">
                                        <div class="program-actions">
                                            @if(checkRoleHas(['Admin', 'Facilitator']))
                                                <a class="btn btn-outline-primary btn-sm" href="{{ route('pretest.select', ['email' => $user->email, 'program_id' => $programId]) }}">Pretests</a>
                                            @endif
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('posttest.results', ['email' => $user->email, 'program_id' => $programId]) }}">Posttests</a>
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('certificates.index', ['search' => $user->email, 'program_id' => $programId]) }}">Certificates</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">No programs are available for this participant.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card section-card">
            <div class="card-body">
                <h2 class="h5 mb-3">Certificates</h2>
                @forelse($certificates as $certificate)
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-2 border-bottom">
                        <div><strong>{{ $certificate->program?->p_name ?? 'Program unavailable' }}</strong><div class="small text-muted">{{ $certificate->certificate_number ?: 'Certificate #'.$certificate->id }}</div></div>
                        @if($certificate->file)
                            <a class="btn btn-outline-primary btn-sm" href="{{ url('download-certificate/'.$certificate->file) }}">Download</a>
                        @else
                            <span class="text-muted small">File unavailable</span>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">No certificates yet.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="edit-details">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Edit details for: {{$user->name}}</h4>
                    </div>
                    <form action="{{route('users.update', $user->id)}}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                        @if(!empty($currentTransaction?->transid))
                            <input type="hidden" name="payment_transid" value="{{ $currentTransaction->transid }}">
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3{{ $errors->has('name') ? ' is-invalid' : '' }}">
                                    <label for="name">Name</label>
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}"
                                        autofocus>
                                    @if ($errors->has('name'))
                                        <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3{{ $errors->has('email') ? ' is-invalid' : '' }}">
                                    <label for="email">E-Mail Address</label>
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email }}">
                                    @if ($errors->has('email'))
                                        <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                    @endif
                                </div>
                                <div class="mb-3{{ $errors->has('phone') ? ' is-invalid' : '' }}">
                                    <label for="phone">Phone</label>
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') ?? $user->phone }}"
                                        autofocus>
                                    @if ($errors->has('phone'))
                                        <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                    @endif
                                </div> 
                                <div class="mb-3{{ $errors->has('job_title') ? ' is-invalid' : '' }}">
                                    <label for="job_title">Job Title</label>
                                    <input id="job_title" type="text" class="form-control" name="job_title" value="{{ old('job_title') ?? $user->job_title }}"
                                        autofocus>
                                    @if ($errors->has('job_title'))
                                        <div class="text-danger small mt-1">{{ $errors->first('job_title') }}</div>
                                    @endif
                                </div>  
                            </div>
                            <div class="col-md-6">
                                 <div class="mb-3{{ $errors->has('password') ? ' is-invalid' : '' }}">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="text-muted small mb-2">Default: 12345</div>
                                    <input id="password" type="password" class="form-control" name="password" value="{{ old('password') ?? '' }}"
                                        autofocus>
                                    @if ($errors->has('password'))
                                        <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                <div><small style="color:red">{{ $errors->first('class')}}</small></div>
                               
                                <div class="mb-3">
                                    <label for="class">Role *</label>
                                    <select name="role" id="class" class="form-control">
                                        <option value="" disabled>Assign Role</option>
                                        <option value="Student" {{ $user->roles == 'Student' ? 'selected' : ''}}>Student</option>
                                        @if(checkRoleHas(['Admin']))
                                        <option value="Teacher" {{ $user->roles == 'Teacher' ? 'selected' : ''}}>Facilitator</option>
                                        <option value="Grader" {{ $user->roles == 'Grader' ? 'selected' : ''}}>Grader</option>
                                        @endif
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('role')}}</small></div>
                                </div>

                                <div class="mb-3">
                                    <label for="status">Status *</label>
                                    <select name="status" id="status" class="form-control" required>
                                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', $user->status ?: 'active') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="class">Gender</label>
                                    <select name="gender" id="class" class="form-control">
                                        <option value="" disabled>Select Gender</option>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                        
                                    </select>
                                    <div><small style="color:red">{{ $errors->first('gender')}}</small></div>
                                </div>
                                
                                <div class="mb-3{{ $errors->has('staffID') ? ' is-invalid' : '' }}">
                                    <label for="staffID">Staff Id</label>
                                    <input id="staffID" type="text" class="form-control" name="staffID" value="{{ old('staffID') ?? $user->staffID }}"
                                        autofocus>
                                    @if ($errors->has('staffID'))
                                        <div class="text-danger small mt-1">{{ $errors->first('staffID') }}</div>
                                    @endif
                                </div>
                            </div>
                            @if(isset($associated))
                                <div class="col-md-12">
                                    <div class="row mb-3">
                                        <label class="training">Select Training(s)</label>
                                        <select name="training[]" id="training" class="select2 form-select mt-3" multiple="multiple" style="width: 100%;" required>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" {{ in_array($program->id, $associated) ? 'selected' : ''}} >{{ $program->p_name }}</option>
                                        @endforeach
                                        </select>

                                        <div>
                                        @if ($errors->has('training'))
                                            <div class="text-danger small mt-1">{{ $errors->first('training') }}</div>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Update
                            </button>
                        </div>
                    </form>
                      
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
