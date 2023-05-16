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
        <div class="col-md-3 col-lg-3">
            <a href="{{ route('payments.index') }}">
                <div class="card card-hover">
                    <div
                        class="box bg-{{ $balance > 0 ? 'danger' : 'success' }} text-center">
                        <h1 class="font-light text-white"><i class="far fa-money-bill-alt"></i></h1>
                        <h6 class="text-white">Payment Status {{ $currency_symbol }}{{ number_format($balance)  }} </h6>
                        <p class="text-white">Paid: {{ $paid }} ; Balance:
                            {{ $balance }} </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-lg-3">
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
        @if($program->hasmock == 1)
        <div class="col-md-3 col-lg-3">
            <a href="{{ route('mocks.index', ['p_id' => $program->id])}}">
                <div class="card card-hover">
                    <div class="box bg-warning text-center">
                        <h1 class="font-light text-white"><i class="fa fa-chalkboard"></i></h1>
                        <h6 class="text-white">&nbsp;</h6>
                        <p class="text-white">Pre Class Tests</p>
                    </div>
                </div>
            </a>
        </div>
        @endif
        <div class="col-md-3 col-lg-3">
            <a href="{{ route('tests.index', ['p_id'=>$program->id])}}">
                <div class="card card-hover">
                    <div class="box bg-success text-center">
                        <h1 class="font-light text-white"><i class="fas fa-question"></i></h1>
                        <h6 class="text-white">&nbsp;</h6>
                        <p class="text-white">Post class Tests</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
  
    @if(!$program->off_season)
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
    @endif
    @if(auth()->user()->facilitator_id)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title m-b-0">Your Facilitator</h1> <br><br>
                    <div class="row pt-2">
                        <div class="col-md-2">
                            <div class="d-flex no-block align-items-center">
                                <img src="{{ asset('profiles/'. auth()->user()->facilitator->profile_picture )}}" alt="{{ auth()->user()->facilitator->profile_picture }}" class="rounded-circle" width="150"
                                height="150" style="margin: auto;">
                               
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div>
                               
                                    <b>Name: </b>{{ auth()->user()->facilitator->name }} <br>
                                    <b>Email: </b>{{ auth()->user()->facilitator->email }} <br>
                                    <b>Phone: </b>{{ auth()->user()->facilitator->t_phone }} <br>
                                    <b>Profile: </b> <br> <span style="padding-right:20px">{!! auth()->user()->facilitator->profile !!}</span> 
                                
                            </div>
                        </div>
                        
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
    @endif
    
</div>
@endsection