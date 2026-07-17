@php
    $check = [
        'coupon.create',
        'coupon.edit',
        'coupon.destroy',
        'coupon.show'
    ];

    $permissions = canUserAccessPermission($check);
@endphp

@extends('dashboard.admin.index')
@section('title', 'All Coupons')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
        </div>
        @if($permissions['coupon.create'])
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Coupons <a href="{{route('coupon.create')}}"><button type="button" class="btn btn-outline-primary">Add New Coupon</button></a></h5> 
            </div>
        </div>
        @endif
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Amount</th>
                            <th>Training</th>
                            <th>Created by</th>
                            <th>Usage count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $coupon->code }}</td>
                            <td>
                                {{ $coupon->type === 'fixed' 
                                    ? currency() . number_format($coupon->amount) 
                                    : number_format($coupon->amount) . '%' }}
                            </td>
                            
                            <td>{{isset($coupon->program_id) ? $coupon->program->p_name  : ($coupon->group->p_name ?? 'NOT SET') }} (<span class="">{{ isset($coupon->program_id) ? "Training"  : "Package" }}</span>)</td>                          
                            <td>{{ isset($coupon->facilitator->name) ? $coupon->facilitator->name : 'Administrator' }}</td>                          
                            <td>{{ $coupon->coupon_users->count() }}</td>                          
                            <td>
                                <div class="btn-group">
                                    @if($permissions['coupon.edit'])
                                    <a data-bs-toggle="tooltip" data-placement="top" title="edit coupon details"
                                        class="btn btn-info" href="{{ route('coupon.edit', $coupon->id)}}"><i class="fa fa-edit"></i>
                                    </a>
                                    @endif
                                    @if($permissions['coupon.destroy'])
                                        <form action="{{ route('coupon.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                                data-placement="top" title="Delete coupon"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($permissions['coupon.show'])
                                    <a data-bs-toggle="tooltip" data-placement="top" title="View coupon usage"
                                        class="btn btn-primary" href="{{ route('coupon.show', $coupon->id)}}"><i class="fa fa-eye"></i>
                                    </a>
                                    @endif
                                </div>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

@endsection
