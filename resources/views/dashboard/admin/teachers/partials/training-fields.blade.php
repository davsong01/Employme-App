@php
    $trainingPermissionMenus = app('app\\Http\\Controllers\\Controller')->adminTrainingPermissions();
@endphp

<section class="teacher-form-section" id="training-access">
    <h2 class="teacher-section-title">Training access</h2>
    <p class="teacher-section-hint">Choose each training and the permissions this teacher should have for it.</p>

    <div id="trainingContainer">
        <div class="accordion" id="trainingAccordion">
            @foreach($trainingRows as $index => $program)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $index }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}" aria-expanded="false" aria-controls="collapse-{{ $index }}">
                            {{ $loop->iteration }}. {{ $program->p_name ?? 'Select a training' }}
                        </button>
                    </h2>
                    <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}" data-bs-parent="#trainingAccordion">
                        <div class="accordion-body">
                            <div class="training-row row mt-3">
                                <div class="col-md-6">
                                    <label for="training-{{ $index }}">Select training</label>
                                    <select id="training-{{ $index }}" class="form-control training-dropdown" name="training[]" required>
                                        <option value="">Select a program</option>
                                        @foreach($programs as $programOption)
                                            <option value="{{ $programOption->id }}" {{ $programOption->id == $program->program_id ? 'selected' : '' }}>{{ $programOption->p_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="h6 mb-0">Permissions</h3>
                                        <label class="d-inline-flex align-items-center gap-2 mb-0">
                                            <input type="checkbox" class="select-all-permissions" id="select-all-{{ $index }}">
                                            Select all
                                        </label>
                                    </div>
                                    @php
                                        $programPermissions = $program->training_permissions ?? [];
                                    @endphp
                                    <div class="row permissions-container p-3">
                                        @foreach($trainingPermissionMenus as $menuIndex => $menu)
                                            @if(!empty($menu['children']))
                                                <div class="col-12 mb-3">
                                                    <h4 class="h6 text-primary">{{ $menu['name'] }}</h4>
                                                    <div class="row">
                                                        @foreach($menu['children'] as $permissionIndex => $permission)
                                                            <div class="col-md-4 col-lg-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="training_permissions[{{ $program->program_id }}][]" value="{{ $permission['route'] }}" id="permission-{{ $index }}-{{ $menuIndex }}-{{ $permissionIndex }}" @checked(in_array($permission['route'], $programPermissions))>
                                                                    <label class="form-check-label" for="permission-{{ $index }}-{{ $menuIndex }}-{{ $permissionIndex }}">{{ $permission['name'] }}</label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12 mt-3 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm removeRowButton">Remove training</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3 text-end">
        <button type="button" id="addRowButton" class="btn btn-outline-primary"><i class="fa fa-plus" aria-hidden="true"></i> Add training</button>
    </div>
</section>

@include('dashboard.admin.teachers.partials.training-scripts', ['programs' => $programs, 'trainingPermissionMenus' => $trainingPermissionMenus])
