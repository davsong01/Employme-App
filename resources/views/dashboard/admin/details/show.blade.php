@extends('dashboard.admin.index')
@section('title', 'Trainings')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @if(session()->get('message'))
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> {{ session()->get('message')}}
            </div>
            @endif
            <h5 class="card-title"> Details</h5>
            <div class="card">
                <div class="card-body">
                        @if($count >= 1)
                            @foreach($results as $result)
                            {{$result->$type}}<br>
                            @endforeach  
                            @elseif($count < 1)
                            No results found for this program, please try again!
                            @endif
                         </div>
            </div>
        </div>

    </div>
</div>
</div>


@endsection

