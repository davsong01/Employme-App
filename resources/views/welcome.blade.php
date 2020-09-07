@extends('layouts.frontend')
@section('title')
    {{ config('app.name') }}
@endsection
@section('content')
<div class="products-catagories-area clearfix">
    <div class="amado-pro-catagory clearfix">
        @foreach($trainings as $training)
        <!-- Single Catagory -->
        <div class="single-products-catagory clearfix">
            <a href="{{ route('trainings', $training->id ) }}">
                <img src="{{ asset('ecommerce/img/bg-img/a.jpg') }}"
                    alt="program image">
                    
                <!-- Hover Content -->
                <div class="hover-content">
                    <div class="line"></div>
                    <h4>{{ $training->p_name }}</h4><br>
                    <p><strong>Price:</strong> {{ config('custom.default_currency') }}{{ $training->p_amount }}@if($training->e_amount > 0)<strong> |  Early Bird:</strong> {{ config('custom.default_currency') }}{{ $training->e_amount }}@endif</p>
                </div>
            </a>
        </div>
        
        @endforeach
    
    </div>
</div>
@endsection