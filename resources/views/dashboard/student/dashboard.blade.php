@extends('dashboard.student.index')
@section('content')
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <a href="{{ route('profiles.edit', Auth::user()->id) }}">
                <div class="card card-hover">
                    <div class="box bg-cyan text-center">
                        <h1 class="font-light text-white"><i class="fas fa-user-edit"></i></h1>
                        <h6 class="text-white">Welcome, {{ Auth::user()->name }}</h6>
                        <p class="text-white">Edit my profile</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
            <a href="{{ route('materials.index') }}">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white">{{ $currentUsermaterialsCount }}</h6>
                        <p class="text-white">Study materials</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-4">
            <a href="{{ route('payments.index') }}">
                <div class="card card-hover">
                    <div
                        class="box bg-{{ Auth::user()->paymentStatus == 0 ? 'danger' : 'success' }} text-center">
                        <h1 class="font-light text-white"><i class="far fa-money-bill-alt"></i></h1>
                        <h6 class="text-white">Payment Status </h6>
                        <p class="text-white">Paid: ₦{{ Auth::user()->t_amount }} ; Balance:
                            ₦{{ Auth::user()->balance }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    {{-- My programs --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title m-b-0">My Program(s)</h4>
                   
                    <div class="m-t-20">
                        <div class="d-flex no-block align-items-center">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="card card-hover">
                                    <div class="box bg-success text-center" style="border: 2px solid black; border-radius: 20px !important;">
                                        <h1 class="font-light text-white"><i class="fas fa-chalkboard-teacher"></i></h1>
                                        <h6 class="text-white">Current Training:</h6>
                                        <p class="text-white">2 Study Materials</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <div class="card card-hover">
                                    <div class="box bg-success text-center">
                                        <h1 class="font-light text-white"><i class="fas fa-chalkboard"></i></h1>
                                        <h6 class="text-white">Current Training:</h6>
                                        <p class="text-white">2 Study Materials</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title m-b-0">Training Progress</h4>
                    <div class="m-t-20">
                        <div class="d-flex no-block align-items-center">
                            <span>{{ $trainingProgress }}%</span>
                            <div class="ml-auto">
                                <span>100</span>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped" role="progressbar"
                                style="width: {{ $trainingProgress }}%" aria-valuenow="10" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-title">
                    <h2 style="text-align: center;; color:green">All {{ config('app.name') }} Training
                        Schedules
                        <hr>
                    </h2>
                </div>
                <div class="card-body">
                    {!! $calendar->calendar() !!}
                    {!! $calendar->script() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection