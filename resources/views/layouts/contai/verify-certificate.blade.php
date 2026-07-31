@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Verify Certificate
@endsection

@section('pagetitle')
Verify Certificate
@endsection
@section('content')
@php
    $certificateInputValue = request('certificate_number');
@endphp
<section class="checkout spad" style="padding-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
    
        @if($response)
            <div class="card verification-card">
                <div class="card-body">
                    <h5 class="certificate-title mb-4">Certificate Verification Status</h5>
                    {{-- {{ dd($response) }} --}}
                    @if(!$response['status'])
                        <div class="mb-3">
                            <strong>Certification Status:</strong>  <br>
                            <span class="status-not-certified">
                                <strong>Status:</strong> False <br>
                                <strong>Message:</strong> {{ $response['message'] }} <br>
                            </span>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('verify.certificate') }}" class="btn btn-primary">Verify Another Certificate</a>
                        </div>
                    @else
                        <div class="mb-3">
                            <strong>Certificate Number:</strong> <span>{{ $response['certificate_number'] }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Certification Status:</strong> 
                            <span class="status-certified">
                                {{ strtoupper($response['certification_status']) }}
                            </span>
                        </div> 
                        <div class="mb-3">
                            <strong>Training Program:</strong> <span>{{ $response['training'] }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Certificate Owner:</strong> <span>{{ $response['owner'] }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Certified On:</strong> <span>{{ $response['certified_on']->format('F j, Y') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Score Obtained:</strong> <span>{{ $response['score_obtained'] }} / {{ $response['score_obtainable'] }}</span>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('verify.certificate') }}" class="btn btn-primary">Verify Another Certificate</a>
                        </div>
                    @endif
                </div>
            </div>
        @else  
            <div class="checkout__form">
                <h4>Please Enter a Certificate Number</h4>
                <form action="{{ route('verify.certificate') }}" method="GET">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="checkout__input">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <label for="certificate_number" style="margin: 0;">Certificate Number<span>*</span></label>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="certificate_number"
                                            name="certificate_number"
                                            value="{{ $certificateInputValue }}"
                                            required
                                            autocomplete="off"
                                        >
                                        <small id="certificate-number-hint" class="text-muted d-block mt-2">
                                            Use only letters, numbers, and hyphens.
                                        </small>
                                        <small id="certificate-number-error" class="text-danger d-none d-block mt-2"></small>
                                        @if(!empty($validationError))
                                            <small class="text-danger d-block mt-2">{{ $validationError }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <button type="submit" class="site-btn checkout-button">Check Status</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</section>
<script>
    (function () {
        const input = document.getElementById('certificate_number');
        const error = document.getElementById('certificate-number-error');
        const form = input ? input.closest('form') : null;
        const allowed = /^[A-Za-z0-9-]*$/;

        if (!input || !error || !form) {
            return;
        }

        const showError = (message) => {
            error.textContent = message;
            error.classList.remove('d-none');
        };

        const clearError = () => {
            error.textContent = '';
            error.classList.add('d-none');
        };

        const validate = () => {
            const value = input.value.trim();

            if (!value) {
                clearError();
                return true;
            }

            if (!allowed.test(value)) {
                showError('Please remove pasted symbols or emojis. Only letters, numbers, and hyphens are allowed.');
                return false;
            }

            clearError();
            return true;
        };

        input.addEventListener('input', validate);
        input.addEventListener('paste', () => {
            window.setTimeout(validate, 0);
        });

        form.addEventListener('submit', function (event) {
            if (!validate()) {
                event.preventDefault();
            }
        });
    })();
</script>
@endsection
