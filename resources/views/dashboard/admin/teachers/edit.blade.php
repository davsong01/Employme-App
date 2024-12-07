@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
<style>
    .select2-container--default .select2-selection--multiple {
        line-height: 27px;
        overflow: scroll;
        height: 150px;
    }
    .view {
        margin: 0 10px;
        border-radius: 10%;
    }
    fieldset {
        border: 1px solid #ddd;
        padding: 10px 15px;
        margin-bottom: 15px;
    }
    legend {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #0056b3;
    }
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
                    
                    @if(!empty(array_intersect(facilitatorRoles(), $user->role())))  
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
                                    <div class="form-group">
                                        <img src="{{ $user->image }}" alt="avatar" class="rounded-circle" width="100" height="100">
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') ?? $user->name }}" autofocus>
                                        @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">E-Mail Address</label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') ?? $user->email }}">
                                        @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') ?? $user->t_phone }}">
                                        @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Role and Status Section -->
                        <fieldset>
                            <legend>Role and Status</legend>
                            {{-- <div class="form-group">
                                <label for="role">Role*</label>
                                <select name="role[]" id="role" class="select2 role form-control" multiple="multiple" style="height: 30px; width: 100%;">
                                    <option value="" disabled>Assign Role</option>
                                    <option value="Facilitator" {{ !empty(array_intersect(facilitatorRoles(), $user->role())) ? 'selected' : '' }}>Facilitator</option>
                                    <option value="Grader" {{ !empty(array_intersect(graderRoles(), $user->role())) ? 'selected' : '' }}>Grader</option>
                                    <option value="Admin" {{ !empty(array_intersect(adminRoles(), $user->role())) ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> --}}
                            <div class="form-group">
                                <label>Role*</label>
                                <div class="row">
                                    <!-- Admin Role -->
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="role[]" value="Admin" 
                                                {{ in_array('Admin', $user->role()) ? 'checked' : '' }} id="role-admin">
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

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </fieldset>
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

                        <!-- Training and Permissions Section -->
                        <fieldset>
                            <legend>Trainings and Permissions</legend>
                            <div id="trainingContainer">
                                @if(!empty($user->trainings))
                                    @foreach($user->trainings as $program)
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
                                            <div class="col-md-12 mt-2">
                                                <label>Permissions</label>
                                                <div class="row permissions-container">
                                                    @foreach(app('app\Http\Controllers\Controller')->adminPermissions() as $menu)
                                                        <div class="col-md-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" 
                                                                    type="checkbox" 
                                                                    name="training_permissions[{{ $program->program_id }}][]" 
                                                                    value="{{ $menu['slug'] }}"
                                                                    @if(in_array($menu['slug'], $program->training_permissions ?? [])) checked @endif>
                                                                <label class="form-check-label permission-label" for="permission-{{ $menu['slug'] }}">
                                                                    {{ $menu['name'] }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <span class="text-danger d-none permission-error">At least one permission must be selected.</span>
                                            </div>
                                            <div class="col-md-12 mt-2 text-end">
                                                <button type="button" class="btn btn-danger btn-sm removeRowButton">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
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

                        


                        <div class="row">
                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                        </div>
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
        // Pass the permissions data from PHP to JavaScript
        let permissionsData = @json(app('app\Http\Controllers\Controller')->adminPermissions());

        let programsList = @json($allprograms); // Existing programs list
        const trainingContainer = $("#trainingContainer");

        // Add new row
        $("#addRowButton").click(function () {
            const selectedPrograms = $("select.training-dropdown").map(function () {
                return $(this).val();
            }).get();

            const availablePrograms = programsList.filter(program => !selectedPrograms.includes(program.id.toString()));

            if (availablePrograms.length === 0) {
                alert("No more programs available to select.");
                return;
            }

            let options = availablePrograms.map(program => `<option value="${program.id}">${program.p_name}</option>`).join("");

            // Dynamically generate a row using JavaScript template literals
            let permissionsHtml = permissionsData.map(menu => `
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input permission-checkbox" 
                            type="checkbox" 
                            name="training_permissions[PROGRAM_ID][]" 
                            value="${menu.slug}">
                        <label class="form-check-label permission-label" for="permission-${menu.slug}">
                            ${menu.name}
                        </label>
                    </div>
                </div>`).join('');

            let newRow = `
                <div class="training-row row mt-3">
                    <div class="col-md-6">
                        <label>Select Training</label>
                        <select class="form-control training-dropdown" name="training[]" required>
                            <option value="">Select a program</option>
                            ${options}
                        </select>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label>Permissions</label>
                        <div class="row permissions-container">
                            ${permissionsHtml}
                        </div>
                        <span class="text-danger d-none permission-error">At least one permission must be selected.</span>
                    </div>
                    <div class="col-md-12 mt-2 text-end">
                        <button type="button" class="btn btn-danger btn-sm removeRowButton">Remove</button>
                    </div>
                </div>`;

            // Replace PROGRAM_ID with the correct program ID dynamically
            newRow = newRow.replace(/PROGRAM_ID/g, availablePrograms[0].id);

            // Append the new row to the container
            trainingContainer.append(newRow);
            updateTrainingOptions();
        });

        // Remove row
        trainingContainer.on("click", ".removeRowButton", function () {
            $(this).closest(".training-row").remove();
            updateTrainingOptions();
        });

        // Update training dropdown options and permissions name
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

                const programId = $(this).val();
                if (programId) {
                    $(this)
                        .closest(".training-row")
                        .find(".permissions-container .permission-checkbox")
                        .each(function () {
                            $(this).attr("name", `training_permissions[${programId}][]`);
                        });
                }
            });
        }

        // Form validation
        $("form").submit(function (e) {
            let isValid = true;

            $(".training-row").each(function () {
                const trainingSelected = $(this).find(".training-dropdown").val();
                const permissionsChecked = $(this).find(".permission-checkbox:checked").length > 0;

                if (!trainingSelected) {
                    isValid = false;
                    $(this).find(".training-dropdown").addClass("is-invalid");
                } else {
                    $(this).find(".training-dropdown").removeClass("is-invalid");
                }

                if (!permissionsChecked) {
                    isValid = false;
                    $(this).find(".permission-error").removeClass("d-none");
                    $(this).find(".permissions-container").addClass("border border-danger");
                } else {
                    $(this).find(".permission-error").addClass("d-none");
                    $(this).find(".permissions-container").removeClass("border border-danger");
                }
            });

            if (!isValid) {
                alert("Please complete all fields correctly.");
                e.preventDefault();
            }
        });

        // Trigger update when training is changed
        trainingContainer.on("change", ".training-dropdown", function () {
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

        // Toggle checkbox by clicking label
        trainingContainer.on("click", ".permission-label", function () {
            const checkbox = $(this).siblings(".permission-checkbox");
            checkbox.prop("checked", !checkbox.prop("checked"));
        });
    });
</script>

<script>
    CKEDITOR.replace('ckeditor');
</script>
@endsection
