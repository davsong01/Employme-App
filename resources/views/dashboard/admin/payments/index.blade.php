@extends('dashboard.admin.index')
@section('title', 'Payment History')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @include('layouts.partials.alerts')
            <h5 class="card-title">Payment History</h5>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice ID</th>
                            <th>Transaction ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Amount Paid</th>
                            <th>Balance</th> 
                            <th>Bank</th>  
                            <th>Manage</th>       
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->invoice_id }}</td>
                            <td>{{ $user->transid }}</td>
                            <td>{{ $user->details->name }}</td>
                            <td>{{ $user->details->email }}</td>
                            <td>{{ $user->program->p_name }}</td>
                            <td>{{ config('custom.default_currency'). $user->t_amount }}</td>
                            @if($user->paymentStatus == 0 )
                                <td><b style="color:red">{{  config('custom.default_currency'). $user->balance }} </b></td> 
                            @else
                                <td><b style="color:green">{{ config('custom.default_currency').  $user->balance }}</b></td> 
                            @endif
                            <td>{{ $user->t_type }}</td>
                             <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit Transaction"
                                        class="btn btn-info" href="{{ route('payments.edit', $user->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    {{-- <a data-toggle="tooltip" data-placement="top" title="Impersonate User"
                                        class="btn btn-warning" href="{{ route('impersonate', $user->id) }}"><i
                                            class="fa fa-unlock"></i>
                                    </a> --}}
                                    <a data-toggle="tooltip" data-placement="top" title="Send E-receipt"
                                        class="btn btn-primary" href="{{ route('users.show', $user->id) }}"><i
                                            class="far fa-envelope"></i>
                                    </a>
                                    <form action="{{ route('payments.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

@endsection