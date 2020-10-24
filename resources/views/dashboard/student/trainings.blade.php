@extends('dashboard.student.trainingsindex')
@section('content')
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
                <h2 style="color:green; text-align:center; padding:20px">{{ strtoupper($program->p_name) }}</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-6">
            <a href="{{ route('materials.index', ['p_id'=> $program->id]) }}">
                <div class="card card-hover">
                    <div class="box bg-info text-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white">{{ $materialsCount }}</h6>
                        <p class="text-white">Study materials</p>
                    </div>
                </div>
            </a>
        </div>
                
        <div class="col-md-6 col-lg-6">
            <a href="{{ route('payments.index') }}">
                <div class="card card-hover">
                    <div
                        class="box bg-{{ $balance > 0 ? 'danger' : 'success' }} text-center">
                        <h1 class="font-light text-white"><i class="far fa-money-bill-alt"></i></h1>
                        <h6 class="text-white">Payment Status </h6>
                        <p class="text-white">Paid: {{ config('custom.default_currency') . $paid}} ; Balance:
                            {{ config('custom.default_currency') . $balance }} </p>
                    </div>
                </div>
            </a>
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