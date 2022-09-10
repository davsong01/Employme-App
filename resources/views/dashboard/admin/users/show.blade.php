@extends('layouts.app')
@section('title', $customer->name )
@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Details for: {{ $customer->name }}</h1>
            <p><a href="/customers/{{ $customer->id }}/edit"> Edit</a></p>
            <!--Delete form-->
            <form action="/customers/{{ $customer->id}}" method = "POST">
                {{ csrf_field()}}
                {{ method_field("DELETE")}}
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
            <!--End of delete form-->
        </div>
    </div>
   <div class="row">
        <div class="col-12">
           <p> <strong>Name: </strong>{{$customer->name}}</p>
           <p> <strong>Email: </strong>{{$customer->email}}</p>
           <p> <strong>Company: </strong>{{$customer->company->name}}</p>
        </div>
    </div>
@endsection