@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }}
@endsection
@section('content')
<style>
    @media (max-width: 576px) {
        .pagination {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .pagination li {
            white-space: nowrap;
        }
    }
</style>
@if($discounts->count() > 0)
<!-- Earlybird Rush -->
<section class="from-blog spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>EarlyBird Rush</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="product__discount__slider owl-carousel">
                @foreach($discounts as $discount)
                <div class="col-lg-4">
                    <div class="product__discount__item">
                        <a href="{{ route('trainings', $discount->slug ) }}" target="_blank">
                            <div class="product__discount__item__pic set-bg" data-setbg="{{ $discount->image ?? 'dummy.jpg'}}">
                                <div class="product__discount__percent">
                                    {{ number_format((($discount->e_amount * 100)/$discount->p_amount) - 100, 0) }}%
                                </div>
                            </div>
                        </a>
                        <div class="product__discount__item__text">
                            <a href="{{ route('trainings', $discount->slug ) }}" target="_blank">
                                <h5 style="color: #c2c2c2">{{ $discount->p_name }}</h5>
                            </a>
                            @if ($discount->is_closed == 'no')
                                <div class="product__item__price">
                                    {{ $currency_symbol . number_format($exchange_rate * $discount->e_amount) }}
                                    <span>{{ $currency_symbol . number_format($exchange_rate * $discount->p_amount) }}</span>
                                </div>
                            @else 
                                <div class="product__item__price" style="color:red">Closed Group Training</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Trainings Section -->
<section class="">
    <div class="container mt-4">
        {{-- Upcoming Trainings --}}
        @if($upcomingTrainings->count() > 0)
        <div class="row mb-3">
            <div class="col-lg-12"><h3>Upcoming Trainings</h3></div>
        </div>
        <div class="row">
            @foreach($upcomingTrainings as $training)
                @include('layouts.contai.training_card', ['training' => $training])
            @endforeach
        </div>
        <div class="row mt-3">
            <div class="col-lg-12">
                {!! $upcomingTrainings->appends(request()->except('upcoming_page'))->links() !!}
            </div>
        </div>
        @endif

        {{-- Ongoing Trainings --}}
        @if($ongoingTrainings->count() > 0)
        <div class="row mb-3 mt-5">
            <div class="col-lg-12"><h3>Ongoing Trainings</h3></div>
        </div>
        <div class="row">
            @foreach($ongoingTrainings as $training)
                @include('layouts.contai.training_card', ['training' => $training])
            @endforeach
        </div>
        <div class="row mt-3">
            <div class="col-lg-12">
                {!! $ongoingTrainings->appends(request()->except('ongoing_page'))->links() !!}
            </div>
        </div>
        @endif

        {{-- Past Trainings --}}
        @if($pastTrainings->count() > 0)
        <div class="row mb-3 mt-5">
            <div class="col-lg-12"><h3>Past Trainings</h3></div>
        </div>
        <div class="row">
            @foreach($pastTrainings as $training)
                @include('layouts.contai.training_card', ['training' => $training])
            @endforeach
        </div>
        <div class="row mt-3 mb-4">
            <div class="col-lg-12">
                {!! $pastTrainings->appends(request()->except('past_page'))->links() !!}
            </div>
        </div>
        @endif

    </div>
</section>

@endsection