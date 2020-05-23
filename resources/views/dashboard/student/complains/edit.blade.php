@extends('dashboard.layouts.main')
@section('title', $customer->name )
@section('content')
    <div class="row">
        <div class="col-12">
            <h1>Edit details for: {{$customer->name}}</h1>
        </div>
    </div>
   <div class="row">
        <div class="col-12">
        <form action="{{ route('customers.update', ['customer' => $customer]) }}" method="POST" class="pb-2">
                {{ method_field('PATCH') }}
                @include('dashboard.admin.materials.form')
                <div><button type="submit" class="btn btn-primary">Update Record </button></div>
            </form> 
        </div>
    </div>
@endsection