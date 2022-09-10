@extends('dashboard.student.index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Column -->
        <div class="col-md-12 col-lg-12">
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
    </div>

    <div class="row">
        <!-- Column -->
        <div class="col-md-12 col-lg-12">
            <div class="card-body">
                <h2 style="text-align: center; color:green">My Trainings (Click to Access)</h2>
                @foreach($thisusertransactions as $details)
                    <div class="col-md-12 col-lg-12">
                        <a href="{{ route('trainings.show', ['p_id' => $details->p_id]) }}">
                            <div class="card card-hover">
                                <div class="box bg-success text-center">
                                    <h1 class="font-light text-white"><i class="fas fa-chalkboard-teacher"></i></h1>
                                    <h6 class="text-white">{{ $details->p_name }}</h6>
                                    <p class="text-white">{{ $details->modules }} Enabled Module Tests | {{ $details->materials }} Materials</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Column -->
    </div>
</div>
@endsection