@php
    $check = [
        'teachers.update.menu',
        'teachers.update.training.access',
        'teachers.view.referral.details',
        'teachers.update',
        'teachers.role.status'
    ];

    $allpermissions = canUserAccessPermission($check);
@endphp
@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
<style>
    
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @include('layouts.partials.alerts')
                    <h4 class="card-title">{{$user->name}}</h4>
                    
                    @if ($allpermissions['teachers.view.referral.details'])
                        <p>
                            Referral link: <b id="link" style="color:blue">{{ url('/') .'/'.'?facilitator='. $user->license}}</b><br>
                            WAACSP Profile link: <b>{{ $user->waaccsp_link }}</b>
                        </p>
                    @endif
                    
                    <form action="{{ route('teachers.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                        
                        <!-- User Info Section -->
                        <fieldset>
                            <legend>Basic Information</legend>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <img src="{{ $user->image }}" alt="avatar" class="rounded-circle" width="100" height="100">
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Name</label>
                                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}" autofocus>
                                                @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email">E-Mail Address</label>
                                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email }}">
                                                @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone">Phone</label>
                                                <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') ?? $user->phone }}">
                                                @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="mb-3{{ $errors->has('password') ? ' is-invalid' : '' }}">
                                                    <label for="password" class="form-label">Password</label>
                                                    <div class="text-muted small mb-2">
                                                        Default: 12345. Leave blank if you want to keep the default password.
                                                    </div>
                                                    <input id="password" type="text" class="form-control" name="password"
                                                        value="{{ old('password') ?? '' }}" autofocus>
                                                    @if ($errors->has('password'))
                                                        <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </fieldset>

                        <!-- Role and Status Section -->
                        @if($allpermissions['teachers.role.status'])
                        <fieldset>
                            <legend>Role and Status</legend>
                            {{-- {{dd($user->roles)}} --}}
                            <div class="mb-3">
                                <label>Role*</label>
                                <div class="row">
                                    <!-- Admin Role -->
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="role[]" value="Admin" 
                                                {{ in_array('Admin', $user->roles) ? 'checked' : '' }} id="role-admin">
                                            <label class="form-check-label" for="role-admin">
                                                Admin
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Grader Role -->
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="role[]" value="Grader" 
                                                {{ in_array('Grader', $user->role()) ? 'checked' : '' }} id="role-grader">
                                            <label class="form-check-label" for="role-grader">
                                                Grader
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Facilitator Role -->
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="role[]" value="Facilitator" 
                                                {{ in_array('Facilitator', $user->role()) ? 'checked' : '' }} id="role-facilitator">
                                            <label class="form-check-label" for="role-facilitator">
                                                Facilitator
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('role')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </fieldset>
                        @endif
                        @if ($allpermissions['teachers.update.menu'])
                        <fieldset>
                            <legend>Menu Permissions</legend>
                            @php
                                $parentMenus = app('app\Http\Controllers\Controller')->adminMenus();
                                $user_permissions = $user->permissions();
                                // dd($parentMenus );
                            @endphp
                            <div class="row">
                                @foreach($parentMenus as $menu)
                                    <!-- Parent Menu -->
                                    <div class="col-md-3 mb-4">
                                        <div class="card border-primary shadow-sm">
                                            <div class="card-header bg-primary text-white">
                                                <div class="form-check">
                                                    <input class="form-check-input parent-checkbox" 
                                                        type="checkbox" 
                                                        name="menu_permissions[]" 
                                                        value="{{ $menu['route'] }}" 
                                                        id="parent-{{ $menu['id'] }}" 
                                                        {{ in_array($menu['route'], $user_permissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="parent-{{ $menu['id'] }}">
                                                        {{ $menu['name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if(!empty($menu['children']))
                                                    <!-- Group Children by Type -->
                                                    @php
                                                        $menuChildren = array_filter($menu['children'], fn($child) => $child['type'] === 'menu');
                                                        $accessChildren = array_filter($menu['children'], fn($child) => $child['type'] === 'access');
                                                    @endphp

                                                    <!-- Menu Section -->
                                                    @if(count($menuChildren) > 0)
                                                        <h6 class="fw-bold text-secondary">Menus</h6>
                                                        <ul class="list-unstyled ms-3">
                                                            @foreach($menuChildren as $child)
                                                                <li>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input child-checkbox" 
                                                                            type="checkbox" 
                                                                            name="menu_permissions[]" 
                                                                            value="{{ $child['route'] }}" 
                                                                            id="child-{{ $child['id'] }}" 
                                                                            data-parent-id="parent-{{ $menu['id'] }}" 
                                                                            {{ in_array($child['route'], $user_permissions) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="child-{{ $child['id'] }}">
                                                                            {{ $child['name'] }}
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif

                                                    <!-- Access Section -->
                                                    @if(count($accessChildren) > 0)
                                                        <h6 class="fw-bold text-secondary">Permissions</h6>
                                                        <ul class="list-unstyled ms-3">
                                                            @foreach($accessChildren as $child)
                                                                <li>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input child-checkbox" 
                                                                            type="checkbox" 
                                                                            name="menu_permissions[]" 
                                                                            value="{{ $child['route'] }}" 
                                                                            id="child-{{ $child['id'] }}" 
                                                                            data-parent-id="parent-{{ $menu['id'] }}" 
                                                                            {{ in_array($child['route'], $user_permissions) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="child-{{ $child['id'] }}">
                                                                            {{ $child['name'] }}
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                @else
                                                    <p class="text-muted fst-italic mb-0">No submenus available.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                        @endif
                        
                        @if ($allpermissions['teachers.update.training.access'])
                        <fieldset>
                            <legend>Trainings and Permissions</legend>
                            
                            <div id="trainingContainer">
                                @if(!empty($user->trainings))
                                <div class="accordion" id="trainingAccordion">
                                    @foreach($user->trainings as $index => $program)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-{{ $index }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}" aria-expanded="false" aria-controls="collapse-{{ $index }}">
                                               {{$loop->iteration}}. {{ $program->p_name ?? 'Unnamed Program' }}
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}" data-bs-parent="#trainingAccordion">
                                            <div class="accordion-body">
                                                <div class="training-row row mt-3">
                                                    <div class="col-md-6">
                                                        <label>Select Training</label>
                                                        <select class="form-control training-dropdown" name="training[]" required>
                                                            <option value="">Select a program</option>
                                                            @foreach($allprograms as $programOption)
                                                                <option value="{{ $programOption->id }}" {{ $programOption->id == $program->program_id ? 'selected' : '' }}>
                                                                    {{ $programOption->p_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-12 mt-4">
                                                        <!-- Permissions Section Title -->
                                                        <label class="d-flex justify-content-between align-items-center mb-3">
                                                            <span class="h5 mb-0">Permissions</span>
                                                            <span>
                                                                <input type="checkbox" class="select-all-permissions" id="selectAll-{{ $program->program_id }}">
                                                                <label for="selectAll-{{ $program->program_id }}" class="mb-0 text-muted">Select All</label>
                                                            </span>
                                                        </label>

                                                        @php
                                                            $permissions = app('app\Http\Controllers\Controller')->adminTrainingPermissions();
                                                            $programPermissions = $program->training_permissions;
                                                        @endphp

                                                        <!-- Permissions Container -->
                                                        <div class="row permissions-container border p-4 rounded2 shadow-sm bg-light">
                                                            @foreach($permissions as $menu)
                                                                @if($menu['children'] && count($menu['children']) > 0)
                                                                <!-- Parent Group -->
                                                                <div class="col-md-12 mb-4">
                                                                    <!-- Parent Name -->
                                                                    <h5 class="text-primary">{{ $menu['name'] }}</h5>
                                                                    <div class="row ms-3">
                                                                        @foreach($menu['children'] as $children)
                                                                            @php
                                                                                $checkboxId = "permission-{$program->program_id}-{$children['route']}";
                                                                            @endphp
                                                                            <!-- Child Permission Checkbox -->
                                                                            <div class="col-md-3">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox" 
                                                                                        type="checkbox" 
                                                                                        name="training_permissions[{{ $program->program_id }}][]" 
                                                                                        value="{{ $children['route'] }}"
                                                                                        id="{{ $checkboxId }}"
                                                                                        @if(in_array($children['route'], $programPermissions ?? [])) checked @endif>
                                                                                    <label class="form-check-label" for="{{ $checkboxId }}">
                                                                                        {{ $children['name'] }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            @endforeach
                                                        </div>

                                                        <!-- Error Message for Validation -->
                                                        <span class="text-danger d-none permission-error">At least one permission must be selected.</span>
                                                    </div>

                                                    <div class="col-md-12 mt-2 text-end">
                                                        <button type="button" class="btn btn-danger btn-sm removeRowButton">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 text-end">
                                    <button type="button" id="addRowButton" class="btn btn-success btn-sm">
                                        <i class="fa fa-plus"></i> Add New Row
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        @endif

                        @if ($allpermissions['teachers.update'])
                        <div class="row">
                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // When a parent checkbox is toggled
        $('.parent-checkbox').on('change', function() {
            let parentId = $(this).attr('id');
            let isChecked = $(this).is(':checked');
            
            // Select or deselect all children associated with this parent
            $(`.child-checkbox[data-parent-id="${parentId}"]`).prop('checked', isChecked);
        });

        // When a child checkbox is toggled
        $('.child-checkbox').on('change', function() {
            let parentId = $(this).data('parent-id');
            let allChecked = $(`.child-checkbox[data-parent-id="${parentId}"]:checked`).length > 0;
            
            // Update parent checkbox based on children
            $(`#${parentId}`).prop('checked', allChecked);
        });
    });
</script>

<script>
    $(document).ready(function () {
        const programsList = @json($allprograms); // Existing programs list
        const permissionsData = @json(app('app\\Http\\Controllers\\Controller')->adminTrainingPermissions()); // Permissions data
        const trainingAccordion = $("#trainingAccordion");

        let rowCounter = $(".accordion-item").length; // Counter for unique IDs, start with existing rows

        // Add new row
        $("#addRowButton").click(function () {
            const selectedPrograms = $("select.training-dropdown").map(function () {
                return $(this).val();
            }).get();

            const availablePrograms = programsList.filter(program => !selectedPrograms.includes(program.id.toString()));

            if (availablePrograms.length === 0) {
                alert("No more trainings available to select.");
                return;
            }

            let options = availablePrograms.map(program => `<option value="${program.id}">${program.p_name}</option>`).join("");

            // Increment rowCounter for unique IDs
            rowCounter++;
            const uniqueRowId = `row-${rowCounter}`;
            const headingId = `heading-${rowCounter}`;
            const collapseId = `collapse-${rowCounter}`;

            // Generate permissions HTML
            let permissionsHtml = permissionsData.map(menu => {
                const permissionIdPrefix = `${uniqueRowId}-${menu.route}`;

                if (menu.children && menu.children.length > 0) {
                    let childPermissionsHtml = menu.children.map(child => {
                        const childPermissionId = `${permissionIdPrefix}-${child.route}`;
                        return `
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" 
                                        type="checkbox" 
                                        name="training_permissions[PROGRAM_ID][]" 
                                        value="${child.route}" 
                                        id="${childPermissionId}">
                                    <label class="form-check-label" for="${childPermissionId}">
                                        ${child.name}
                                    </label>
                                </div>
                            </div>`;
                    }).join('');

                    return `
                        <div class="col-md-12 mb-3">
                            <h5 class="text-primary">${menu.name}</h5>
                            <div class="row ms-3">
                                ${childPermissionsHtml}
                            </div>
                        </div>`;
                }

                return '';
            }).join('');

            // Replace PROGRAM_ID with the first available program ID
            permissionsHtml = permissionsHtml.replace(/PROGRAM_ID/g, availablePrograms[0].id);

            // Create the accordion item HTML
            let newAccordionItem = `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="${headingId}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                            Training Row ${rowCounter}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#trainingAccordion">
                        <div class="accordion-body">
                            <div class="training-row row mt-3" data-row-id="${uniqueRowId}">
                                <div class="col-md-6">
                                    <label>Select Training</label>
                                    <select class="form-control training-dropdown" name="training[]" required>
                                        <option value="">Select a program</option>
                                        ${options}
                                    </select>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="d-flex justify-content-between align-items-center">
                                        Permissions
                                        <span>
                                            <input type="checkbox" class="select-all-permissions" id="select-all-${uniqueRowId}">
                                            <label for="select-all-${uniqueRowId}" class="ms-2">Select All</label>
                                        </span>
                                    </label>
                                    <div class="permissions-container border p-3 rounded2 shadow-sm bg-light">
                                        ${permissionsHtml}
                                    </div>
                                    <span class="text-danger d-none permission-error">At least one permission must be selected.</span>
                                </div>
                                <div class="col-md-12 mt-2 text-end">
                                    <button type="button" class="btn btn-danger btn-sm removeRowButton">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            // Append the new accordion item to the container
            trainingAccordion.append(newAccordionItem);
            updateTrainingOptions();
        });

        // Remove row
        trainingAccordion.on("click", ".removeRowButton", function () {
            $(this).closest(".accordion-item").remove();
            updateTrainingOptions();
        });

        // Update training options dynamically
        function updateTrainingOptions() {
            const selectedPrograms = $("select.training-dropdown").map(function () {
                return $(this).val();
            }).get();

            $("select.training-dropdown").each(function () {
                const currentValue = $(this).val();
                let options = programsList
                    .filter(program => program.id.toString() === currentValue || !selectedPrograms.includes(program.id.toString()))
                    .map(program => `<option value="${program.id}" ${currentValue === program.id.toString() ? "selected" : ""}>${program.p_name}</option>`)
                    .join("");
                $(this).html(`<option value="">Select a program</option>${options}`);
            });
        }

        // "Select All" functionality
        trainingAccordion.on("change", ".select-all-permissions", function () {
            const parentRow = $(this).closest(".training-row");
            const isChecked = $(this).prop("checked");

            parentRow.find(".permission-checkbox").each(function () {
                $(this).prop("checked", isChecked);
            });
        });

        // Trigger update when training is changed
        trainingAccordion.on("change", ".training-dropdown", function () {
            const programId = $(this).val();
            const parentRow = $(this).closest(".training-row");
            if (programId) {
                parentRow
                    .find(".permissions-container .permission-checkbox")
                    .each(function () {
                        $(this).attr("name", `training_permissions[${programId}][]`);
                    });
            }
            updateTrainingOptions();
        });
    });
</script>
<script>
    CKEDITOR.replace('ckeditor');
</script>
@endsection
