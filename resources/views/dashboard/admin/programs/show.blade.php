@extends('layouts.app')
@section('title', $program->name )
@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Details for: {{ $program->name }}</h1>
            <p><a href="/programs/{{ $program->id }}/edit"> Edit</a></p>
            <!--Delete form-->
            <form action="/programs/{{ $program->id}}" method = "POST">
                {{ csrf_field()}}
                {{ method_field("DELETE")}}
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <!--End of delete form-->
        </div>
    </div>
   <div class="row">
        <div class="col-12">
           <p> <strong>Name: </strong>{{$program->name}}</p>
        
          
        </div>
    </div>
@endsection