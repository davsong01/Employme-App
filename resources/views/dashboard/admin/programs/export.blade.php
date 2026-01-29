@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Export Program Details</h4>
                    </div> 
                    <form action="{{route('process.programs.export.participants')}}" method="POST" enctype="multipart/form-data" class="pb-2">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Program</label>
                                    <select name="program_ids[]" class="select2 form-control" multiple required data-placeholder="Select Programs">
                                        @foreach($programs as $pro)
                                            <option value="{{ $pro->id }}">
                                                {{ $pro->p_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="from" value="{{ old('from')}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="to" value="{{ old('to')}}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select name="payment_type" class="form-control" id="payment_type">
                                        <option value="" selected>All</option>
                                        <option value="full" {{ old('payment_type') == 'full' ? 'selected' : '' }}>Full</option>
                                        <option value="part" {{ old('payment_type') == 'part' ? 'selected' : '' }}>Part</option>
                                        <option value="earlybird" {{ old('payment_type') == 'earlybird' ? 'selected' : '' }}>EarlyBird</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remove Duplicates Participants?</label>
                                    <select name="remove_duplicate" class="form-control" id="remove_duplicate">
                                        <option value="no" selected>No</option>
                                        <option value="yes" {{ old('remove_duplicate') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <div class="col-md-12">
                        <button name="submit" class="btn btn-primary" style="width:100%">Submit</button>
                    </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection