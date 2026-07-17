@extends('dashboard.admin.index')
@section('title', 'Grouped Trainings')
@section('css')
<style>
    .group-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
    }

    .group-table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .06em;
        color: #475569;
    }

    .group-table tbody tr:hover {
        background: #f8fbff;
    }

    .group-banner {
        width: 85px;
        border-radius: 8px;
        object-fit: cover;
    }

    .group-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .flashing-red {
        color: red;
        font-weight: bold;
        animation: flashRed 1s infinite;
    }

    @keyframes flashRed {
        0%   { opacity: 1; }
        50%  { opacity: 0; }
        100% { opacity: 1; }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero group-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Grouped Trainings</h1>
                            <p class="text-muted mb-0">Bundle trainings into packages, manage child programs, and update pricing from one screen.</p>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                            <i class="fa fa-plus me-1"></i> Add Group
                        </button>
                    </div>
                    @include('layouts.partials.alerts')
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm group-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0 group-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Banner</th>
                            <th>Group Name / Members</th>
                            <th>Fee</th>
                            <th>Dates</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                
                    <tbody>
                        @foreach ($groups as $group)
                            <tr>
                                <td>{{ paginationIndex($groups, $loop) }}</td>
                
                                <td data-label="Banner">
                                    @if ($group->image)
                                        <img src="{{ asset($group->image) }}" alt="banner" class="group-banner">
                                    @endif
                                </td>
                
                                <td>
                                    <div class="mb-1">
                                        <strong class="text-dark h6">{{ $group->p_name }}</strong>
                                    </div>
                                
                                    @if ($group->programs->isNotEmpty())
                                    <ul class="list-unstyled small text-dark mb-2">
                                        @foreach ($group->programs as $index => $child)
                                            @php
                                                $isActive = in_array($child->id, $allActivePrograms->pluck('id')->toArray());
                                            @endphp
                                    
                                            <li>
                                                {{ $index + 1 }}.
                                                <a href="{{ route('programs.edit', $child->id) }}" target="_blank" class="">
                                                    @if ($isActive)
                                                        <strong>{{ $child->p_name }}</strong>
                                                    @else
                                                        <del class="text-danger">{{ $child->p_name }}</del>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    
                                
                                        @if ($group->status == 1)
                                            <div class="mt-2">
                                                <a href="{{ route('show.packages', $group->slug) }}" target="_blank" class="d-inline-block text-primary">
                                                    <i class="fa fa-eye me-1"></i> Preview Package
                                                </a>
                                            </div>
                                
                                            @if ($group->early_bird_status == 1)
                                                <div class="mt-1">
                                                    <a href="{{ route('earlybird.groups', $group->slug)}}" target="_blank" class="d-inline-block text-success">
                                                        <i class="fa fa-eye me-1"></i> Preview Early Bird Package
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    @else
                                        <span class="text-muted small">(No programs in this group)</span>
                                    @endif
                                </td>
                                
                
                                <td>
                                    <strong>Normal Fee:</strong> {{ $currency_symbol . number_format($group->p_amount) }}<br>
                                    <strong>Early Bird:</strong> {{ $currency_symbol . number_format($group->e_amount) }}<br>
                                </td>
                
                                <td>
                                    <strong>Start:</strong> {{ $group->p_start }}<br>
                                    <strong>End:</strong> {{ $group->p_end }}
                                </td>
                
                                <td>
                                    {{ $group->participants_count ?? 0 }}
                                </td>
                
                                <td>
                                    @if ($group->status)
                                        <button class="btn btn-dark btn-xs">Active</button>
                                    @else
                                        <button class="btn btn-secondary btn-xs">Inactive</button>
                                    @endif
                                </td>
                
                                <td data-label="Actions" style="vertical-align: top;">
                                    <div class="group-actions">
                                        <a data-bs-toggle="tooltip" data-placement="top" title="Import Participants"
                                            class="btn btn-dark btn-sm" style="background:#183153" href="{{ route('group.import', ['p_id'=> $group->id, 'program'=> $group->id, 'source'=>'group'])}}"><i class="fa fa-upload me-1"></i> Bulk Import
                                        </a>
                                        <button class="btn btn-info btn-sm" title="Edit Group"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editGroupModal{{ $group->id }}">
                                            <i class="fa fa-edit me-1"></i> Edit
                                        </button>
                                        <form class="d-inline" action="{{ route('groupedprogram.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Delete this group?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm" title="Trash Group">
                                                <i class="fa fa-trash me-1"></i> Delete
                                            </button>
                                        </form>
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
@foreach($groups as $group)
    {{-- ─────────────────────────────────────────────────────────────────────────────
    | Edit Group Modal – keyed by group ID
    |--------------------------------------------------------------------------- --}}
    <div class="modal fade" id="editGroupModal{{ $group->id }}" tabindex="-1"
        aria-labelledby="editGroupLabel{{ $group->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('groupedprogram.update', $group->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="modal-content">
                    {{-- ── Header ─────────────────────────────────────────────── --}}
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"
                            id="editGroupLabel{{ $group->id }}">
                            Edit Group – {{ $group->p_name }}
                        </h5>
                        <button type="button" class="close btn btn-danger"
                                data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    {{-- ── Body ───────────────────────────────────────────────── --}}
                    <div class="modal-body">
                        {{-- BASIC INFO --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label>Status *</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ $group->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ ! $group->status ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Early-Bird Status *</label>
                                <select name="early_bird_status" class="form-control">
                                    <option value="1" {{ $group->early_bird_status ? 'selected' : '' }}>On</option>
                                    <option value="0" {{ ! $group->early_bird_status ? 'selected' : '' }}>Off</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Group Abbreviation *</label>
                                <input type="text" name="p_abbr" class="form-control"
                                        value="{{ old('p_abbr', $group->p_abbr) }}" required>
                            </div>

                            <div class="col-md-12">
                                <label>Group Name *</label>
                                <input type="text" name="p_name" class="form-control"
                                        value="{{ old('p_name', $group->p_name) }}" required>
                            </div>
                            <div class="col-md-12">
                                <label for="image">Replace Banner (optional)</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                
                                @if ($group->image)
                                    <small class="d-block mt-2">Current Banner:</small>
                                    <img src="{{ asset($group->image) }}" alt="Banner" style="max-width: 180px; border: 1px solid #ddd;">
                                @endif
                            </div>
                        </div>
                        {{-- CHILD PROGRAMS --}}
                        <div class="mb-3">
                            {{-- {{dd($allActivePrograms->pluck('id')->toArray())}} --}}
                            <label>Child Programs *</label>
                            <select name="programs[]" class="form-control select2" multiple required>
                                @foreach ($allPrograms as $program)
                                    <option value="{{ $program->id }}"
                                        {{ in_array($program->id, $group->programs->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $program->p_name }}
                                        {{ in_array($program->id, $allActivePrograms->pluck('id')->toArray()) ? '(Active)' : '(Inactive)'}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PRICING --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label>Group Price (Default Currency) *</label>
                                <input  type="number" step="0.01" name="p_amount"
                                        class="form-control"
                                        value="{{ old('p_amount', $group->p_amount) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label>Enable Part Payment *</label>
                                <select name="haspartpayment" class="form-control" required>
                                    <option value="1" {{$group->haspartpayment ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{! $group->haspartpayment ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Early-Bird Price (Default Currency)</label>
                                <input  type="number" step="0.01" name="e_amount"
                                        class="form-control"
                                        value="{{ old('e_amount', $group->e_amount) }}">
                            </div>
                            <div class="col-md-6">
                                <label>Enable Part Payment</label>
                                <select name="haspartpayment" class="form-control">
                                    <option value="1" {{ $group->haspartpayment == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $group->haspartpayment == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        {{-- OPTIONAL DATES --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label>Start Date</label>
                                <input type="date" name="p_start" class="form-control"
                                        value="{{ old('p_start', $group->p_start) }}">
                            </div>
                            <div class="col-md-6">
                                <label>End Date</label>
                                <input type="date" name="p_end" class="form-control"
                                        value="{{ old('p_end', $group->p_end) }}">
                            </div>
                        </div>

                        {{-- CURRENCIES --}}
                        <div class="row">
                            <div class="col-md-12"><label>Currencies to Display *</label></div>

                            @foreach ($currencies as $currency)
                                @php
                                    $existing = collect($group->currencies)->firstWhere('id', $currency->id);
                                    $isChecked = !is_null($existing);
                                    $amount    = $existing['amount'] ?? '';
                                @endphp

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center border rounded px-3 py-2 h-100">
                                        <input  type="checkbox"
                                                class="form-check-input me-2"
                                                id="currency_edit_{{ $group->id }}_{{ $currency->id }}"
                                                name="currencies[]"
                                                value="{{ $currency->id }}"
                                                {{ $isChecked ? 'checked' : '' }}
                                                onchange="toggleCurrencyInput(this)">

                                        <label class="form-check-label me-2"
                                                for="currency_edit_{{ $group->id }}_{{ $currency->id }}">
                                                {{ $currency->name }}
                                        </label>

                                        <input  type="number" step="0.000001"
                                                name="currency_values[{{ $currency->id }}]"
                                                class="form-control form-control-sm ms-auto"
                                                style="width:100px"
                                                value="{{ $amount }}"
                                                {{ $isChecked ? '' : 'disabled' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Footer ─────────────────────────────────────────────── --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Group</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ---------- Currency toggle script (include once per page) ---------- --}}
    @once
    <script>
        function toggleCurrencyInput(checkbox) {
            const input = checkbox.closest('div')
                                    .querySelector('input[type="number"]');
            if (checkbox.checked) {
                input.removeAttribute('disabled');
            } else {
                input.setAttribute('disabled', true);
                input.value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', () =>
            document.querySelectorAll('input[name="currencies[]"]')
                    .forEach(toggleCurrencyInput)
        );
    </script>
    @endonce
@endforeach
<div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('groupedprogram.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGroupLabel">Add New Group</h5>
                    <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{-- BASIC DETAILS ------------------------------------------------ --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label>Status *</label>
                            <select name="status" class="form-control">
                                <option value="1" selected>Active</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Early-Bird Status *</label>
                            <select name="early_bird_status" class="form-control">
                                <option value="0" selected>Off</option>
                                <option value="1">On</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Group Abbreviation *</label>
                            <input type="text" name="p_abbr" class="form-control" required placeholder="e.g. DBGA">
                        </div>

                        <div class="col-md-12">
                            <label>Group Name *</label>
                            <input type="text" name="p_name" class="form-control" required placeholder="Enter Group Name">
                        </div>
                        <div class="col-md-12">
                            <label for="image">Group Banner</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>

                    </div>

                    {{-- MEMBERS (child programs) --------------------------------------- --}}
                    <div class="mb-3">
                        <label for="child_programs">Child Programs *</label>
                        <select name="programs[]" id="child_programs"
                                class="form-control select2" multiple required>
                            @foreach ($allActivePrograms as $program)
                                <option value="{{ $program->id }}">{{ $program->p_name }}({{$currency_symbol.number_format($program->p_amount)}})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PRICING -------------------------------------------------------- --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label>Group Price (Default Currency) *</label>
                            <input type="number" step="0.01" name="p_amount" class="form-control" required placeholder="e.g. 25000">
                        </div>
                        <div class="col-md-6">
                            <label>Enable Part Payment *</label>
                            <select name="haspartpayment" class="form-control" required>
                                <option value="1" selected>Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Early-Bird Price (Default Currency)</label>
                            <input type="number" step="0.01" name="e_amount"
                                   class="form-control" placeholder="e.g. 20000">
                        </div>
                    {{-- </div> --}}

                    {{-- OPTIONAL DATES -------------------------------------------------- --}}
                    {{-- <div class="row g-3 mb-4"> --}}
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <input type="date" name="p_start" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>End Date</label>
                            <input type="date" name="p_end" class="form-control">
                        </div>
                    </div>

                    {{-- CURRENCIES ------------------------------------------------------ --}}
                    <div class="row mt-2">
                        <hr>
                        <div class="col-md-12"><label>Currencies to Display *</label></div>

                        @foreach ($currencies as $currency)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center border rounded px-3 py-2 h-100">
                                    <input  type="checkbox"
                                            class="form-check-input me-2"
                                            id="currency_{{ $currency->id }}"
                                            name="currencies[]"
                                            value="{{ $currency->id }}"
                                            onchange="toggleCurrencyInput(this)">

                                    <label class="form-check-label me-2"
                                           for="currency_{{ $currency->id }}">
                                           {{ $currency->name }}
                                    </label>

                                    <input  type="number" step="0.000001"
                                            name="currency_values[{{ $currency->id }}]"
                                            class="form-control form-control-sm ms-auto"
                                            style="width:100px" placeholder="Rate" disabled>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <script>
                        function toggleCurrencyInput(checkbox) {
                            const input = checkbox.closest('div')
                                                  .querySelector('input[type="number"]');
                            if (checkbox.checked) {
                                input.removeAttribute('disabled');
                            } else {
                                input.setAttribute('disabled', true);
                                input.value = '';
                            }
                        }
                        document.addEventListener('DOMContentLoaded', () =>
                            document.querySelectorAll('input[name="currencies[]"]')
                                    .forEach(toggleCurrencyInput)
                        );
                    </script>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Group</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection
@section('extra-scripts')
    <script>
        $(document).ready(function() {
            $('.select3').select2({
                dropdownParent: $('body'),
                width: '100%'
            });

            $('.select4').select2({
                dropdownParent: $('body'),
                width: '100%',
                minimumResultsForSearch: 0
            });
        });
        
        document.querySelector('.dropdown-button').addEventListener('click', function() {
            const dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    </script>
@endsection
