@extends('dashboard.admin.index')
@section('title', $user->name )
@section('css')
<style>
    .access-column {
        width: 250px;
    }

    fieldset {
        border: 1px solid green;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
        background-color: #f9f9f9;
    }

    legend {
        font-weight: bold;
        color: green;
        padding: 0 10px;
        font-size: 1.1em;
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

                    <form action="{{ route('companyuser.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="pb-2">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Personal Information Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Personal Information</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Name</label>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email">Email Address</label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone">Phone</label>
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
                                        <label for="job_title">Job Title</label>
                                        <input id="job_title" type="text" class="form-control" name="job_title" value="{{ old('phone', $user->job_title) }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="">Select...</option> 
                                            <option value="active" {{ (old('status', $user->status) == 'active') ? 'selected' : '' }}>Active</option> 
                                            <option value="inactive" {{ (old('status', $user->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">Select...</option> 
                                            <option value="Male" {{ (old('gender', $user->gender) == 'Male') ? 'selected' : '' }}>Male</option> 
                                            <option value="Female" {{ (old('gender', $user->gender) == 'Female') ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password">New Password</label>
                                        <input id="password" type="text" class="form-control" name="password" value="{{ old('phone') }}" autofocus>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Menu Permissions Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Menu Permissions</legend>
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-md-12">
                                    <h6>Select Menu Permissions</h6>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        @foreach($menuStructure as $menu)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input parent-checkbox" type="checkbox" name="permissions[]" value="{{ $menu['id'] }}" id="menu-{{ $menu['id'] }}" 
                                                    {{ in_array($menu['id'], old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="menu-{{ $menu['id'] }}">
                                                        {{ $menu['name'] }}
                                                    </label>
                                                </div>
                                                
                                                @if (!empty($menu['children']))
                                                    <div class="ml-4">
                                                        @foreach($menu['children'] as $child)
                                                            <div class="form-check">
                                                                <input class="form-check-input child-checkbox-{{ $menu['id'] }}" type="checkbox" name="permissions[]" value="{{ $child['id'] }}" id="menu-{{ $child['id'] }}" 
                                                                {{ in_array($child['id'], old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="menu-{{ $child['id'] }}">
                                                                    {{ $child['name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Trainings and Access Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Trainings and Access</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Select Trainings</h6>
                                </div>
                                @foreach($programs as $program)
                                    <div class="col-md-6 mb-3">
                                        <table class="table">
                                            <tr>
                                                <td>
                                                    <input type="checkbox" id="program{{ $program->id }}" name="trainings[{{ $program->id }}][selected]" value="1" 
                                                    {{ array_key_exists($program->id, $user->trainings) ? 'checked' : '' }}>
                                                </td>
                                                <td>
                                                    <label for="program{{ $program->id }}">{{ $program->p_name }}</label>
                                                </td>
                                                <td class="access-column">
                                                    <select name="trainings[{{ $program->id }}][access]" id="training{{ $program->id }}" class="form-control">
                                                        <option value="">Select</option>
                                                        <option value="all_access" {{ (isset($user->trainings[$program->id]) && $user->trainings[$program->id] == 'all_access') ? 'selected' : '' }}>All Access</option>
                                                        <option value="limited_access" {{ (isset($user->trainings[$program->id]) && $user->trainings[$program->id] == 'limited_access') ? 'selected' : '' }}>Limited Access</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="row">
                            <button type="submit" class="btn btn-primary" style="width:100%">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extra-scripts')
<script>
    document.querySelectorAll('.parent-checkbox').forEach(parentCheckbox => {
        parentCheckbox.addEventListener('change', function() {
            const parentId = this.value;
            const childCheckboxes = document.querySelectorAll(`.child-checkbox-${parentId}`);
            
            childCheckboxes.forEach(childCheckbox => {
                childCheckbox.checked = this.checked;
            });
        });
    });
</script>
@endsection
