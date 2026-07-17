@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Retrieval Tool</span>
                            <h1 class="h3 mb-2">Retrieve User Details</h1>
                            <p class="text-muted mb-0">Find a person by email, name, or phone number.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                @include('layouts.partials.alerts')
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <form action="{{ route('details.index') }}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="type" class="form-label">Select Retrieval Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="email">Email</option>
                                    <option value="name">Name</option>
                                    <option value="phone">Phone Number</option>
                                </select>
                                <div class="text-danger small mt-1">{{ $errors->first('type') }}</div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="program_id" class="form-label">Select Training *</label>
                                <select name="program_id" id="program_id" class="form-select" required>
                                    <option value=""></option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id }}">{{$program->p_name}}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger small mt-1">{{ $errors->first('program_id') }}</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="submit" class="btn btn-primary w-100">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
