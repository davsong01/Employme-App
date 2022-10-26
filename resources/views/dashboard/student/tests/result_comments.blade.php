@extends('dashboard.student.trainingsindex')
@section('title', 'Result comments')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Result Comments</h2>
                        @if(isset($comments->facilitator_comment) && !empty($comments->facilitator_comment))
                        <h5>Facilitator's Comment</h5>
                       
                        <p>
                        {!! $comments->facilitator_comment !!}
                        </p>
                        <hr>
                        @endif
                        @if(isset($comments->grader_comment) && !empty($comments->grader_comment))

                        <h5>Grader's Comment</h5>
                        <p>
                             {!! $comments->grader_comment !!}
                        </p>
                       @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection