@extends('dashboard.student.trainingsindex')
@section('content')
<div class="container-fluid">
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-2 mb-3">Training Overview</span>
                            <h2 class="mb-2">{{ strtoupper($program->p_name) }}</h2>
                            <p class="text-muted mb-0">Your program progress, materials and test access all in one place.</p>
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
        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('participants.payments.index') }}">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="box bg-{{ $balance > 0 ? 'danger' : 'success' }} text-center rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                        <h1 class="font-light text-white"><i class="far fa-money-bill-alt"></i></h1>
                        <h6 class="text-white mb-2">Payment Status {{ $currency_symbol }}{{ number_format($balance)  }}</h6>
                        <p class="text-white mb-0">Paid: {{ $paid }}; Balance: {{ $balance }}</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('participants.materials.index', ['p_id'=> $program->id]) }}">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="box bg-info text-center rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                        <h1 class="font-light text-white"><i class="fas fa-download"></i></h1>
                        <h6 class="text-white mb-2">{{ $materialsCount }}</h6>
                        <p class="text-white mb-0">Study materials</p>
                    </div>
                </div>
            </a>
        </div>
        @if($program->hasmock == 1)
        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('participants.mocks.index', ['p_id' => $program->id])}}">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="box bg-warning text-center rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                        <h1 class="font-light text-white"><i class="fa fa-chalkboard"></i></h1>
                        <h6 class="text-white mb-2">&nbsp;</h6>
                        <p class="text-white mb-0">Pre Class Tests</p>
                    </div>
                </div>
            </a>
        </div>
        @endif
        <div class="col-12 col-md-6 col-xl-3">
            <a href="{{ route('participants.tests.index', ['p_id'=>$program->id])}}">
                <div class="card card-hover h-100 border-0 shadow-sm">
                    <div class="box bg-success text-center rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                        <h1 class="font-light text-white"><i class="fas fa-question"></i></h1>
                        <h6 class="text-white mb-2">&nbsp;</h6>
                        <p class="text-white mb-0">Post class Tests</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
  
    {{-- @if(!$program->off_season)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Training Progress</h4>
                    <div class="mt-4">
                        <div class="d-flex no-block align-items-center">
                            <span>{{ $trainingProgress }}%</span>
                            <div class="ms-auto">
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
    </div> --}}
    {{-- @endif --}}
    @if(resolveAuthUser()->facilitator_id)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title mb-0">Your Facilitator</h1>
                    <div class="row pt-3">
                        <div class="col-md-2">
                            <div class="d-flex no-block align-items-center">
                                <img src="{{ asset('profiles/'. resolveAuthUser()->facilitator->profile_picture )}}" alt="{{ resolveAuthUser()->facilitator->profile_picture }}" class="rounded-circle" width="150"
                                height="150" style="margin: auto;">
                               
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div>
                        
                                    <b>Name: </b>{{ resolveAuthUser()->facilitator->name }} <br>
                                    <b>Email: </b>{{ resolveAuthUser()->facilitator->email }} <br>
                                    <b>Phone: </b>{{ resolveAuthUser()->facilitator->phone }} <br>
                                    <b>Profile: </b> <br> <span style="padding-right:20px">{!! resolveAuthUser()->facilitator->profile !!}</span> 
                                
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
