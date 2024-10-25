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
                        <h1>Page Expired</h1>
                        <p>Sorry, your session has expired. Please click the button below to refresh.</p>
                        <a href="{{ url()->previous() }}" class="btn btn-primary">REFRESH</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection