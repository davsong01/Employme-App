@extends('dashboard.admin.index')
@section('title', 'Add Company Admin')
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
                    <div class="card-title">
                        <h4 class="card-title">Add new Company Admin</h4>
                    </div>
                    <form action="{{route('companyuser.store')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        @csrf
                        
                        <!-- Personal Information Fieldset -->
                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Personal Information</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Name</label>
                                        <input required id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email">Email Address</label>
                                        <input required id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone">Phone</label>
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
                                        <label for="job_title">Job Title</label>
                                        <input id="job_title" type="text" class="form-control" name="job_title" value="{{ old('job_title') }}" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="">Select...</option> 
                                            <option value="active" selected>Active</option> 
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                                        <label for="gender">gender</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">Select...</option> 
                                            <option value="Male" selected>Male</option> 
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('profile_picture') ? ' has-error' : '' }}">
                                        <label for="profile_picture">Profile Picture</label>
                                        <input id="profile_picture" type="file" class="form-control" name="profile_picture" value="{{ old('profile_picture') }}" autofocus>
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
                                                    <input required class="form-check-input parent-checkbox" type="checkbox" name="permissions[]" value="{{$menu['id']}}" id="menu-{{$menu['id']}}">
                                                    <label class="form-check-label" for="menu-{{$menu['id']}}">
                                                        {{$menu['name']}}
                                                    </label>
                                                </div>
                                                
                                                @if (!empty($menu['children']))
                                                    <div class="ml-4">
                                                        @foreach($menu['children'] as $child)
                                                            <div class="form-check">
                                                                <input class="form-check-input child-checkbox-{{$menu['id']}}" type="checkbox" name="permissions[]" value="{{$child['id']}}" id="menu-{{$child['id']}}">
                                                                <label class="form-check-label" for="menu-{{$child['id']}}">
                                                                    {{$child['name']}}
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
                                                    <input type="checkbox" id="program{{ $program->id }}" name="trainings[{{ $program->id }}][selected]" value="1">
                                                </td>
                                                <td>
                                                    <label for="program{{ $program->id }}">{{ $program->p_name }}</label>
                                                </td>
                                                <td class="access-column">
                                                    <select name="trainings[{{ $program->id }}][access]" id="training{{ $program->id }}" class="form-control">
                                                        <option value="">Select...</option>
                                                        <option value="all_access">All Access</option>
                                                        <option value="limited_access">Limited Access</option>
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