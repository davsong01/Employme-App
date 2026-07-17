<?php 
    use App\Models\Program;
    use Illuminate\Support\Facades\Storage;
    $program = $program ?? new Program();
    $isCreate = $isCreate ?? false;
    $certificateDesignerIndexUrl = \Illuminate\Support\Facades\Route::has('certificates.manage.templates.index')
        ? route('certificates.manage.templates.index')
        : url('/admin/certificates/manage/templates');
    $c_settings = $certificateSettings ?? ($program->auto_certificate_settings ?? []);
    $autoCertificateStatus = old('auto_certificate_status', data_get($c_settings, 'auto_certificate_status', 'no'));
    $useExistingSettings = old('use_existing_settings', $useExistingSettings ?? (data_get($c_settings, 'inherited_from') ? 'yes' : 'no'));
    $inheritedProgramId = old('existing_program_id', $inheritedProgramId ?? data_get($c_settings, 'inherited_from'));
    $legacyCertificateSettings = $legacyCertificateSettings ?? (empty($program->certificate_template_id) && (!empty(data_get($c_settings, 'auto_certificate_template')) || !empty(data_get($c_settings, 'settings')) || !empty(data_get($c_settings, 'inherited_from'))));
    $formAction = $isCreate ? route('programs.store') : route('programs.update', $program->id);
    $formMethodField = $isCreate ? '' : method_field('PATCH');
    $submitLabel = $isCreate ? 'Submit' : 'Update';
    $pageTitle = $isCreate ? 'Trainings' : $program->p_name;
    $cardTitle = $isCreate ? 'Add new Program' : 'Update ' . $program->p_name;
?>
@section('css')
    <style>
        #previewModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            max-width: 90%;
            max-height: 90%;
            overflow: auto;
        }

        #certificatePreviewImage {
            width: 100%;
            height: auto;
        }

        .holder{
            padding: 0px 10px;
            margin-bottom:20px;
            border-radius: 10px;
            margin-top: 10px;
            background: antiquewhite; 
        }
        .field{
            border: 1px solid #ddd; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 5px; background-color: #f9f9f9;
        }
    </style>
@endsection
@extends('dashboard.admin.index')
@section('title', $pageTitle )
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">{{ $cardTitle }}</h4>
                    </div> 
                    <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {!! $formMethodField !!}
                        {{ csrf_field() }}
                        <fieldset class="field">
                            <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Core Settings</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Training Title *</label>
                                        <input type="text" name="p_name" value="{{ old('p_name') ?? $program->p_name}}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Training Hashtag *</label>
                                        <input type="text" name="p_abbr" value="{{ old('p_abbr') ?? $program->p_abbr }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Date *</label>
                                        <input type="date" name="p_start" value="{{ old('p_start') ?? $program->p_start }}" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Date *</label>
                                        <input type="date" name="p_end" value="{{ old('p_end') ??  $program->p_end }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Off Season Program?</label>
                                        <select name="off_season" class="form-control" id="off_season" required>
                                            <option value="1" {{ $program->off_season == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $program->off_season == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Closed Group Training?</label>
                                        <select name="is_closed" class="form-control" id="is_closed" required>
                                            <option value="yes" {{ $program->is_closed == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->is_closed == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Show Catalogue Popup</label>
                                        <select name="show_catalogue_popup" class="form-control" id="show_catalogue_popup" required>
                                            <option value="yes" {{ $program->show_catalogue_popup == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->show_catalogue_popup == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ !empty($program->image) ? 'Replace Program Banner' : 'Upload Program Banner' }}</label> <br>
                                        @if(!empty($program->image))
                                            <img src="{{ url('/').'/'.$program->image }}" alt="banner" style="width: 70px;padding-bottom: 10px;">  
                                        @endif
                                        <input type="file" name="image" value="{{ old('image') ??  $program->image }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Flexible timing?</label>
                                        <select name="allow_preferred_timing" class="form-control" id="allow_preferred_timing" required>
                                            <option value="no" {{ $program->allow_preferred_timing == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ $program->allow_preferred_timing == 'yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        @if($program->booking_form)
                                        <label>Replace Booking form</label>
                                        <i data-bs-toggle="tooltip" title="{{$program->booking_form }}" class="fa fa-paperclip" style="width: 70px;padding-bottom: 10px;"></i>
                                        @else
                                        <label>Upload Booking form</label>
                                        @endif
                                        <input type="file" name="booking_form" value="{{ old('booking_form') }}" placeholder="{{ $program->booking_form }}" class="form-control">
                                    </div>
                                    <div><small style="color:red">{{ $errors->first('booking_form')}}</small></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Does Program have pre class tests?</label>
                                        <select name="hasmock" class="form-control" id="hasmock" required>
                                            <option value="1" {{ $program->hasmock == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $program->hasmock == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                        <small style="color:red">{{ $errors->first('p_end')}}</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" id="status" required>
                                            <option value="1" {{ $program->status == 1 ? 'selected' : '' }}>Published</option>
                                            <option value="0" {{ $program->status == 0 ? 'selected' : '' }}>Draft</option>
                                        </select>
                                        <small style="color:red">{{ $errors->first('status')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Program Status <small>(Participants will not be able to access program)</small></label>
                                        <select name="program_lock" class="form-control" id="program_lock" required>
                                            <option value="1" {{ $program->program_lock == 1 ? 'selected' : '' }}>Locked</option>
                                            <option value="0" {{ $program->program_lock == 0 ? 'selected' : '' }}>Unlocked</option>
                                        </select>
                                        <small style="color:red">{{ $errors->first('program_lock')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label style="color:red">Login Without Password</label>
                                        <select name="login_without_password" class="form-control" id="login_without_password" required>
                                            <option value="1" {{ $program->login_without_password == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $program->login_without_password == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                        <small style="color:red">{{ $errors->first('login_without_password')}}</small>
                                    </div>
                                </div>
                                
                            </div>
                        </fieldset>
                        
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Payment settings</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Training Fee *</label>
                                        <input type="number" name="p_amount" value="{{ old('p_amount') ??  $program->p_amount}}" min="0"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment?</label>
                                        <select name="haspartpayment" class="form-control" id="hasmock" required>
                                            <option value="1" {{ $program->haspartpayment == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ $program->haspartpayment == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                        <small style="color:red">{{ $errors->first('haspartpayment')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Early Bird Fee *</label>
                                        <input type="number" name="e_amount" value="{{ old('e_amount') ??  $program->e_amount}}" min="0"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Early Bird Status</label>
                                        <select name="early_bird_status" class="form-control" id="early_bird_status" required>
                                            <option value="1" {{ $program->early_bird_status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $program->early_bird_status == 0 ? 'selected' : '' }}>In Active</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Flexible payment?</label>
                                        <select name="allow_flexible_payment" class="form-control" id="allow_flexible_payment" required>
                                            <option value="no" {{ $program->allow_flexible_payment == 'no' ? 'selected' : '' }}>No</option>
                                            <option value="yes" {{ $program->allow_flexible_payment == 'yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="currencies">Currencies to Display *</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($currencies as $currency)
                                                @php
                                                    // Extract currency IDs from $program->currencies array of objects
                                                    $existingCurrencyIds = collect(old('currencies') ?? collect($program->currencies)->pluck('id')->toArray());
                                                    $isChecked = $existingCurrencyIds->contains($currency->id);

                                                    $value = old(
                                                        'currency_values.' . $currency->id,
                                                        collect($program->currencies)->firstWhere('id', $currency->id)['amount'] ?? ''
                                                    );
                                                    
                                                @endphp
                                                <div class="d-flex align-items-center border rounded px-3 py-2" style="min-width: 250px;">
                                                    <input 
                                                        type="checkbox"
                                                        class="form-check-input me-2"
                                                        id="currency_{{ $currency->id }}"
                                                        name="currencies[]"
                                                        value="{{ $currency->id }}"
                                                        {{ $isChecked ? 'checked' : '' }}
                                                        onchange="toggleCurrencyInput(this)">
                                                    
                                                    <label class="form-check-label me-2" for="currency_{{ $currency->id }}">
                                                        {{ $currency->name }}
                                                    </label>

                                                    <input 
                                                        type="number" 
                                                        step="0.000001" 
                                                        name="currency_values[{{ $currency->id }}]" 
                                                        class="form-control form-control-sm"
                                                        style="width: 100px;"
                                                        placeholder="Rate"
                                                        value="{{ $value }}"
                                                        {{ $isChecked ? '' : 'disabled' }}>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <script>
                                        function toggleCurrencyInput(checkbox) {
                                            const input = checkbox.closest('div').querySelector('input[type="number"]');
                                            if (checkbox.checked) {
                                                input.removeAttribute('disabled');
                                            } else {
                                                input.setAttribute('disabled', true);
                                                input.value = ''; 
                                            }
                                        }

                                        document.addEventListener('DOMContentLoaded', function () {
                                            document.querySelectorAll('input[type="checkbox"][name="currencies[]"]').forEach(toggleCurrencyInput);
                                        });
                                    </script>

                                </div>
                            </div>
                            
                            <div class="row" style="margin-top: 20px;">  
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Show Mode (2 payment modes only)</label>
                                    <select name="show_modes" class="form-control" id="show_modes" required>
                                        <option value="no" {{ $program->show_modes == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ $program->show_modes == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_mode" style="display:{{ $program->show_modes == 'yes' ? 'block':'none' }}">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-mode"><i class="fa fa-plus"></i> Add Mode</button>
                                </div>
                            </div>
                            <section id="mode-holder" class="holder pt-2" style="display: none">
                                <div class="row" id="mode-0">
                                </div>
                                <?php $mode_counter = 1?>
                                @if($program->show_modes == 'yes' && isset($program->modes) && !empty($program->modes))
                                    @foreach(json_decode($program->modes, true) as $key=>$value)
                                        <?php $counter = $mode_counter++ ?>
                                        <div class="row" id="oldmode-{{ $counter }}">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="mode_name">Mode Name</label>
                                                
                                                    <input type="select" class="form-control" value="{{ $key }}"
                                                        name="mode_name[]" required>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="mode_amount">Mode Amount</label>
                                                    <input type="text" class="form-control" id="unit" value="{{ $value }}"name="mode_amount[]" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                                                    <button class="btn btn-danger removeold-mode" id="removeold-mode-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </section>
                        </fieldset>
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Payment Restriction Settings</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for materials?</label>
                                        <select name="allow_payment_restrictions_for_materials" class="form-control" id="allow_payment_restrictions_for_materials" required>
                                            <option value="yes" {{ $program->allow_payment_restrictions_for_materials == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->allow_payment_restrictions_for_materials == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Pre class tests?</label>
                                        <select name="allow_payment_restrictions_for_pre_class_tests" class="form-control" id="allow_payment_restrictions_for_pre_class_tests" required>
                                            <option value="yes" {{ $program->allow_payment_restrictions_for_pre_class_tests == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->allow_payment_restrictions_for_pre_class_tests == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Post class tests?</label>
                                        <select name="allow_payment_restrictions_for_post_class_tests" class="form-control" id="allow_payment_restrictions_for_post_class_tests" required>
                                            <option value="yes" {{ $program->allow_payment_restrictions_for_post_class_tests == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->allow_payment_restrictions_for_post_class_tests == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Completed Tests?</label>
                                        <select name="allow_payment_restrictions_for_completed_tests" class="form-control" id="allow_payment_restrictions_for_completed_tests" required>
                                            <option value="yes" {{ $program->allow_payment_restrictions_for_completed_tests == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->allow_payment_restrictions_for_completed_tests == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Results?</label>
                                        <select name="allow_payment_restrictions_for_results" class="form-control" id="allow_payment_restrictions_for_results" required>
                                            <option value="yes" {{ $program->allow_payment_restrictions_for_results == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->allow_payment_restrictions_for_results == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enable Part Payment Restrictions for Certificates?</label>
                                        <select name="allow_payment_restrictions_for_certificates" class="form-control" id="allow_payment_restrictions_for_certificates" required>
                                            <option value="yes" {{ $program->allow_payment_restrictions_for_certificates == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->allow_payment_restrictions_for_certificates == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="field">
                            <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Certificate settings</legend>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Only Certified Should See Certificate</label>
                                        <select name="only_certified_should_see_certificate" class="form-control" id="only_certified_should_see_certificate" required>
                                            <option value="">Select...</option>
                                            <option value="yes" {{ $program->only_certified_should_see_certificate == 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="no" {{ $program->only_certified_should_see_certificate == 'no' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>
        
                            </legend>
                        </fieldset>
                        <fieldset class="field">
                            <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">
                                AI Settings
                            </legend>

                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <div class="form-group">
                                        <label>AI Status</label>
                                        <select name="ai_settings[status]" class="form-control" id="ai_status" required>
                                            <option value="0" {{ ($program->ai_settings['status'] ?? null) == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ ($program->ai_settings['status'] ?? null) == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>AI Page Use Cases:</label>
                                        <select name="ai_settings[use_cases][]" class="select2 form-control" multiple>
                                            @foreach(['materials'] as $case)
                                                <option value="{{ $case }}" 
                                                    {{ in_array($case, $program->ai_settings['use_cases'] ?? []) ? 'selected' : '' }}>
                                                    {{ ucfirst($case) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset style="" class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Sub Trainings</legend>
                            <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px">
                                    <label>Sub Trainings?</label>
                                    <select name="show_sub" class="form-control" id="show_sub" required>
                                        <option value="yes" {{ $program->show_sub == 'yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="no" {{ is_null($program->show_sub) || $program->show_sub == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_sub" style="display:{{ isset($program->subPrograms) && $program->subPrograms->count() > 0 ? 'block':'none' }}">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-sub"><i class="fa fa-plus"></i> Add Sub Training</button>
                                </div>
                            </div>
                        </section>
                        <section id="sub-holder" class="holder pt-2" style="display: none">
                            <div class="row pt-2" id="sub-0">
                            </div>
                            <?php $sub_counter = 1?>
                            @if(isset($program->subPrograms) && !empty($program->subPrograms))
                            @foreach($program->subPrograms as $sub)
                            <?php $counter = $sub_counter++ ?>
                                    <input type="hidden" name="sub_program_id[]" value="{{ $sub->id }}">
                                    <div class="row p-2" id="oldsub-{{ $counter }}">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="sub_name" style="padding-top:10px">Sub Program Name</label>
                                                <input type="text" class="form-control" value="{{ $sub->p_name }}"
                                                    name="sub_name[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="sub_amount" style="padding-top:10px">Sub Program Amount</label>
                                                <input type="text" class="form-control" id="amount" value="{{ $sub->p_amount }}" name="sub_amount[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mode_name" style="padding-top:10px">Status</label>
                                                <select name="sub_status[]" class="form-control" id="sub_status" required>
                                                    <option value="1" {{  $sub->status == 1 ? 'selected' : '' }}>Published</option>
                                                    <option value="0" {{  $sub->status == 0 ? 'selected' : '' }}>Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="mark" style="color:antiquewhite; padding-top:10px">sdsdsddsdssd</label>
                                                <button class="btn btn-danger removeold-sub" data-program-id="{{ $sub->id }}" id="removeold-sub-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                                <a target="_blank" class="btn btn-info" id="" type="button" style="min-width: unset;" href="{{route('programs.edit', $sub->id)}}"> <i class="fa fa-edit"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </section>
                        </fieldset>
                        @include('dashboard.admin.programs.partials.certificate-settings')
                        <fieldset class="field">
                        <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Others</legend>
                            <section>
                            <div class="row">                                   
                                <div class="col-md-6" style="margin-bottom:5px;">
                                    <label>Show Location</label>
                                    <select name="show_locations" class="form-control" id="show_locations" required>
                                        <option value="no" {{ $program->show_locations == 'no' ? 'selected' : '' }}>No</option>
                                        <option value="yes" {{ $program->show_locations == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                                <div class="col-md-2" id="add_location" style="display:{{ $program->show_locations == 'yes' ? 'block':'none' }}">
                                    <label style="color:white">S</label>
                                    <button class="btn btn-sm btn-info form-control" style="padding: 8px;" type="button" id="add-course"><i class="fa fa-plus"></i> Add Location</button>
                                </div>
                            </div>
                        </section>
                        {{-- {{dd($program->locations, $program->show_locations)}} --}}
                        <section id="course-holder" class="holder pt-2">
                            <div class="row" id="course-0">
                            </div>
                            <?php $location_counter = 1 ?>
                            @if($program->show_locations == 'yes' && isset($program->locations) && !empty($program->locations))
                                @foreach(json_decode($program->locations, true) as $key=>$value)
                                <?php $counter = $location_counter++ ?>
                                <div class="row" id="oldcourse-{{ $counter }}">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="location_name">Location Name</label>
                                            <input type="text" class="form-control" value="{{ $key }}"
                                                name="location_name[]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="location_address">Location Address</label>
                                            <input type="text" class="form-control" id="unit" value="{{ $value }}"name="location_address[]" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                                            <button class="btn btn-danger remove-old-course" id="oldcourse-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </section>
                        </fieldset>
                        
                        <div class="col-12">
                            <input type="submit" name="submit" value="{{ $submitLabel }}" class="btn btn-primary" style="width:100%">
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <div id="previewModal" style="display:none;">
        <img id="certificatePreviewImage" src="" alt="Certificate Preview">
    </div>
</div>
<script>
    $(document).ready(function() {
        function toggleCertificateFields() {
            var useExisting = $('#use_existing_settings').val();
            
            if (useExisting === 'yes') {
                $('#program_select_wrapper').fadeIn();
            } else {
                $('#program_select_wrapper').hide();
            }
        }

        // Trigger on change
        $('#use_existing_settings').on('change', function() {
            toggleCertificateFields();
        });

        // Run on page load (in case of validation errors returning value)
        toggleCertificateFields();
        
        $('#addRowButton').on('click', function(e) {
            var lastChild = $("#certificateRows").children().last();
            var lastId = $(lastChild).attr('id');

            var id = 1;
            if (lastId) {
                lastId = lastId.split('-'); 
                id = parseInt(lastId[1]) + 1; 
            }

            var newRow = `
                <div class="row added-row" style="border-top: black solid 1px;margin-bottom: 6px;padding-top: 15px;" id="certificate-` + id + `">
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text Type</label>
                            <select name="text_type[]" class="form-control" id="text_type" required>
                                <option value="">Select...</option>
                                <option value="certificate_number">Certificate Number</option>
                                <option value="name">Name</option>
                                <option value="email">Email</option>
                                <option value="staffID">Staff ID</option>
                                <option value="date_issued">Date Issued</option>
                                <option value="qr_code">QR Verification Code</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Font Type Face</label>
                            <select name="text_type_face[]" class="form-control" id="text_type_face">
                                @foreach(certificateFontType() as $key=>$value)
                                <option value="{{ $key }}" {{ (isset($setting['text_type_face']) && $setting['text_type_face'] == $key) ? 'selected' : ''}}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text font size, e.g 150</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_name_font_size[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text font weight e.g 300</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_name_font_weight[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text Top offset. e.g 300</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_top_offset[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text Left offset. e.g 100</label>
                            <input type="number" min="0" class="form-control" name="auto_certificate_left_offset[]">
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Text color</label>
                            <input type="color" class="form-control" name="auto_certificate_color[]">
                        </div>
                    </div>
                    <div class="col-md-4 mt-4">
                        <button type="button" class="btn btn-danger btn-sm removeRowButton">
                            <i class="fa fa-minus"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            
            $('#certificateRows').append(newRow);
        });

        // Remove row
        $(document).on('click', '.removeRowButton', function(e) {
            e.preventDefault();
            $(this).closest('.added-row').remove();
        });

        // Preview button handling
        $('#previewButton').on('click', function(e) {
            e.preventDefault();
            $('#loadingSpinner').show();
            
            var formData = new FormData();

            // 1. Handle the Template (File vs. Existing Hidden Path)
            var fileInput = $('#auto_certificate_template')[0].files[0];
            
            if (fileInput) {
                // User picked a new file
                formData.append('auto_certificate_template', fileInput);
            } else {
                // No new file, try to grab the hidden value
                var existingPath = $('#existing_auto_certificate_template').val();
                if (existingPath) {
                    formData.append('existing_template_path', existingPath);
                }
            }

            // 2. Handle Inheritance Settings
            formData.append('use_existing_settings', $('#use_existing_settings').val());
            formData.append('existing_program_id', $('select[name="existing_program_id"]').val());

            // 3. Loop through all dynamic arrays
            $('select[name="text_type[]"]').each(function() { formData.append('text_type[]', $(this).val()); });
            $('select[name="text_type_face[]"]').each(function() { formData.append('text_type_face[]', $(this).val()); });
            $('input[name="auto_certificate_name_font_size[]"]').each(function() { formData.append('auto_certificate_name_font_size[]', $(this).val()); });
            $('input[name="auto_certificate_name_font_weight[]"]').each(function() { formData.append('auto_certificate_name_font_weight[]', $(this).val()); });
            $('input[name="auto_certificate_top_offset[]"]').each(function() { formData.append('auto_certificate_top_offset[]', $(this).val()); });
            $('input[name="auto_certificate_left_offset[]"]').each(function() { formData.append('auto_certificate_left_offset[]', $(this).val()); });
            $('input[name="auto_certificate_color[]"]').each(function() { formData.append('auto_certificate_color[]', $(this).val()); });
            
            $.ajax({
                url: '/admin/generate-certificate-preview/{{$program->id}}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loadingSpinner').hide();
                    if (response.preview_image_path) {
                        // Force refresh image by appending timestamp
                        var timestamp = new Date().getTime();
                        $('#certificatePreviewImage').attr('src', response.preview_image_path + '?t=' + timestamp);
                        $('#previewModal').show(); 
                    } else {
                        alert('Error: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingSpinner').hide();
                    alert('Server Error: ' + error);
                }
            });
        });
    });

    $(document).on('click', function(event) {
        if ($(event.target).closest('#previewModal').length === 0 && $('#previewModal').is(':visible')) {
            $('#previewModal').fadeOut();
        }
    });

    $("#show_locations").on('change', function () {
        if($("#show_locations").val() == 'yes'){
            $("#add_location").show();
            $("#course-holder").show();
            
        }else{
            $("#add_location").hide();
            $("#course-holder").hide();

        }
    });
    
    $("#show_modes").on('change', function () {
        if($("#show_modes").val() == 'yes'){
            $("#add_mode").show();
            $("#mode-holder").show();
            
        }else{
            $("#add_mode").hide();
            $("#mode-holder").hide();
        }
    });

    $("#show_sub").on('change', function () {
        if($("#show_sub").val() == 'yes'){
            $("#add_sub").show();
            $("#sub-holder").show();
            
        }else{
            $("#add_sub").hide();
            $("#sub-holder").hide();
        }
    });

    $("#add-course").on('click', function () {
        //get last ID
        var lastChild = $("#course-holder").children().last();
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        var child = `<div class="row" id="course-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_name">Location Name</label>
                        <input type="text" class="form-control" value="{{ old('location_name') }}"
                            name="location_name[]" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_address">Location Address</label>
                        <input type="text" class="form-control" id="unit" value="{{ old('location_address')}}"name="location_address[]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-course" id="remove-course-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#course-holder").append(child);      
    });

    $("#course-holder").on('click','.remove-course', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#course-"+id).remove();
    });

    $("#course-holder").on('click','.remove-old-course', function(e) {
        var removeId = $(e.target).attr('id');
        $("#"+removeId).remove();
    });

    $("#certificate-holder").on('click', '.remove-old-certificate', function() {
        // Get the ID of the clicked element
        $(this).closest('.row').remove();
        var removeId = $(this).attr('id');  
    });


    $("#mode-holder").on('click','.removeold-mode', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#oldmode-"+id).remove();
    });

    $("#sub-holder").on('click','.removeold-sub', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var program_id =  $(e.target).attr('data-program-id');
        var id = removeId[2];
        // validate and remove program from database
        $.get("/admin-remove-sub-program/"+program_id,function(data, status){
            if(data.status == 'success'){
                $("#oldsub-"+id).remove();
            }else{
                alert(data.message);
            }
        });
    });

    $("#add-mode").on('click', function () {
        //get last ID
        var lastChild = $("#mode-holder").children().last();
        var countChildren = $("#mode-holder").children().length;
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        if(countChildren > 2){
            return alert('You can only add 2 payment modes!');
        }

        var child = `<div class="row" id="mode-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="mode_name">Mode Name</label>
                        <select name="mode_name[]" class="form-control" id="mode_name" required>
                            <option value="" selected>Select mode</option>
                            <option value="Online">Online</option>
                            <option value="Offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="location_address">Mode Amount</label>
                        <input type="text" class="form-control" id="unit" value="{{ old('mode_amount')}}"name="mode_amount[]" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-mode" id="remove-mode-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#mode-holder").append(child);      
        });

    $("#mode-holder").on('click','.remove-mode', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
        $("#mode-"+id).remove();
    });

    $("#add-sub").on('click', function () {
        //get last ID
        var lastChild = $("#sub-holder").children().last();
        var countChildren = $("#sub-holder").children().length;
        var lastId = $(lastChild).attr('id').split('-');

        var id = lastId[1] + 1;
        
        var child = `<div class="row" id="sub-`+id+`">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="sub_name" style="padding-top:10px">Sub Program Name</label>
                        <input type="text" class="form-control" id="sub_name" value="{{ old('sub_name')}}"name="sub_name[]" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sub_amount" style="padding-top:10px" >Sub Program Amount</label>
                        <input type="text" class="form-control" id="sub_amount" value="{{ old('sub_amount')}}"name="sub_amount[]" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mode_name" style="padding-top:10px">Status</label>
                        <select name="sub_status[]" class="form-control" id="sub_status" required>
                            <option value="1" selected>Published</option>
                            <option value="0">Draft</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="mark" style="color:antiquewhite;padding-top:10px">sdsdsddsdssd</label>
                        <button class="btn btn-danger remove-sub" id="remove-sub-`+id+`" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                    </div>
                </div>
            </div>`
        $("#sub-holder").append(child);      
        });

    $("#sub-holder").on('click','.remove-sub', function(e) {
        var removeId = $(e.target).attr('id').split('-');
        var id = removeId[2];
       
        $("#sub-"+id).remove();
    });

</script>
@endsection
