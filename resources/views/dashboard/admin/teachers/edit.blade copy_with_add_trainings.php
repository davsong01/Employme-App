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
                            <div class="form-group">
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
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </fieldset>

                        <!-- Menu Permissions -->
                        <fieldset>
                            <legend>Menu Permissions</legend>
                            <div class="row">
                                @foreach(app('app\Http\Controllers\Controller')->adminMenus() as $menu)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="menu_permissions[]" value="{{ $menu['id'] }}" id="{{ $menu['id'] }}" {{ in_array($menu['id'], explode(',', $user->menu_permissions ?? '')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $menu['id'] }}">
                                            {{ $menu['name'] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <!-- Training and Permissions Section -->

                        {{-- <fieldset>
                            <legend>Trainings and Permissions</legend>
                            <div id="trainingPermissionsContainer">
                                <div class="training-permission-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="training[]">Select Training</label>
                                                <select name="training[]" class="form-control training-select">
                                                    <option value="" disabled selected>Select a training</option>
                                                    @foreach($allprograms as $allprogram)
                                                        <option value="{{ $allprogram->id }}">{{ $allprogram->p_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row permissions-row">
                                        <div class="col-md-12">
                                            <label>Permissions</label>
                                            <div class="row">
                                                @foreach(app('app\Http\Controllers\Controller')->adminMenus() as $menu)
                                                    <div class="col-md-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permissions[0][]" value="{{ $menu['id'] }}" id="menu_{{ $menu['id'] }}">
                                                            <label class="form-check-label" for="menu_{{ $menu['id'] }}">
                                                                {{ $menu['name'] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="btn btn-danger btn-sm removeRowButton">
                                                <i class="fa fa-minus"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-success btn-sm" id="addRowButton">
                                    <i class="fa fa-plus"></i> Add New Row
                                </button>
                            </div>
                        </fieldset> --}}
                        <fieldset>
                            <legend>Trainings and Permissions</legend>
                            <div id="trainingContainer">
                                <!-- Dynamic rows will be appended here -->
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
{{-- <script>
    $(document).ready(function () {
        const $container = $('#trainingPermissionsContainer');
        const $addRowButton = $('#addRowButton');

        // Function to update dropdown options
        function updateDropdownOptions() {
            const selectedPrograms = [];
            $('.training-select').each(function () {
                const value = $(this).val();
                if (value) {
                    selectedPrograms.push(value);
                }
            });

            $('.training-select').each(function () {
                const currentValue = $(this).val();
                $(this)
                    .find('option')
                    .each(function () {
                        const optionValue = $(this).val();
                        if (optionValue === currentValue || !selectedPrograms.includes(optionValue)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
            });
        }

        // Add new row
        $addRowButton.on('click', function () {
            const rowCount = $container.children('.training-permission-group').length;
            const newRow = `
                <div class="training-permission-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="training[]">Select Training</label>
                                <select required name="training[]" class="form-control training-select">
                                    <option value="" disabled selected>Select a training</option>
                                    @foreach($allprograms as $allprogram)
                                        <option value="{{ $allprogram->id }}">{{ $allprogram->p_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row permissions-row">
                        <div class="col-md-12">
                            <label>Permissions</label>
                            <div class="row">
                                @foreach(app('app\Http\Controllers\Controller')->adminMenus() as $menu)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[${rowCount}][]" value="{{ $menu['id'] }}" id="menu_${rowCount}_{{ $menu['id'] }}">
                                            <label class="form-check-label" for="menu_${rowCount}_{{ $menu['id'] }}">
                                                {{ $menu['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-danger btn-sm removeRowButton">
                                <i class="fa fa-minus"></i> Remove
                            </button>
                        </div>
                    </div>
                    <hr>
                </div>
            `;
            $container.append(newRow);
            updateDropdownOptions();
        });

        // Remove row
        $container.on('click', '.removeRowButton', function () {
            $(this).closest('.training-permission-group').remove();
            updateDropdownOptions();
        });

        // Update dropdowns when a training is selected
        $container.on('change', '.training-select', function () {
            updateDropdownOptions();
        });

        // Initial dropdown update
        updateDropdownOptions();
    });
</script> --}}
<script>
    $(document).ready(function () {
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

        const newRow = `
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
                        @foreach(app('app\Http\Controllers\Controller')->adminMenus() as $menu)
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" type="checkbox" value="{{ $menu['id'] }}">
                                <label class="form-check-label permission-label" for="permission-{{ $menu['id'] }}">
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
            </div>`;

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
                        $(this).attr("name", `training_and_permissions[${programId}][]`);
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
                    $(this).attr("name", `training_and_permissions[${programId}][]`);
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

</script>
<script>
    CKEDITOR.replace('ckeditor');
</script>
@endsection
