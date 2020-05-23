@extends('dashboard.admin.index')
@section('title', 'Dashboard')
@section('content')

<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Start Page Content -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-cyan text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                    <h6 class="text-white"><b>{{$programCount}}</b> Trainings</h6>
         
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                    <h6 class="text-white"><b>{{$materialCount}}</b> Materials</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white"><b> {{$userowing}} </b> Owing</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3">
            <div class="card card-hover">
                <div class="box bg-warning text-center">
                    <h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>
                    <h6 class="text-white">{{$userCount}} Students</h6>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-title"><h2 style="text-align: center;; color:green">Training Schedule<hr></h2></div>
            <div class="card-body">
                    {!! $calendar->calendar() !!}
                    {!! $calendar->script() !!}
            </div>
        </div>
    </div>
</div>
    <!-- BEGIN MODAL -->


@endsection