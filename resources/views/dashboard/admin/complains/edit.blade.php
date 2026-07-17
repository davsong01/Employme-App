@extends($extend)
@section('title', 'Edit CRM Case')
@section('content')
@php
    $status = $complain->status ?? 'Pending';
    $priority = $complain->priority ?? 'Medium';
    $statusBadge = match ($status) {
        'Resolved' => 'success',
        'In Progress' => 'warning',
        'Pending' => 'secondary',
        default => 'dark',
    };
    $priorityBadge = match ($priority) {
        'High' => 'danger',
        'Medium' => 'warning',
        'Low' => 'success',
        default => 'secondary',
    };
@endphp
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">CRM Desk</span>
                            <h1 class="h3 fw-bold mb-2">Case management</h1>
                            <p class="text-muted mb-0">Review the customer record, update the case, and keep the follow-up moving.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }} rounded-pill px-3 py-2">{{ $status }}</span>
                            <span class="badge bg-{{ $priorityBadge }}-subtle text-{{ $priorityBadge }} rounded-pill px-3 py-2">{{ $priority }} priority</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>

        <div class="col-xl-8">
            <form action="{{ request()->prefix__ == '/admin' ? route('complains.update', ['complain' => $complain->id]) : route('participant.complains.update', ['complain' => $complain->id]) }}" method="POST" class="crm-case-form">
                @csrf
                @method('PATCH')

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h2 class="h5 mb-1">Customer profile</h2>
                        <p class="text-muted small mb-0">These details are read-only and help the team keep the case in context.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Customer name</label>
                                <input type="text" class="form-control" value="{{ $complain->name }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email address</label>
                                <input type="email" class="form-control" value="{{ $complain->email }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone number</label>
                                <input type="text" class="form-control" value="{{ $complain->phone }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gender</label>
                                <input type="text" class="form-control" value="{{ $complain->gender }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" value="{{ $complain->state }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">LGA</label>
                                <input type="text" class="form-control" value="{{ $complain->lga }}" disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" value="{{ $complain->address }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact mode</label>
                                <select class="form-select" name="mode">
                                    <option value="Phone Call" {{ $complain->mode == 'Phone Call' ? 'selected' : '' }}>Phone Call</option>
                                    <option value="Email" {{ $complain->mode == 'Email' ? 'selected' : '' }}>Email</option>
                                    <option value="Whatsapp" {{ $complain->mode == 'Whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="Twitter" {{ $complain->mode == 'Twitter' ? 'selected' : '' }}>Twitter / X</option>
                                    <option value="Facebook" {{ $complain->mode == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="Instagram" {{ $complain->mode == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="Other" {{ $complain->mode == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Other contact details</label>
                                <input type="text" class="form-control" name="other" value="{{ old('other', $complain->other) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Training</label>
                                <input type="text" class="form-control" value="{{ $complain->program->p_name ?? 'N/A' }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h2 class="h5 mb-1">Case details</h2>
                        <p class="text-muted small mb-0">Keep the case classification and follow-up information up to date.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject', $complain->subject) }}" placeholder="Short CRM headline">
                                @error('subject')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" id="category" name="category" class="form-control" value="{{ old('category', $complain->category) }}" placeholder="Billing, support, service, etc.">
                                @error('category')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="follow_up_at" class="form-label">Next follow-up</label>
                                <input type="date" id="follow_up_at" name="follow_up_at" class="form-control" value="{{ old('follow_up_at', optional($complain->follow_up_at)->format('Y-m-d')) }}">
                                @error('follow_up_at')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="type" class="form-label">Case type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="Complain" {{ $complain->type == 'Complain' ? 'selected' : '' }}>Complaint</option>
                                    <option value="Enquiry" {{ $complain->type == 'Enquiry' ? 'selected' : '' }}>Enquiry</option>
                                    <option value="Request" {{ $complain->type == 'Request' ? 'selected' : '' }}>Request</option>
                                </select>
                                @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="issues" class="form-label">Issue</label>
                                <select name="issues" id="issues" class="form-select"></select>
                                @error('issues')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="Low" {{ $complain->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ $complain->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ $complain->priority == 'High' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="{{ $complain->status }}" selected>{{ $complain->status }}</option>
                                    @if(checkRoleHas(['Admin','Facilitator']))
                                    <option value="Resolved" {{ $complain->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    @endif
                                </select>
                                @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="teamlead" class="form-label">Team lead</label>
                                <input id="teamlead" type="text" class="form-control" name="teamlead" value="{{ old('teamlead', $complain->teamlead) }}" placeholder="Optional owner or supervisor">
                                @error('teamlead')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tags" class="form-label">Tags</label>
                                <input id="tags" type="text" class="form-control" name="tags" value="{{ old('tags', $complain->tags) }}" placeholder="vip, urgent, billing">
                                @error('tags')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h2 class="h5 mb-1">Narrative and response</h2>
                        <p class="text-muted small mb-0">Update the case narrative, response, and internal notes.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="content" class="form-label">Case description</label>
                                <textarea id="crm-content-editor" class="form-control" name="content" rows="10" required>{!! old('content', $complain->content) !!}</textarea>
                                @error('content')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="response" class="form-label">Initial response</label>
                                <textarea id="crm-response-editor" class="form-control" name="response" rows="10" placeholder="Optional response or next action">{!! old('response', $complain->response) !!}</textarea>
                                @error('response')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Internal notes</label>
                                <textarea class="form-control" name="notes" rows="4" placeholder="Optional internal note for the team">{{ old('notes', $complain->notes) }}</textarea>
                                @error('notes')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid d-md-flex justify-content-md-end gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-4">Update Case</button>
                </div>
            </form>

            @if($complain->notes)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3">Latest internal note</h3>
                        <div class="text-muted small mb-2">Visible to the team only</div>
                        <div class="border rounded-3 bg-light p-3">{!! $complain->notes !!}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm sticky-xl-top crm-sidebar">
                <div class="card-body">
                    <h3 class="h5 mb-3">Case summary</h3>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Ticket</span>
                            <span class="fw-semibold">EMPL000{{ $complain->id }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status</span>
                            <span class="badge bg-{{ $statusBadge }}-subtle text-{{ $statusBadge }}">{{ $status }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Priority</span>
                            <span class="badge bg-{{ $priorityBadge }}-subtle text-{{ $priorityBadge }}">{{ $priority }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Submitted by</span>
                            <span class="fw-semibold text-end">{{ $complain->name }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Training</span>
                            <span class="fw-semibold text-end">{{ $complain->program->p_name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Created</span>
                            <span class="fw-semibold text-end">{{ optional($complain->created_at)->format('d M Y H:i') }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Updated</span>
                            <span class="fw-semibold text-end">{{ optional($complain->updated_at)->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
<script>
    $(document).ready(function () {
        const currentType = @json(old('type', $complain->type));
        const currentIssue = @json(old('issues', $complain->issues));
        const currentStatus = @json(old('status', $complain->status));

        function populateCaseOptions(type, selectedIssue, selectedStatus) {
            const issueSelect = $('#issues');
            const statusSelect = $('#status');

            issueSelect.empty();
            statusSelect.empty();

            issueSelect.append('<option value="">- Select -</option>');
            statusSelect.append('<option value="">- Select -</option>');

            if (type === 'Complain') {
                issueSelect.append('<option value="Drop Balance">Drop Balance</option>');
                issueSelect.append('<option value="Network Issues">Network Issues</option>');
                issueSelect.append('<option value="Recharge Issues">Recharge Issues</option>');
                issueSelect.append('<option value="Data Issues">Data Issues</option>');
                issueSelect.append('<option value="Late delivery">Late delivery</option>');
                issueSelect.append('<option value="Damages">Damages</option>');

                statusSelect.append('<option value="Pending">Pending</option>');
                statusSelect.append('<option value="In Progress">In Progress</option>');
                @if(checkRoleHas(['Admin','Facilitator','Grader']))
                statusSelect.append('<option value="Resolved">Resolved</option>');
                @endif
            } else if (type === 'Enquiry') {
                issueSelect.append('<option value="Product Enquires">Product Enquires</option>');
                issueSelect.append('<option value="Recharge Enquires">Recharge Enquires</option>');
                issueSelect.append('<option value="Opening hours">Opening hours</option>');
                issueSelect.append('<option value="Office location">Office location</option>');
                issueSelect.append('<option value="Cost of product">Cost of product</option>');

                statusSelect.append('<option value="Resolved">Resolved</option>');
            } else if (type === 'Request') {
                issueSelect.append('<option value="Product Request">Product Request</option>');
                issueSelect.append('<option value="Recharge Request">Recharge Request</option>');
                issueSelect.append('<option value="Home delivery">Home delivery</option>');
                issueSelect.append('<option value="Exchange (Size or colour)">Exchange (Size or colour)</option>');

                statusSelect.append('<option value="Pending">Pending</option>');
                statusSelect.append('<option value="In Progress">In Progress</option>');
                @if(checkRoleHas(['Admin','Facilitator','Grader']))
                statusSelect.append('<option value="Resolved">Resolved</option>');
                @endif
            }

            if (selectedIssue) {
                if (! issueSelect.find(`option[value="${selectedIssue}"]`).length) {
                    issueSelect.append(`<option value="${selectedIssue}">${selectedIssue}</option>`);
                }
                issueSelect.val(selectedIssue);
            }

            if (selectedStatus) {
                if (! statusSelect.find(`option[value="${selectedStatus}"]`).length) {
                    statusSelect.append(`<option value="${selectedStatus}">${selectedStatus}</option>`);
                }
                statusSelect.val(selectedStatus);
            } else if (type === 'Request') {
                statusSelect.val('Pending');
            }
        }

        $('#type').on('change', function () {
            populateCaseOptions($(this).val());
        });

        populateCaseOptions(currentType, currentIssue, currentStatus);

        const compactEditorConfig = {
            toolbar: [
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'editing', items: ['Undo', 'Redo'] }
            ],
            removeButtons: 'Subscript,Superscript,Anchor,Image,Table,HorizontalRule,SpecialChar,Source,Maximize,Scayt,About',
            removePlugins: 'elementspath,flash,forms,iframe,smiley,showblocks',
            extraPlugins: '',
            height: 220
        };

        CKEDITOR.replace('crm-response-editor', compactEditorConfig);
        CKEDITOR.replace('crm-content-editor', compactEditorConfig);
    });
</script>
@endsection
