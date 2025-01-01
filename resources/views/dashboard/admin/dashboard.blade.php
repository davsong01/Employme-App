@extends('dashboard.admin.index')
@section('title', 'Dashboard')
@section('content')

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    @include('layouts.partials.alerts')
    @if(checkRoleHas(['Admin'])) 
        <div class="row">
            <!-- Column -->
            
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('programs.index') }}">
                <div class="card card-hover">
                    <div class="box bg-cyan text-center">
                        <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                        <h6 class="text-white"><b>{{$programCount}}</b> Training(s)</h6>
                    </div>
                </div>
                </a>
            </div>
        
            <!-- Column -->
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('materials.index') }}">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white"><b>{{ isset($materialCount) ? $materialCount : ''}}</b> Material(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <!-- Column -->
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('payments.index') }}">
                <div class="card card-hover">
                    <div class="box bg-danger text-center">
                        <h1 class="font-light text-white"><i class="fas fa-dollar-sign"></i></h1>
                        <h6 class="text-white"><b> {{ isset($userowing) ? $userowing : ''}} </b> Owing</h6>
                    </div>
                </div>
                </a>
            </div>
            
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('users.index') }}">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fa fa-users"></i></h1>
                        <h6 class="text-white">{{ isset($userCount) ? $userCount : ''}} Student(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('pop.index') }}">
                <div class="card card-hover">
                    <div class="box bg-danger text-center">
                        <h1 class="font-light text-white"><i class="fa fa-check-circle"></i></h1>
                        <h6 class="text-white"><b> {{ isset($pending_payments) ? $pending_payments : ''}} </b> Pending Payments</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('complains.index') }}">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="far fa-comments"></i></h1>
                        <h6 class="text-white">CRM Tool</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('modules.index') }}">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fa fa-edit"></i></h1>
                        <h6 class="text-white">LMS Management</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('users.mail') }}">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fa fa-envelope"></i></h1>
                        <h6 class="text-white">Send Emails</h6>
                    </div>
                </div>
                </a>
            </div>
        </div> 
    @endif   
    <div class="row">     
        <!-- Column -->
        @if(checkRoleHas(['Facilitator','Grader']))
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('teachers.students', resolveAuthUser()->id) }}">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fa fa-users"></i></h1>
                        <h6 class="text-white">{{ $user->students_count ?? ''}} Student(s)</h6>
                    </div>
                </div>
                </a>
            </div>
             <div class="col-md-3 col-lg-3">
                <a href="{{ route('teachers.programs', resolveAuthUser()->id) }}"">
                <div class="card card-hover">
                    <div class="box bg-primary text-center">
                        <h1 class="font-light text-white"><i class="fa fa-link"></i></h1>
                        <h6 class="text-white"><b>{{ $user->programCount }}</b> Trainings(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('materials.index') }}">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white"><b>{{ $materialCount }}</b> Material(s)</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('complains.index') }}">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="far fa-comments"></i></h1>
                        <h6 class="text-white">CRM Tool</h6>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-md-3 col-lg-3">
                <a href="{{ route('modules.index') }}">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fa fa-edit"></i></h1>
                        <h6 class="text-white">LMS Management</h6>
                    </div>
                </div>
                </a>
            </div>
        @endif
        <!-- Column -->
        
    </div>
</div>
@endsection