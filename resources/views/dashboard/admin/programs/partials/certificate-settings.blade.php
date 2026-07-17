@php
    $certificateSettings = $certificateSettings ?? ($program->auto_certificate_settings ?? []);
    $isCreate = $isCreate ?? false;
    $autoCertificateStatus = old('auto_certificate_status', data_get($certificateSettings, 'auto_certificate_status', 'no'));
    $useExistingSettings = old('use_existing_settings', $useExistingSettings ?? (data_get($certificateSettings, 'inherited_from') ? 'yes' : 'no'));
    $inheritedProgramId = old('existing_program_id', $inheritedProgramId ?? data_get($certificateSettings, 'inherited_from'));
    $legacyCertificateSettings = $legacyCertificateSettings ?? (
        empty(data_get($program ?? null, 'certificate_template_id'))
        && (!empty(data_get($certificateSettings, 'auto_certificate_template'))
            || !empty(data_get($certificateSettings, 'settings'))
            || !empty(data_get($certificateSettings, 'inherited_from')))
    );
    $certificateDesignerIndexUrl = $certificateDesignerIndexUrl ?? route('certificates.manage.templates.index');
@endphp

<fieldset class="field">
    <legend style="font-size: 1.2rem; font-weight: bold; color: #333; padding: 0 10px; width: auto; border-bottom: none;">Auto Certificate settings</legend>
    <section>
        <div class="row">
            <div class="col-md-6" style="margin-bottom:5px">
                <div class="form-group">
                    <label>Enable Auto generate certificate</label>
                    <select name="auto_certificate_status" class="form-control" id="auto_certificate_status" required>
                        <option value="">Select...</option>
                        <option value="yes" {{ $autoCertificateStatus == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ $autoCertificateStatus == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
        </div>

        @if($legacyCertificateSettings)
            <div>
                <div class="row">
                    <div class="col-md-6" style="margin-bottom:5px">
                        <div class="form-group">
                            <label>Use Existing Certificate Settings</label>
                            <select name="use_existing_settings" class="form-control" id="use_existing_settings" required>
                                <option value="">Select...</option>
                                <option value="no" {{ $useExistingSettings == 'no' ? 'selected' : '' }}>No</option>
                                <option value="yes" {{ $useExistingSettings == 'yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" id="program_select_wrapper" style="display: none; margin-bottom:15px">
                        <div class="form-group">
                            <label style="color: blue; font-weight: bold;">Select Program to Inherit Certificate From</label>
                            <select name="existing_program_id" class="form-control select2">
                                <option value="">Select Program...</option>
                                @foreach($programs as $inheritProgram)
                                    <option value="{{ $inheritProgram->id }}" {{ (string) $inheritedProgramId === (string) $inheritProgram->id ? 'selected' : '' }}>{{ $inheritProgram->p_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="manual_settings_wrapper">
                <div class="row">
                    <div class="col-md-12" style="margin-bottom:15px">
                        <div class="form-group">
                            <label class="form-label" style="font-weight: bold;">
                                {{ !empty(data_get($certificateSettings, 'auto_certificate_template')) ? 'Replace Certificate Template' : 'Upload Certificate Template' }}
                            </label>

                            <div class="d-flex align-items-start">
                                <div style="flex-grow: 1;">
                                    <input type="file" name="auto_certificate_template" class="form-control" id="auto_certificate_template">

                                    @if(!empty(data_get($certificateSettings, 'auto_certificate_template')))
                                        <input type="hidden" name="existing_auto_certificate_template" id="existing_auto_certificate_template" value="{{ data_get($certificateSettings, 'auto_certificate_template') }}">
                                    @endif
                                </div>

                                @if(!empty(data_get($certificateSettings, 'auto_certificate_template')))
                                    <div class="ml-3">
                                        <div style="border: 1px solid #ddd; padding: 2px; border-radius: 4px; background: #f9f9f9;">
                                            <img src="{{ url('uploads/' . base64_encode(data_get($certificateSettings, 'auto_certificate_template'))) }}"
                                                alt="Current Template"
                                                style="width: 100px; height: 70px; object-fit: cover; cursor: pointer;"
                                                onclick="window.open(this.src)">

                                            <input type="hidden" name="existing_auto_certificate_template"
                                                id="existing_auto_certificate_template"
                                                value="{{ data_get($certificateSettings, 'auto_certificate_template') }}">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty(data_get($certificateSettings, 'settings')))
                    @php $certificate_counter = 1; @endphp
                    <section id="certificate-holder" class="pt-2">
                        @foreach(data_get($certificateSettings, 'settings') as $setting)
                            @php $counter = $certificate_counter++; @endphp
                            <div id="oldcertificate-{{ $counter }}" class="row" style="border-top: black solid 1px;margin-bottom: 6px;padding-top: 15px;">
                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Text Type</label>
                                        <select name="text_type[]" class="form-control" id="text_type" required>
                                            <option value="">Select...</option>
                                            <option value="certificate_number" {{ (isset($setting['text_type']) && $setting['text_type'] == 'certificate_number') ? 'selected' : '' }}>Certificate Number</option>
                                            <option value="name" {{ (isset($setting['text_type']) && $setting['text_type'] == 'name') ? 'selected' : '' }}>Name</option>
                                            <option value="email" {{ (isset($setting['text_type']) && $setting['text_type'] == 'email') ? 'selected' : '' }}>Email</option>
                                            <option value="staffID" {{ (isset($setting['text_type']) && $setting['text_type'] == 'staffID') ? 'selected' : '' }}>Staff ID</option>
                                            <option value="date_issued" {{ (isset($setting['text_type']) && $setting['text_type'] == 'date_issued') ? 'selected' : '' }}>Date Issued</option>
                                            <option value="qr_code" {{ (isset($setting['text_type']) && $setting['text_type'] == 'qr_code') ? 'selected' : ''}}>QR Verification Code</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Font Type Face</label>
                                        <select name="text_type_face[]" class="form-control" id="text_type_face">
                                            @foreach(certificateFontType() as $key => $value)
                                                <option value="{{ $key }}" {{ (isset($setting['text_type_face']) && $setting['text_type_face'] == $key) ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Text font size, e.g 150</label>
                                        <input type="number" min="0" class="form-control" name="auto_certificate_name_font_size[]"
                                            value="{{ $setting['auto_certificate_name_font_size'] ?? old('auto_certificate_name_font_size') }}"
                                            id="auto_certificate_name_font_size">
                                    </div>
                                </div>

                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Text font weight e.g 300</label>
                                        <input type="number" min="0" class="form-control" name="auto_certificate_name_font_weight[]"
                                            value="{{ $setting['auto_certificate_name_font_weight'] ?? old('auto_certificate_name_font_weight') }}"
                                            id="auto_certificate_name_font_weight">
                                    </div>
                                </div>

                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Text Top offset. e.g 300</label>
                                        <input type="number" min="0" class="form-control" name="auto_certificate_top_offset[]"
                                            value="{{ $setting['auto_certificate_top_offset'] ?? old('auto_certificate_top_offset') }}"
                                            id="auto_certificate_top_offset">
                                    </div>
                                </div>

                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Text Left offset. e.g 100</label>
                                        <input type="number" min="0" class="form-control" name="auto_certificate_left_offset[]"
                                            value="{{ $setting['auto_certificate_left_offset'] ?? old('auto_certificate_left_offset') }}"
                                            id="auto_certificate_left_offset">
                                    </div>
                                </div>

                                <div class="col-md-4" style="margin-bottom:5px">
                                    <div class="form-group">
                                        <label>Text color</label>
                                        <input type="color" class="form-control" name="auto_certificate_color[]"
                                            value="{{ $setting['auto_certificate_color'] ?? '#000000' }}">
                                    </div>
                                </div>

                                <div class="col-md-2 mt-4">
                                    <div class="form-group">
                                        <label style="color:transparent">label</label>
                                        <button class="btn btn-danger remove-old-certificate" type="button"><i class="fa fa-minus"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif

                <div id="certificateRows"></div>
                <div class="row mt-5">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success btn-sm" id="addRowButton"><i class="fa fa-plus"></i> Add New Row</button>
                        <button type="button" class="btn-info btn-sm" id="previewButton"><i class="fa fa-eye"></i> Preview</button>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info mb-0">
                Legacy certificate settings have moved to the new Certificate Designer.
                <a href="{{ $certificateDesignerIndexUrl }}" class="alert-link">Open Certificate Designer</a>
            </div>
            @if(! $isCreate)
                <form action="{{ route('programs.certificate.migrate', $program) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        Migrate this program to the new Certificate Designer
                    </button>
                </form>
            @endif
        @endif
    </section>
</fieldset>
