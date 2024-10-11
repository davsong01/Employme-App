@extends('dashboard.company.index')
@section('css')
<style>
    a{
        text-decoration: none !important;
    }

    .accounts{
        min-height: 270px;
    }

    .badge {
        display: inline !important;
        padding: 10px;
    }
</style>

@endsection
@section('title', 'All Participants')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                @include('layouts.partials.alerts')
                <div class="card-header">
                    <div class="card-title">
                        <h5>
                            All Participants
                            <span class="badge bg-success ms-2">
                                <span class="transaction-count">{{ $records }}</span>
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $currentStatus = request('status');
                        @endphp
                    
                        <div class="mt-4">
                            <form class="row g-3 search-form" method="GET" action="{{ route('company.participants') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <div class="col-md-4 mb-2">
                                    <label for="program_id" class="form-label">Training</label>
                                    <select name="program_id" id="program_id" class="form-control">
                                        <option value="">Select Training</option>
                                        @foreach($allPrograms as $training)
                                            <option value="{{ $training->id }}">{{ $training->p_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ request('name') }}">
                                </div>
                            
                                <div class="col-md-2 mb-2">
                                    <label for="staffID" class="form-label">Staff ID</label>
                                    <input type="text" class="form-control" name="staffID" id="staffID" placeholder="Enter Staff ID" value="{{ request('staffID') }}">
                                </div>
                                
                                <div class="col-md-2 mb-2">
                                    <label for="" class="form-label" style="color:transparent;display:block">Name</label>
                                    <button type="submit" class="btn btn-primary mb-2 rounded">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Details</th>
                            <th>Trainings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $user->name }} <br>
                                @if($user->staffID)
                                <strong>Staff ID: </strong>{{$user->staffID}} <br>
                                @endif
                                @if($user->t_phone)
                                <strong>Phone: </strong>{{ $user->t_phone}}
                                @endif
                            </td>

                            <b style="display:none">{{ $count = 1 }}</b>
                            <td>
                                @foreach($user->programs as $prog)
                                    @if(in_array($prog->id, $programs))
                                        <small style="color:green">{{ $count ++ }}.
                                        {{ $prog->p_name }} <br></small>
                                    @endif
                                @endforeach
                            </td>
                            @endforeach
                    </tbody>
                </table>
            </div>
            {{  $users->appends($_GET)->links()  }}

        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>
@endsection