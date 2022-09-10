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
                <img src="{{ $training->image }}"
                    alt="program image">
                <!-- Hover Content -->
                
                
            </a>
            <div class="details">
                <p class="detailsp">{{ mb_strimwidth($training->p_name, 0, 46, "...") }}<br>
                {{ config('custom.default_currency') }}{{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount) }}
                    @if($training->close_earlybird == 1)
                        @if($training->e_amount > 0)<strong> |  Early Bird:</strong> {{ config('custom.default_currency') }}{{ $training->e_amount }}@endif
                    @endif
                <br>
                @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                    <strong class="closed" style="color:red">Registration Closed</strong>
                @else <br>
                @endif

                </p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection