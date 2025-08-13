@extends('dashboard.admin.index')
@section('title', 'Edit Blacklisted Value')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Edit Blacklisted Value</h4>
                    </div>
                    <form action="{{ route('blacklist.update', $blacklist->id) }}" method="POST" class="pb-2">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-md-12">

                                {{-- Type --}}
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">-- Select Option --</option>
                                        <option value="global" {{ $blacklist->type === 'global' ? 'selected' : '' }}>Global</option>
                                    </select>
                                </div>
                                
                                {{-- Value --}}
                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <input type="text" class="form-control" name="value" value="{{ old('value', $blacklist->value) }}" required>
                                </div>

                                {{-- Reason --}}
                                <div class="form-group">
                                    <label for="reason">Reason</label>
                                    <textarea name="reason" class="form-control" cols="30" rows="5">{{ old('reason', $blacklist->reason) }}</textarea>
                                </div>

                                {{-- Status --}}
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="1" {{ old('status', $blacklist->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $blacklist->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="row">
                            <button type="submit" class="btn btn-success" style="width:100%">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
