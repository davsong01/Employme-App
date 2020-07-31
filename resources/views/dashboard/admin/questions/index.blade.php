@extends('dashboard.admin.index')
@section('title')
{{ config('app.name') }} Test Management
@endsection
@section('content')

<div class="container-fluid">
     <div class="card">
        <div class="card-body">
           @include('layouts.partials.alerts')
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Questions @if(auth()->user()->role_id == "Admin") <a href="{{route('questions.create')}}"><button type="button" class="btn btn-outline-primary">Add New question</button></a>@endif</h5> 
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Title</th>                            
                            <th>Associated Module</th>
                            <th>Correct Option</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                        <tr>
                            <td>{{ $i++ }}</td>

                            <td>{{ $question->created_at->format('d/m/Y') }}</td>
                            <td>{{ $question->title }}</td>
                            <td>{{$question->module->title}}</td>
                            <td>{{ $question->correct }}</td>
                            @if(Auth::user()->role_id == "Admin")
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit question"
                                        class="btn btn-info" href="{{ route('questions.edit', $question->id)}}"><i
                                            class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('questions.destroy', $question->id) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to Delete forever?');">
                                        
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-warning" data-toggle="tooltip"
                                            data-placement="top" title="Delete Questions"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                            @endif
                            @if(Auth::user()->role_id == "Facilitator")
                            <td>
                                N/A
                            </td>
                            @endif
                        </tr> 
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
<script>
    $(".delete").on("submit", function () {
        return confirm("Are you sure?");
    });
</script>
@endsection