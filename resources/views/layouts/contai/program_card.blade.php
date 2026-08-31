@php
    $today = date('Y-m-d');

    $isDisabled = ($item->p_end < $today || ($item->is_closed ?? 'no') == 'yes');

    if ($item->p_end < $today || ($item->is_closed ?? 'no') == 'yes') {
        $status = 'Past';
        $statusColor = 'bg-danger';
    } elseif ($item->p_start > $today) {
        $status = 'Upcoming';
        $statusColor = 'bg-primary';
    } else {
        $status = 'Ongoing';
        $statusColor = 'bg-success';
    }

    $route = $type === 'package'
        ? route('show.packages', $item->slug)
        : route('trainings', $item->slug);

    $hasDiscount = $item->isEarlyBirdActive() && $item->e_amount > 0;

    $discountPercent = $hasDiscount
        ? number_format((($item->e_amount * 100) / $item->p_amount) - 100, 0)
        : null;
@endphp


<div class="col-lg-3 col-md-4 col-sm-6 mix">
    <div class="featured__item position-relative">

        {{-- Badge (Discount OR Status) --}}
        @if($hasDiscount)
            <span class="badge position-absolute top-0 start-0 m-2 text-white px-2 py-1 bg-danger" style="z-index:10;">
                {{ $discountPercent }}% OFF
            </span>
        @else
            <span class="badge position-absolute top-0 start-0 m-2 text-white px-2 py-1 {{ $statusColor }}" style="z-index:10;">
                {{ $status }}
            </span>
        @endif


        {{-- Image --}}
        @if($isDisabled)
            <div class="featured__item__pic set-bg" data-setbg="{{ $item->image ?? 'dummy.jpg' }}">
                <span class="badge position-absolute bottom-0 start-50 translate-middle-x text-white px-3 py-2 bg-danger"
                      style="z-index:10; font-size:0.85rem;">
                    Registration Closed
                </span>
            </div>
        @else
            <a href="{{ $route }}" target="_blank">
                <div class="featured__item__pic set-bg" data-setbg="{{ $item->image ?? 'dummy.jpg' }}"></div>
            </a>
        @endif


        {{-- Text --}}
        <div class="featured__item__text">

            <h6 style="min-height:60px">
                @if($isDisabled)
                    <span class="disabled-link">{{ $item->p_name }}</span>
                @else
                    <a href="{{ $route }}" target="_blank">{{ $item->p_name }}</a>
                @endif
            </h6>

            <h5>

                {{-- Closed Group Training (trainings only) --}}
                @if(isset($item->is_closed) && $item->is_closed == 'yes')

                    <span class="text-danger">Closed Group Training</span>

                @else

                    @if($hasDiscount)

                        {{ $currency_symbol }}{{ number_format($exchange_rate * $item->e_amount) }}

                        <span class="discount-color">
                            &nbsp; {{ $currency_symbol }}
                            <span class="linethrough discount-color">
                                {{ number_format($exchange_rate * $item->p_amount) }}
                            </span>
                        </span>

                    @else

                        {{ $currency_symbol }}{{ number_format($exchange_rate * $item->p_amount) }}

                    @endif

                @endif

            </h5>

        </div>

    </div>
</div>
