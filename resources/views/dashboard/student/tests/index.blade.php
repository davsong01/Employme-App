@extends('dashboard.student.trainingsindex')

@section('title', 'My Tests')

@section('content')
<div class="container-fluid">

    <!-- Instructions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-title">
                <h4>All Tests</h4>
                <h6 class="text-danger">Please read the following carefully before you proceed to take a test:</h6>
                <ul>
                    <li>All Tests are timed. If you run out of time, the test will be submitted automatically.</li>
                    <li><strong>Certification</strong> tests are open-ended.</li>
                    <li><strong>Class Test</strong> are multiple choice.</li>
                    <li>You will be redirected to the result page after completion.</li>
                    <li>Ensure you have a stable internet connection.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-title">
                @include('layouts.partials.alerts')
                <h5>All Tests</h5>
            </div>

            @if($modules->isEmpty())
                <div class="text-danger">
                    <h6>No tests are available yet! Please check back later.</h6>
                </div>
            @endif
        </div>
    </div>

    <!-- Modules -->
    <div class="row">
        @foreach($modules as $module)

            @php
                $expiryPassed = !empty($module->expiry) && \Carbon\Carbon::parse($module->expiry)->isPast();

                $canStart = (
                    ($module->completed == 0) ||
                    ($module->redo == 1 && (empty($module->expiry) || !$expiryPassed))
                );
            @endphp

            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card bg-light h-100" style="border: 1px solid blue; border-radius: 5px;">
                    <div class="box bg-white text-center p-3 d-flex flex-column h-100">

                        <!-- Icon -->
                        <h1 class="font-light text-primary mb-3">
                            <i class="fa fa-list-alt"></i>
                        </h1>

                        <!-- Title -->
                        <div class="card-title mb-2">
                            <h5 class="d-flex justify-content-center align-items-center gap-2">
                                {{ $module->title }}
                                @if($module->redo == 1)
                                    <span class="badge bg-danger text-white">RETAKE</span>
                                @endif
                            </h5>
                        </div>

                        <!-- Status -->
                        <small class="mb-2">
                            @if($module->redo == 1)
                                @if(!$expiryPassed)
                                    <div class="text-danger fw-bold">RETAKE IN PROGRESS</div>
                                @else
                                    <div class="text-muted fw-bold">RETAKE EXPIRED</div>
                                @endif
                            @endif

                            @if(!empty($module->expiry))
                                <div class="fw-bold" style="color: {{ $expiryPassed ? 'red' : 'orange' }}">
                                    Resit Expiry: {{ \Carbon\Carbon::parse($module->expiry)->format('Y-m-d H:i') }}
                                </div>
                            @endif
                        </small>

                        <!-- Details -->
                        <h6 class="text-primary mb-2">Type: {{ $module->type }}</h6>
                        <p class="text-primary mb-2">No. of Questions: {{ $module->questions->count() }}</p>
                        <p class="text-primary mb-3">Time: {{ $module->time }} minutes</p>

                        <!-- Action -->
                        <div class="mt-auto">
                            @if($canStart)
                                <a href="{{ route('participants.tests.show', ['test' => $module->id, 'p_id' => $program->id]) }}">
                                    <button type="button" class="btn btn-outline-primary w-100"
                                        onclick="return confirm('Have you read the instructions above?');">
                                        {{ $module->redo == 1 ? 'Continue Retake' : 'Start Now!' }}
                                    </button>
                                </a>
                            @else
                                <a href="{{ route('tests.results', ['p_id' => $program->id]) }}">
                                    <button type="button" class="btn btn-outline-success w-100">
                                        Test Completed! View Details
                                    </button>
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        @endforeach
    </div>

</div>
@endsection