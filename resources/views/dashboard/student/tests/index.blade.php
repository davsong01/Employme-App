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
            <div class="col-md-4 col-lg-4 mb-3">
                <div class="card bg-light" style="border-radius: 8px;">
                    <div class="box bg-white text-center p-3" style="border: 1px solid blue; border-radius: 5px;">
                        <h1 class="font-light text-primary">
                            <i class="fa fa-list-alt"></i>
                        </h1>
                        <div class="card-title">
                            <h5>{{ $module->title }}</h5>
                            @if($module->redotest == 1)
                                <span class="badge bg-danger text-white">RETAKE</span>
                                @if(!empty($module->expiry))
                                    <br>
                                    <small>
                                        <span class="badge bg-danger text-white">Expiry: {{ $module->expiry }}</span>
                                    </small>
                                @endif
                            @endif
                        </div>
                        <h6 class="text-primary">Type: {{ $module->type }}</h6>
                        <p class="text-primary">No. of Questions: {{ $module->questions->count() }}</p>
                        <p class="text-primary">Time: {{ $module->time }} minutes</p>
                    
                        <!-- Action Buttons -->
                        @if($module->completed == 0 ||$module->redo == 1)
                            <a href="{{ route('tests.show', ['test' => $module->id, 'p_id' => $program->id]) }}">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="return confirm('Have you read the instructions above?');">Start Now!</button>
                            </a>
                        @else
                            <a href="{{ route('tests.results', ['p_id' => $program->id]) }}">
                                <button type="button" class="btn btn-outline-success w-100">Test Completed! View Details</button>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
