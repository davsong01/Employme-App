@extends('dashboard.admin.index')
@section('title', 'Coupon Usage')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
         </div>
        <div class="card-header">
            <div>
                <h5 class="card-title">Coupon: <span style="color:blue">{{ $coupon->code}}</span></h5> 
            </div>
        </div>
            <div class="" style="padding: 10px;">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Training</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usages as $usage)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>@if($usage->status == 1)
                                <button class="btn btn-success btn-xs">Used</button>
                                @else
                                <button class="btn btn-warning btn-xs">Initiated</button>
                                @endif
                            <td>{{ $usage->created_at }}</td>
                            <td>{{ $usage->user()->name ?? null }}</td>
                            <td>{{ $usage->program->p_name  ?? NULL}}</td>                          
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

@endsection