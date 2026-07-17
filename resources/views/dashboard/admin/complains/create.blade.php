@extends($extend)
@section('title', 'Log New CRM Case')
@section('content')
@php
    $trainingName = $training->p_name ?? 'Selected training';
@endphp
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">CRM Desk</span>
                            <h1 class="h3 fw-bold mb-2">Log a new customer case</h1>
                            <p class="text-muted mb-0">Capture the customer, the issue, and the follow-up plan in one place.</p>
                        </div>
                        <div class="text-lg-end">
                            <div class="small text-uppercase text-muted fw-semibold">Training</div>
                            <div class="h5 mb-0">{{ $trainingName }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>

        <div class="col-xl-8">
            <form action="{{ request()->prefix__ == '/admin' ? route('complains.store') : route('participant.complains.store') }}" method="POST" class="crm-case-form">
                @csrf

                <input type="hidden" name="program_id" value="{{ $training->id }}">

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h2 class="h5 mb-1">Customer profile</h2>
                        <p class="text-muted small mb-0">Who raised the case and how we can reach them.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label">Customer name</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="phone" class="form-label">Phone number</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-select" required>
                                    <option value="">Choose</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="state" class="form-label">State</label>
                                <select name="state" id="state" class="form-select select2" required>
                                    <option value="">- Select -</option>
                                    @foreach(['Abia', 'Adamawa', 'AkwaIbom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'] as $state)
                                        <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                    @endforeach
                                </select>
                                @error('state')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="lga" class="form-label">LGA</label>
                                <select name="lga" id="lga" class="form-select" required></select>
                                @error('lga')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}" required>
                                @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="mode" class="form-label">Contact mode</label>
                                <select name="mode" id="mode" class="form-select" required>
                                    <option value="">- Select -</option>
                                    <option value="Phone Call" {{ old('mode') == 'Phone Call' ? 'selected' : '' }}>Phone Call</option>
                                    <option value="Email" {{ old('mode') == 'Email' ? 'selected' : '' }}>Email</option>
                                    <option value="Whatsapp" {{ old('mode') == 'Whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="Twitter" {{ old('mode') == 'Twitter' ? 'selected' : '' }}>Twitter / X</option>
                                    <option value="Facebook" {{ old('mode') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="Instagram" {{ old('mode') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="Other" {{ old('mode') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('mode')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="other" class="form-label">Other contact details</label>
                                <input id="other" type="text" class="form-control" name="other" value="{{ old('other') }}" placeholder="Optional notes about the contact channel">
                                @error('other')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h2 class="h5 mb-1">Case details</h2>
                        <p class="text-muted small mb-0">Classify the case and set the right follow-up path.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Short CRM headline">
                                @error('subject')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" id="category" name="category" class="form-control" value="{{ old('category') }}" placeholder="Billing, support, service, etc.">
                                @error('category')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="follow_up_at" class="form-label">Next follow-up</label>
                                <input type="date" id="follow_up_at" name="follow_up_at" class="form-control" value="{{ old('follow_up_at') }}">
                                @error('follow_up_at')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="type" class="form-label">Case type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="">- Select -</option>
                                    <option value="Complain" {{ old('type') == 'Complain' ? 'selected' : '' }}>Complaint</option>
                                    <option value="Enquiry" {{ old('type') == 'Enquiry' ? 'selected' : '' }}>Enquiry</option>
                                    <option value="Request" {{ old('type') == 'Request' ? 'selected' : '' }}>Request</option>
                                </select>
                                @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="issues" class="form-label">Issue</label>
                                <select name="issues" id="issues" class="form-select" required></select>
                                @error('issues')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="">- Select -</option>
                                    <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required></select>
                                @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="teamlead" class="form-label">Team lead</label>
                                <input id="teamlead" type="text" class="form-control" name="teamlead" value="{{ old('teamlead') }}" placeholder="Optional owner or supervisor">
                                @error('teamlead')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="tags" class="form-label">Tags</label>
                                <input id="tags" type="text" class="form-control" name="tags" value="{{ old('tags') }}" placeholder="vip, urgent, billing">
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
                        <p class="text-muted small mb-0">Capture the case details and any early response or internal note.</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="content" class="form-label">Case description</label>
                                <textarea id="crm-content-editor" name="content" class="form-control" rows="10" required>{{ old('content') }}</textarea>
                                @error('content')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label for="response" class="form-label">Initial response</label>
                                <textarea id="crm-response-editor" name="response" class="form-control" rows="10" placeholder="Optional response or next action">{{ old('response') }}</textarea>
                                @error('response')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Internal notes</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Optional internal note for the team">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid d-md-flex justify-content-md-end gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-4">Save Case</button>
                </div>
            </form>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm sticky-xl-top crm-sidebar">
                <div class="card-body">
                    <h3 class="h5 mb-3">Quick setup</h3>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Training</span>
                            <span class="fw-semibold text-end">{{ $trainingName }}</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">CRM enabled</span>
                            <span class="badge bg-success-subtle text-success">Yes</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Required</span>
                            <span class="fw-semibold text-end">Customer, issue, case content</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Optional</span>
                            <span class="fw-semibold text-end">Subject, category, follow-up, tags</span>
                        </div>
                    </div>
                    <div class="alert alert-light border mt-4 mb-0 small">
                        Use this page to log a case quickly. Response and internal notes can be added now or updated later from the edit screen.
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
        const previousType = @json(old('type'));
        const previousIssue = @json(old('issues'));
        const previousStatus = @json(old('status'));

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
                @if(checkRoleHas(['Admin', 'Facilitator']))
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
                @if(checkRoleHas(['Admin', 'Facilitator']))
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
            } else if (type === 'Request' && !selectedStatus) {
                statusSelect.val('Pending');
            }
        }

        $('#type').on('change', function () {
            populateCaseOptions($(this).val());
        });

        populateCaseOptions(previousType, previousIssue, previousStatus);

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
