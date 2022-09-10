@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .'Questions' }}
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Select a Training to manage its questions. Trainings which have no questions will not appear here </h5> 
                </div>
            </div>
            <div class="table-responsive">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Training</th>
                            <th>Questions</th>                           
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($programs_with_questions as $programs)
                         @if($programs->questions_count >0)
                            <tr>
                                <td>{{  $i++ }}</td>
                                <td><a data-toggle="tooltip" data-placement="top" title="Click to view questions for this training"
                                    class="btn btn-info" href="{{ route( 'questions.show', $programs->id ) }}">
                                    {{ $programs->p_name }}
                                </a>
                                </td>
                                <td>{{ $programs->questions_count}}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection