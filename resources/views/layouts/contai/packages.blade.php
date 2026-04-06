@extends('layouts.contai.app')

@section('title')
    {{ config('app.name') }}
@endsection


@section('content')

<section>
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>All Packages</h2>
                </div>
            </div>
        </div>


        {{-- UPCOMING PACKAGES --}}
        @if($upcomingPackages->count())

            <div class="row mb-3 mt-4">
                <div class="col-lg-12">
                    <h3>Upcoming</h3>
                </div>
            </div>

            <div class="row featured__filter">
                @foreach($upcomingPackages as $package)
                    @include('layouts.contai.program_card', [
                        'item' => $package,
                        'type' => 'package'
                    ])
                @endforeach
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    {!! $upcomingPackages->appends(request()->except('upcoming_page'))->links() !!}
                </div>
            </div>

        @endif



        {{-- Ongoing TrainingsS --}}
        @if($ongoingPackages->count())

            <div class="row mb-3 mt-5">
                <div class="col-lg-12">
                    <h3>Ongoing</h3>
                </div>
            </div>

            <div class="row featured__filter">
                @foreach($ongoingPackages as $package)
                    @include('layouts.contai.program_card', [
                        'item' => $package,
                        'type' => 'package'
                    ])
                @endforeach
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    {!! $ongoingPackages->appends(request()->except('ongoing_page'))->links() !!}
                </div>
            </div>

        @endif



        {{-- PAST PACKAGES --}}
        @if($pastPackages->count())

            <div class="row mb-3 mt-5">
                <div class="col-lg-12">
                    <h3>Past</h3>
                </div>
            </div>

            <div class="row featured__filter">
                @foreach($pastPackages as $package)
                    @include('layouts.contai.program_card', [
                        'item' => $package,
                        'type' => 'package'
                    ])
                @endforeach
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    {!! $pastPackages->appends(request()->except('past_page'))->links() !!}
                </div>
            </div>

        @endif


    </div>
</section>

@endsection