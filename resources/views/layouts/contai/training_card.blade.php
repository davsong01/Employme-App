@php
    $today = date('Y-m-d');

    if ($training->p_end < $today || $training->close_registration == 1) {
        $badge = 'Past';
        $badgeColor = 'bg-danger'; // red
        $isDisabled = true;
    } elseif ($training->p_start > $today) {
        $badge = 'Upcoming';
        $badgeColor = 'bg-primary'; // blue
        $isDisabled = false;
    } else {
        $badge = 'Ongoing';
        $badgeColor = 'bg-success'; // green
        $isDisabled = false;
    }

    // EarlyBird percentage badge
    $earlyBirdPercent = ($training->e_amount > 0 && $training->early_bird_status == 0)
        ? number_format((($training->e_amount * 100)/$training->p_amount) - 100, 0)
        : null;
@endphp

<div class="col-lg-3 col-md-4 col-sm-6 mix">
    <div class="featured__item position-relative">

        {{-- Timing Badge --}}
        <span class="badge position-absolute top-0 start-0 m-2 text-white px-2 py-1 {{ $badgeColor }}" style="z-index: 10;">
            {{ $badge }}
        </span>

        {{-- EarlyBird Discount Badge --}}
        @if($earlyBirdPercent)
            <span class="badge position-absolute top-0 end-0 m-2 text-white px-2 py-1 bg-warning" style="z-index: 10;">
                {{ $earlyBirdPercent }}%
            </span>
        @endif

        {{-- Image / Link --}}
        @if($isDisabled)
            <div class="featured__item__pic set-bg" data-setbg="{{ $training->image ?? 'dummy.jpg' }}">
                {{-- Registration Closed Badge at Bottom Center --}}
                <span class="badge position-absolute bottom-0 start-50 translate-middle-x text-white px-3 py-2 bg-danger" 
                      style="z-index: 10; font-size: 0.85rem; border-radius: 0.25rem;">
                    Registration Closed
                </span>
            </div>
        @else
            <a href="{{ route('trainings', $training->slug) }}" target="_blank">
                <div class="featured__item__pic set-bg" data-setbg="{{ $training->image ?? 'dummy.jpg' }}"></div>
            </a>
        @endif

        {{-- Text --}}
        <div class="featured__item__text">
            <h6 style="min-height:60px">
                @if($isDisabled)
                    <span class="disabled-link">{{ $training->p_name }}</span>
                @else
                    <a href="{{ route('trainings', $training->slug) }}" target="_blank">{{ $training->p_name }}</a>
                @endif
            </h6>
            <h5>
                @if ($training->is_closed == 'no')
                    @if($training->e_amount > 0 && $training->early_bird_status == 0)
                        {{ $currency_symbol }}{{ number_format($exchange_rate * $training->e_amount) }}
                        <span class="discount-color">
                            &nbsp; {{ $currency_symbol }}
                            <span class="linethrough discount-color">{{ number_format($exchange_rate * $training->p_amount) }}</span>
                        </span>
                    @else
                        @if(!empty($training->price_range))
                            From {{ $currency_symbol.number_format($exchange_rate * $training->price_range['from']) }} 
                            to {{ $currency_symbol.number_format($exchange_rate * $training->price_range['to']) }}
                        @else
                            {{ $currency_symbol }}{{ number_format($exchange_rate * $training->p_amount) }}
                        @endif
                    @endif
                @else
                    <span class="text-danger">Closed Group Training</span>
                @endif
            </h5>
        </div>

    </div>
</div>