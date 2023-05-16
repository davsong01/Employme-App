@extends('dashboard.admin.index')
@section('title', 'Payment methods')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Payment method 
                        <a href="{{route('paymentmethod.create')}}"><button type="button" class="btn btn-outline-primary">Add Payment Method</button></a>
                    </h5>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($methods as $method)
                        
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td></td>
                            <td>{{ $method->name }}</td>
                            <td>{{ $method->type }}</td>
                            <td> <img src="/{{ $method->image }}" alt="image" class="rounded-circle" width="50" height="50"> </td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit payment method"
                                        class="btn btn-info" href="{{ route('paymentmethod.edit', $method->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                  
                                    <form action="{{ route('paymentmethod.destroy', $method->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete payment method"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                            @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection