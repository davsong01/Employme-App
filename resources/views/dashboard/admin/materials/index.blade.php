<?php
    $permissionsToCheck = ['materials.store','material.clone','materials.download','materials.destroy','getmaterial'];
    $permissions = checkTrainingHasPermissions($training->id, $permissionsToCheck);
?>
@extends('dashboard.admin.index')
@section('title', 'Download materials')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
        </div>
        @if($permissions['materials.store'])
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Materials <a data-bs-toggle="modal" data-bs-target="#addMaterial"><button type="button" class="btn btn-outline-primary">Add New study Material</button></a></h5> 
            </div>

            <div class="modal fade" id="addMaterial" tabindex="-1" aria-labelledby="addMaterial" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMaterial">Add new study Material</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Modal Body -->
                    <form action="{{ route('materials.store', ['p_id' => $training->id]) }}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="pb-2">
                                @csrf
                                <input type="hidden"  name="training_id" id="training_id" value="{{ $training->id }}">
                                <div class="form-group">
                                    <label>Select files</label>
                                    <input type="file" id="file" name="file[]" value="" class="form-control" multiple>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $material->title }}</td>
                            <td>{{ $material->created_at->format('d/m/Y') }}</td>
                            <td>{{ isset($material->program->p_name) ? $material->program->p_name : 'Training has been trashed'}}</td>
                            <td>
                                <div class="btn-group">
                                    @if(isset($material->program->id))
                                        @if($permissions['getmaterial'])
                                            <a data-toggle="tooltip" data-placement="top" title="Download Material"
                                                class="btn btn-info" href="{{ route('getmaterial', ['p_id'=>$material->program->id, 'filename'=> $material->file])}}"><i
                                                    class="fa fa-download"></i>
                                            </a>
                                        @endif
                                        @if($permissions['material.clone'])
                                            <a data-toggle="tooltip" data-placement="top" title="Clone Material"
                                            class="btn btn-primary" onclick="return confirm('Are you really sure?');" href="{{ route('materials.show', $material->id) }}"><i
                                                class="fa fa-clone"></i>
                                            </a>
                                        @endif
                                    @endif
                                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}
                                        @if($permissions['materials.destroy'])
                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete material"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection