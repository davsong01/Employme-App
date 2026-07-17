@extends('dashboard.admin.index')
@section('title', isset($coupon) ? 'Edit Coupon' : 'Add Coupon')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">{{ isset($coupon) ? 'Edit Coupon' : 'Add new Coupon' }}</h4>
                    </div>

                    <form action="{{ isset($coupon) ? route('coupon.update', $coupon->id) : route('coupon.store') }}" method="POST" class="pb-2">
                        @csrf
                        @if(isset($coupon))
                            @method('PATCH')
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="training_type">Select Type *</label>
                                    <select name="training_type" id="training_type" class="form-control" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="program" {{ old('training_type', $coupon->program_id ?? null ? 'program' : '') === 'program' ? 'selected' : '' }}>Program</option>
                                        @if(checkRoleHas(['Admin']))
                                        <option value="group" {{ old('training_type', $coupon->group_id ?? null ? 'group' : '') === 'group' ? 'selected' : '' }}>Package</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            {{-- Program select --}}
                            <div class="col-md-12 {{ old('training_type', $coupon->program_id ?? null ? 'program' : '') === 'program' ? '' : 'd-none' }}" id="program_select_wrapper">
                                <div class="mb-3">
                                    <label for="program_id">Select Training *</label>
                                    <select name="program_ids[]" id="program_id" class="select2 form-control" multiple="multiple" style="height: 30px; width: 100%;">
                                        <option value="all" {{ in_array('all', old('program_ids', [])) ? 'selected' : '' }}>All</option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->id }}"
                                                {{ in_array($program->id, old('program_ids', isset($coupon) && $coupon->program_id ? [$coupon->program_id] : [])) ? 'selected' : '' }}>
                                                {{ $program->p_name }} | <strong>{{ currency() . number_format($program->p_amount) }}</strong>
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Group select --}}
                            <div class="col-md-12 {{ old('training_type', $coupon->group_id ?? null ? 'group' : '') === 'group' ? '' : 'd-none' }}" id="group_select_wrapper">
                                <div class="mb-3">
                                    <label for="group_id">Select Package *</label>
                                    <select name="group_ids[]" id="group_id" class="select2 form-control" multiple="multiple" style="height: 30px; width: 100%;">
                                        <option value="all" {{ in_array('all', old('group_ids', [])) ? 'selected' : '' }}>All</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}"
                                                {{ in_array($group->id, old('group_ids', isset($coupon) && $coupon->group_id ? [$coupon->group_id] : [])) ? 'selected' : '' }}>
                                                {{ $group->p_name }} | <strong>{{ currency() . number_format($group->p_amount) }}</strong>
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Coupon details --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="code">Coupon Code</label>
                                    <input id="code" type="text" class="form-control" name="code"
                                           value="{{ old('code', $coupon->code ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type">Select Coupon Type *</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount">Coupon Amount</label>
                                    <input id="amount" type="number" class="form-control" name="amount"
                                           value="{{ old('amount', $coupon->amount ?? '') }}" required>
                                </div>
                            </div>
                        </div>

                        <input type="submit" value="{{ isset($coupon) ? 'Update' : 'Submit' }}" class="btn btn-primary" style="width:100%">
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script>
    jQuery(function ($) {
        const $trainingType   = $('#training_type');
        const $programWrapper = $('#program_select_wrapper');
        const $groupWrapper   = $('#group_select_wrapper');
        const $programSelect  = $('#program_id');
        const $groupSelect    = $('#group_id');

        function toggleField($wrapper, $select, show) {
            if (show) {
                $wrapper.removeClass('d-none');
                $select.prop('required', true);
            } else {
                $wrapper.addClass('d-none');
                $select.prop('required', false).val(null).trigger('change');
            }
        }

        function handleTrainingTypeChange() {
            const type = $trainingType.val();
            toggleField($programWrapper, $programSelect, type === 'program');
            toggleField($groupWrapper, $groupSelect, type === 'group');
        }

        $trainingType.on('change', handleTrainingTypeChange);
        handleTrainingTypeChange();
    });
</script>
@endsection
