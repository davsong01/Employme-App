@extends('layouts.frontend')
@section('title')
    {{ config('app.name') }}
@endsection
@section ('status')
class="active"
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
                        <p><strong>Price:</strong> {{ config('custom.default_currency') }}{{ $training->p_amount }}
                            @if($training->close_earlybird == 1)
                                @if($training->e_amount > 0)<strong> |  Early Bird:</strong> {{ config('custom.default_currency') }}{{ $training->e_amount }}@endif
                        </p>
                           
                    @endif
                    @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                    <p class="closed" style="color:red">Registration Closed</p>
                    @endif
                    
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection