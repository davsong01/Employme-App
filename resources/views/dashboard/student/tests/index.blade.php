@extends('dashboard.student.trainingsindex')

@section('title', 'My Tests')

@section('content')
<div class="container-fluid">
    <!-- Instructions Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-title">
                <h4>All Tests</h4>
                <h6 class="text-danger">Please read the following carefully before you proceed to take a test:</h6>
                <ul>
                    <li>All Tests are timed. If you run out of time, the test will be submitted automatically with all answered questions.</li>
                    <li>Tests with Type: <strong>Certification</strong> are open-ended. You will be required to type in your input.</li>
                    <li>Tests with Type: <strong>Class Test</strong> are multiple choice. You will be required to select the correct option.</li>
                    <li>When a test ends, you will be redirected to the result page to view your score.</li>
                    <li>Ensure you have a stable internet connection while taking a test.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alerts and Available Tests Section -->
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

    <!-- Test Modules Section -->
    <div class="row">
        @foreach($modules as $module)
    @php
        $expiryPassed = !empty($module->expiry) && \Carbon\Carbon::parse($module->expiry)->isPast();
    @endphp

    <div class="col-md-4 col-lg-4 mb-3">
        <div class="card bg-light h-100" style="border: 1px solid blue; border-radius: 5px;">
            <div class="box bg-white text-center p-3 d-flex flex-column h-100">
                <!-- Icon -->
                <h1 class="font-light text-primary mb-3">
                    <i class="fa fa-list-alt"></i>
                </h1>

                <!-- Title and Retake Badge -->
                <div class="card-title mb-2">
                    <h5 style="display: inline-flex; align-items: center; justify-content: center; gap: 10px;">
                        {{ $module->title }}
                        @if($module->redo == 1)
                            <span class="badge bg-danger text-white">RETAKE</span>
                        @endif
                    </h5>
                </div>

                <!-- Expiry / Status -->
                <small class="mb-2" style="color: {{ $expiryPassed ? 'red' : 'orange' }};">
                    @if($module->redo == 1)
                        <div class="text-danger">RETAKE IN PROGRESS</div>
                    @endif

                    @if(!empty($module->expiry))
                        <div style="font-weight: bold;">(RESIT Expiry: {{ \Carbon\Carbon::parse($module->expiry)->format('Y-m-d H:i') }})</div>
                    @else
                        <div style="visibility: hidden;">(No Expiry)</div>
                    @endif
                </small>

                <!-- Module Details -->
                <h6 class="text-primary mb-2">Type: {{ $module->type }}</h6>
                <p class="text-primary mb-2">No. of Questions: {{ $module->questions->count() }}</p>
                <p class="text-primary mb-3">Time: {{ $module->time }} minutes</p>

                <!-- Action Buttons -->
                {{-- {{ dd($module->completed, $module->redo, $module->expiry, $expiryPassed) }} --}}
                <div class="mt-auto">
                    @if(($module->completed == 0 ) && (empty($module->expiry) || !$expiryPassed))
                        <a href="{{ route('participants.tests.show', ['test' => $module->id, 'p_id' => $program->id]) }}">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="return confirm('Have you read the instructions above?');">
                                Start Now!
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

</div>
@endsection
