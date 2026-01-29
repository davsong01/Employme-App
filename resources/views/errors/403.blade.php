@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Page Expired
@endsection
@section('content')
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
        <div class="checkout__form text-center">
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Closed for Maintenance</h1>
                        <p>{!! $exception->getMessage() ?: 'We are currently performing scheduled maintenance. Please check back soon<.' !!}</p>
                        <p>
                        {{-- <a href="{{ url()->previous() }}" class="btn btn-primary">REFRESH</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection