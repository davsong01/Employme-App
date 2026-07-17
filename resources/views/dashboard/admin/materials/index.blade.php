<?php
    $permissionsToCheck = ['materials.store','material.clone','materials.download','materials.destroy','getmaterial'];
    $permissions = checkTrainingHasPermissions($training->id, $permissionsToCheck);
    $totalMaterials = method_exists($materials, 'total') ? $materials->total() : $materials->count();
?>
@extends('dashboard.admin.index')
@section('title', 'Download materials')
@section('content')

<div class="container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm crm-hero">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Admin Desk</span>
                            <h1 class="h3 fw-bold mb-2">Study Materials</h1>
                            <p class="text-muted mb-0">Upload, download, and clone training materials from a cleaner admin workspace.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $totalMaterials }} materials</span>
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">{{ $training->p_name ?? 'Training' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.alerts')
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-3">
                <div class="text-muted small">Files attached to this training are listed below.</div>
                @if($permissions['materials.store'])
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterial">Add New Study Material</button>
                @endif
            </div>

            @if($permissions['materials.store'])
                <div class="modal fade" id="addMaterial" tabindex="-1" aria-labelledby="addMaterialLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMaterialLabel">Add new study Material</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('materials.store', ['p_id' => $training->id]) }}" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    @csrf
                                    <input type="hidden" name="training_id" id="training_id" value="{{ $training->id }}">
                                    <div class="mb-3">
                                        <label class="form-label">Select files</label>
                                        <input type="file" id="file" name="file[]" class="form-control" multiple>
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

            <div class="table-responsive">
                <table id="zero_config" class="table table-hover align-middle crm-table crm-mobile-stack mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                        <tr>
                            <td data-label="#">
                                {{ $i++ }}
                            </td>
                            <td data-label="Title">{{ $material->title }}</td>
                            <td data-label="Date Uploaded">{{ $material->created_at->format('d/m/Y') }}</td>
                            <td data-label="Program/Class">{{ isset($material->program->p_name) ? $material->program->p_name : 'Training has been trashed' }}</td>
                            <td class="text-end" data-label="Actions">
                                <div class="d-none d-md-inline-flex justify-content-end gap-1">
                                    @if(isset($material->program->id))
                                        @if($permissions['getmaterial'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Download Material" class="btn btn-outline-primary btn-sm" href="{{ route('getmaterial', ['p_id' => $material->program->id, 'filename' => $material->file]) }}">
                                                <i class="fa fa-download"></i>
                                            </a>
                                        @endif
                                        @if($permissions['material.clone'])
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Clone Material" class="btn btn-outline-info btn-sm" onclick="return confirm('Are you really sure?');" href="{{ route('materials.show', $material->id) }}">
                                                <i class="fa fa-clone"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @if($permissions['materials.destroy'])
                                        <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-placement="top" title="Delete material">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="dropdown d-inline-flex d-md-none">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        More
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if(isset($material->program->id))
                                            @if($permissions['getmaterial'])
                                                <li><a class="dropdown-item" href="{{ route('getmaterial', ['p_id' => $material->program->id, 'filename' => $material->file]) }}"><i class="fa fa-download me-2"></i>Download Material</a></li>
                                            @endif
                                            @if($permissions['material.clone'])
                                                <li><a class="dropdown-item" onclick="return confirm('Are you really sure?');" href="{{ route('materials.show', $material->id) }}"><i class="fa fa-clone me-2"></i>Clone Material</a></li>
                                            @endif
                                        @endif
                                        @if($permissions['materials.destroy'])
                                            <li>
                                                <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');" class="m-0">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash me-2"></i>Delete Material
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
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
