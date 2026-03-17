@php
    $resitStatus = $user->training_result->certification_test_resit_status ?? null;
    $expiry = $user->training_result->certification_test_resit_expiry ?? null;
    $parsedDate = $expiry ? \Carbon\Carbon::parse($expiry) : null;
    $canOverride = $permissions['results.destroy'] ?? false;
@endphp

@if(!is_null($resitStatus))
    <br>

    {{-- STATUS: NOT STARTED / FAILED / RESETTABLE --}}
    @if(in_array($resitStatus, [0, 2]))
        @if($canOverride)
            @include('dashboard.admin.results.resit-button', [
                'user' => $user,
                'btnClass' => 'btn-danger',
                'label' => 'Enable Resit'
            ])
        @endif
    @endif

    {{-- STATUS: IN PROGRESS --}}
    @if($resitStatus == 1 && $parsedDate)
        @if($parsedDate->isFuture())
            <small class="resit-status d-block text-danger">
                <strong>Resit In Progress!</strong>
            </small>

            <small class="resit-status d-block text-danger">
                <strong>Expires on:</strong> {{ $parsedDate->format('j M, Y') }}
            </small>

        @else
            {{-- ⚠️ This is the "should not happen" case --}}
            <small class="resit-status d-block text-warning">
                <strong>⚠️ Resit expired but still marked as active</strong>
            </small>

            <small class="resit-status d-block">
                <strong>Expired on:</strong> {{ $parsedDate->format('j M, Y') }}
            </small>

            @if($canOverride)
                @include('dashboard.admin.results.resit-button', [
                    'user' => $user,
                    'btnClass' => 'btn-dark',
                    'label' => 'Re-enable Resit'
                ])
            @endif
        @endif

    @endif
@endif