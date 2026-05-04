@extends('dashboard.admin.index')

@section('css')
<style>
    a.pre-order-btn { 
        color: #000;
        background-color: gold;
        border-radius: 1em;
        padding: 1em;
        display: block;
        margin: 2em auto;
        width: 100%;
        font-size: 1.25em;
        font-weight: bold;
        text-align: center;
    }

    a.pre-order-btn:hover { 
        background-color: #000;
        text-decoration: none;
        color: gold;
    }

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

@section('title', 'Add Certificate Template')

@section('content')
<div class="container-fluid">
    @include('layouts.partials.alerts')

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Regeneration Certificate Management</h4>
            <a href="javascript:void(0)" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                Add Template
            </a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <table id="zero_config" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Preview</th>
                        <th>Programs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ paginationIndex($templates, $loop) }}</td>
                        <td>{{ $template->name ?? 'Untitled Template' }}</td>

                        <td style="text-align:center;">
                            @if(!empty($template->auto_certificate_settings['auto_certificate_template']))
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#previewModal-{{ $template->id }}">
                                    Preview
                                </button>
                            @else
                                <span>No Preview Available</span>
                            @endif
                        </td>
                        <td>
                            <ol>
                                @foreach($template->certificatePrograms as $program)
                                    <li>{{ $program->p_name ?? 'Unnamed Program' }}</li>
                                @endforeach
                            </ol>
                        </td>

                        <td>
                            <div class="btn-group">
                                <a data-bs-toggle="modal" style="color:white" data-bs-target="#edit-{{ $template->id }}" class="btn btn-info btn-sm" title="Edit template">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <form action="{{ route('certificatetemplate.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Template">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @foreach($templates as $template)
            <div class="modal fade" id="edit-{{ $template->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $template->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('update.certificate.template', $template->id) }}" method="POST" enctype="multipart/form-data" class="edit-template-form">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <!-- Template Name -->
                                    <div class="col-md-12 mb-3">
                                        <label for="template_name_{{ $template->id }}" class="form-label">Template Name</label>
                                        <input type="text" name="name" id="template_name_{{ $template->id }}" class="form-control" value="{{ old('name', $template->name) }}" required>
                                    </div>
                                    <!-- Programs select -->
                                    <div class="col-md-12 mb-3">
                                        <label>Programs</label>
                                        <select name="program_ids[]" class="select2 form-control" multiple required data-placeholder="Select Programs">
                                            @foreach($programs as $pro)
                                                <option value="{{ $pro->id }}"
                                                    {{ in_array($pro->id, $template->certificatePrograms->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $pro->p_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>

                                <!-- File Upload -->
                                <div class="row mb-3">
                                    <label class="col-form-label">
                                        @if(!empty($template->auto_certificate_settings['auto_certificate_template']))
                                            Replace Certificate Template
                                        @else
                                            Upload Certificate Template
                                        @endif
                                    </label>
                                    <input type="file" name="auto_certificate_template" class="form-control">
                                    @if(!empty($template->auto_certificate_settings['auto_certificate_template']))
                                        <small>Current file: {{ basename($template->auto_certificate_settings['auto_certificate_template']) }}</small>
                                    @endif
                                </div>

                                <!-- Existing Settings Rows -->
                                <section id="edit-certificate-holder-{{ $template->id }}">
                                    @php
                                        $settings = $template->auto_certificate_settings['settings'] ?? [];
                                        $counter = 1;
                                    @endphp
                                    @foreach($settings as $setting)
                                    <div class="row edit-certificate-row" data-row-id="{{ $counter }}" style="border-top: black solid 1px; margin-bottom: 6px; padding-top: 15px;">
                                        <div class="col-md-4 mb-3">
                                            <label>Text Type</label>
                                            <select name="text_type[]" class="form-control" required>
                                                <option value="">Select...</option>
                                                <option value="certificate_number" {{ $setting['text_type'] == 'certificate_number' ? 'selected' : '' }}>Certificate Number</option>
                                                <option value="name" {{ $setting['text_type'] == 'name' ? 'selected' : '' }}>Name</option>
                                                <option value="email" {{ $setting['text_type'] == 'email' ? 'selected' : '' }}>Email</option>
                                                <option value="staffID" {{ $setting['text_type'] == 'staffID' ? 'selected' : '' }}>Staff ID</option>
                                                <option value="date_issued" {{ $setting['text_type'] == 'date_issued' ? 'selected' : '' }}>Date Issued</option>
                                                <option value="qr_code" {{ (isset($setting['text_type']) && $setting['text_type'] == 'qr_code') ? 'selected' : ''}}>QR Verification Code</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Font Type Face</label>
                                            <select name="text_type_face[]" class="form-control">
                                                @foreach(certificateFontType() as $key => $value)
                                                <option value="{{ $key }}" {{ $setting['text_type_face'] == $key ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Text font size</label>
                                            <input type="number" min="0" name="auto_certificate_name_font_size[]" class="form-control" value="{{ $setting['auto_certificate_name_font_size'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Text font weight</label>
                                            <input type="number" min="0" name="auto_certificate_name_font_weight[]" class="form-control" value="{{ $setting['auto_certificate_name_font_weight'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Text Top offset</label>
                                            <input type="number" min="0" name="auto_certificate_top_offset[]" class="form-control" value="{{ $setting['auto_certificate_top_offset'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Text Left offset</label>
                                            <input type="number" min="0" name="auto_certificate_left_offset[]" class="form-control" value="{{ $setting['auto_certificate_left_offset'] ?? '' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>Text color</label>
                                            <input type="color" name="auto_certificate_color[]" class="form-control" value="{{ $setting['auto_certificate_color'] ?? '#000000' }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label>QR VerificationCode</label>
                                            <input type="color" name="auto_certificate_color[]" class="form-control" value="{{ $setting['auto_certificate_color'] ?? '#000000' }}">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <button type="button" class="btn btn-danger btn-sm remove-old-certificate" data-row-id="{{ $counter }}">
                                                <i class="fa fa-minus"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    @php $counter++; @endphp
                                    @endforeach
                                </section>

                                <!-- Container for New Rows -->
                                <div id="edit-certificate-new-rows-{{ $template->id }}"></div>

                                <!-- Controls -->
                                <div class="row mt-2">
                                    <div class="col-md-4 mb-3">
                                        <button type="button" class="btn btn-success btn-sm add-edit-row-btn" data-template-id="{{ $template->id }}">
                                            <i class="fa fa-plus"></i> Add New Row
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm preview-edit-btn" data-template-id="{{ $template->id }}">
                                            <i class="fa fa-eye"></i> Preview
                                        </button>
                                        <span class="loadingSpinner" style="display:none; margin-left:5px;">
                                            <i class="fa fa-spinner fa-spin"></i>
                                        </span>
                                    </div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Template</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @php
                $filePath = $template->auto_certificate_settings['auto_certificate_template'] ?? null;
            @endphp

            @if($filePath)
            <div class="modal fade" id="previewModal-{{ $template->id }}" tabindex="-1" aria-labelledby="previewModalLabel-{{ $template->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Template Preview: {{ $template->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ route('certificate.template.serve', $template->id) }}?v={{ \Illuminate\Support\Str::random(8) }}" alt="Certificate Template" class="img-fluid" style="max-height: 80vh;">

                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
<!-- Add Template Modal -->
<div class="modal fade" id="addTemplateModal" tabindex="-1" aria-labelledby="addTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form action="{{route('save.certificate.template')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="template_name" class="form-label">Template Name</label>
                                <input type="text" name="name" class="form-control" id="template_name" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label style="color:">Programs:</label>
                                <select name="program_ids[]" class="select2 form-control" multiple required>
                                    @foreach($programs->whereNotIn('id', $attachedProgramIds) as $pro)
                                        <option value="{{ $pro->id }}" {{ in_array($pro->id, $program->resolve_to_ids ?? []) ? 'selected' : '' }}>
                                            {{ $pro->p_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <section>
                        @php
                            $c_settings['auto_certificate_status'] = $c_settings['auto_certificate_status'] ?? 'no';
                        @endphp
                        <div class="row">  
                            <div class="col-md-12" style="margin-bottom:5px">
                                <div class="form-group row">
                                    <label class="col-md-6 col-form-label">
                                        @if(isset($c_settings['auto_certificate_template']))
                                            Replace Certificate Template
                                        @else
                                            Upload Certificate Template
                                        @endif
                                    </label>
                                    
                                    <div class="col-md-12">
                                        <input type="file" name="auto_certificate_template" class="form-control" id="auto_certificate_template">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(!empty($c_settings['settings']))
                            @php
                                $certificate_counter = 1;
                            @endphp
                            <section id="certificate-holder" class="pt-2">
                                <div class="row" id="certificate-0">
                                </div>
                                @foreach($c_settings['settings'] as $setting)
                                @php
                                    $counter = $certificate_counter++;
                                @endphp
                                <div id="oldcertificate-{{ $counter }}" class="row" style="border-top: black solid 1px;margin-bottom: 6px;padding-top: 15px;">  
                                    <div class="col-md-4" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <label>Text Type</label>
                                            <select name="text_type[]" class="form-control" id="text_type" required>
                                                <option value="">Select...</option>
                                                <option value="certificate_number" {{ (isset($setting['text_type']) && $setting['text_type'] == 'certificate_number') ? 'selected' : ''}}>Certificate Number</option>
                                                <option value="name" {{ (isset($setting['text_type']) && $setting['text_type'] == 'name') ? 'selected' : ''}}>Name</option>
                                                <option value="email" {{ (isset($setting['text_type']) && $setting['text_type'] == 'email') ? 'selected' : ''}}>Email</option>
                                                <option value="staffID" {{ (isset($setting['text_type']) && $setting['text_type'] == 'staffID') ? 'selected' : ''}}>Staff ID</option>
                                                <option value="date_issued" {{ (isset($setting['text_type']) && $setting['text_type'] == 'date_issued') ? 'selected' : ''}}>Date Issued</option>
                                                <option value="qr_code" {{ (isset($setting['text_type']) && $setting['text_type'] == 'qr_code') ? 'selected' : ''}}>QR Verification Code</option>

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
                                            <input type="number" min="0" class="form-control" name="auto_certificate_name_font_size[]" value="{{ $setting['auto_certificate_name_font_size'] ?? old('auto_certificate_name_font_size')}}" id="auto_certificate_name_font_size">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <label>Text font weight e.g 300</label>
                                            <input type="number" min="0" class="form-control" name="auto_certificate_name_font_weight[]" value="{{ $setting['auto_certificate_name_font_weight'] ?? old('auto_certificate_name_font_weight')}}" id="auto_certificate_name_font_weight">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <label>Text Top offset. e.g 300</label>
                                            <input type="number" min="0" class="form-control" name="auto_certificate_top_offset[]" value="{{ $setting['auto_certificate_top_offset'] ?? old('auto_certificate_top_offset') }}" id="auto_certificate_top_offset">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <label>Text Left offset. e.g 100</label>
                                            <input type="number" min="0" class="form-control" name="auto_certificate_left_offset[]" value="{{ $setting['auto_certificate_left_offset'] ?? old('auto_certificate_left_offset') }}" id="auto_certificate_left_offset">
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="margin-bottom:5px">
                                        <div class="form-group">
                                            <label>Text color</label>
                                            <input type="color" class="form-control" name="auto_certificate_color[]" value="{{ $setting['auto_certificate_color'] ?? '#000000' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="mark" style="color:transparent">sdsdsddsdssd</label>
                                            <button class="btn btn-danger remove-old-certificate" id="oldcertificate-{{ $counter }}" type="button" style="min-width: unset;"> <i class="fa fa-minus"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </section>
                        @endif
                        <div id="certificateRows"></div>
                        <div class="row">
                            <div class="col-md-4" style="margin-bottom:5px">
                                <div class="form-group">
                                    <button type="button" class="btn btn-success btn-sm" id="addRowButton"><i class="fa fa-plus"></i> Add New Row</button>
                                    <button type="button" class="btn-info btn-sm" id="previewButton"><i class="fa fa-eye"></i> Preview</button>
                                    <span id="loadingSpinner" style="display: none; margin-left: 5px;">
                                        <i class="fa fa-spinner fa-spin"></i>
                                    </span>
                                </div>
                                <div class="form-group">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Template</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div id="previewModal" style="display:none; position: fixed; top: 10%; left: 50%; transform: translateX(-50%); background: white; padding: 20px; border: 1px solid #ccc; z-index: 1050;">
        <button id="closePreviewModal" style="float: right; font-size: 16px; background: transparent; border: none; cursor: pointer;">&times;</button>
        <img id="certificatePreviewImage" src="" alt="Certificate Preview" style="max-width: 100%; height: auto;">
    </div>
</div>
<script>
    $(function () {
        let rowIndex = 0;
    
        // Add row
        $('#addRowButton').on('click', function () {
            const newRow = `
                <div class="row certificate-row" id="certificate-${rowIndex}" style="border-top: black solid 1px; margin-bottom: 6px; padding-top: 15px;">
                    <div class="col-md-4 mb-3">
                        <label>Text Type</label>
                        <select name="text_type[]" class="form-control" required>
                            <option value="">Select...</option>
                            <option value="certificate_number">Certificate Number</option>
                            <option value="name">Name</option>
                            <option value="email">Email</option>
                            <option value="staffID">Staff ID</option>
                            <option value="date_issued">Date Issued</option>
                            <option value="qr_code">QR Verification Code</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Font Type Face</label>
                        <select name="text_type_face[]" class="form-control">
                            @foreach(certificateFontType() as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Text font size</label>
                        <input type="number" min="0" name="auto_certificate_name_font_size[]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Text font weight</label>
                        <input type="number" min="0" name="auto_certificate_name_font_weight[]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Text Top offset</label>
                        <input type="number" min="0" name="auto_certificate_top_offset[]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Text Left offset</label>
                        <input type="number" min="0" name="auto_certificate_left_offset[]" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Text color</label>
                        <input type="color" name="auto_certificate_color[]" class="form-control" value="#000000">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label style="color: transparent;">Remove</label>
                        <button type="button" class="btn btn-danger btn-sm remove-certificate-row">
                            <i class="fa fa-minus"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            $('#certificateRows').append(newRow);
            rowIndex++;
        });
    
        // Remove row (delegated handler)
        $(document).on('click', '.remove-certificate-row, .remove-old-certificate', function () {
            $(this).closest('.row').remove();
        });
    
        // Preview button
        $('#previewButton').on('click', function (e) {
            e.preventDefault();
            $('#loadingSpinner').show();
    
            const form = $(this).closest('form')[0];
            const formData = new FormData(form);
    
            $.ajax({
                url: '/admin/preview-regenerated-certificate-settings',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false
            })
            .done(res => {
                $('#loadingSpinner').hide();
                if (res.preview_image_path) {
                    $('#certificatePreviewImage').attr('src', res.preview_image_path);
                    $('#previewModal').show();
                } else {
                    alert('Preview failed: ' + (res.error ?? 'Unknown error'));
                }
            })
            .fail((xhr, status, error) => {
                $('#loadingSpinner').hide();
                alert('An error occurred: ' + error);
            });
        });
    
        // Close preview modal
        $('#closePreviewModal').on('click', function () {
            $('#previewModal').hide();
        });
    
        // Select2 inside modal
        $('#addTemplateModal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2({
                dropdownParent: $('#addTemplateModal'),
                width: '100%',
                placeholder: $(this).find('.select2').data('placeholder') || 'Select Programs',
                allowClear: true
            });
        });
    });

    $(function () {
        // Add new row for each modal
        $('.add-edit-row-btn').on('click', function () {
            const templateId = $(this).data('template-id');
            const $container = $('#edit-certificate-new-rows-' + templateId);
            const index = $container.children('.edit-certificate-row').length + 1;

            const newRow = `
            <div class="row edit-certificate-row" data-row-id="${index}" style="border-top: 1px solid black; margin-bottom: 6px; padding-top: 15px;">
                <div class="col-md-4 mb-3">
                    <label>Text Type</label>
                    <select name="text_type[]" class="form-control" required>
                        <option value="">Select...</option>
                        <option value="certificate_number">Certificate Number</option>
                        <option value="name">Name</option>
                        <option value="email">Email</option>
                        <option value="staffID">Staff ID</option>
                        <option value="date_issued">Date Issued</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Font Type Face</label>
                    <select name="text_type_face[]" class="form-control">
                        @foreach(certificateFontType() as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Text font size</label>
                    <input type="number" min="0" name="auto_certificate_name_font_size[]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Text font weight</label>
                    <input type="number" min="0" name="auto_certificate_name_font_weight[]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Text Top offset</label>
                    <input type="number" min="0" name="auto_certificate_top_offset[]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Text Left offset</label>
                    <input type="number" min="0" name="auto_certificate_left_offset[]" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Text color</label>
                    <input type="color" name="auto_certificate_color[]" class="form-control" value="#000000">
                </div>
                <div class="col-md-12 mb-3">
                    <button type="button" class="btn btn-danger btn-sm remove-old-certificate" data-row-id="${index}">
                        <i class="fa fa-minus"></i> Remove
                    </button>
                </div>
            </div>
            `;
            $container.append(newRow);
        });

        // Remove old or newly added row
        $(document).on('click', '.remove-old-certificate', function () {
            $(this).closest('.edit-certificate-row').remove();
        });

        // Preview per-template
        $('.preview-edit-btn').on('click', function () {
            const templateId = $(this).data('template-id');
            const $form = $('#edit-' + templateId).find('form')[0];
            const $spinner = $('#edit-' + templateId).find('.loadingSpinner');
            const formData = new FormData($form);

            $spinner.show();

            $.ajax({
                url: '/admin/preview-regenerated-certificate-settings',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false
            })
            .done(response => {
                $spinner.hide();
                if (response.preview_image_path) {
                    $('#certificatePreviewImage').attr('src', response.preview_image_path);
                    $('#previewModal-' + templateId).modal('show');
                } else {
                    alert('Preview failed: ' + (response.error ?? 'Unknown error.'));
                }
            })
            .fail((xhr, status, error) => {
                $spinner.hide();
                console.error(error);
                alert('AJAX error: ' + error);
            });
        });

        // Initialize Select2 for all select2s in modals
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2({
                dropdownParent: $(this),
                width: '100%',
                placeholder: $(this).find('.select2').data('placeholder'),
                allowClear: true
            });
        });
    });

    $(document).on('click', '#closePreviewModal', function() {
        $('#previewModal').hide();
    });
</script>
@endsection

