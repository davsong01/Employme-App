@extends('dashboard.student.index')
@section('title', 'Payment History')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @if(session()->get('message'))
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> {{ session()->get('message')}}
            </div>
            @endif
            <h5 class="card-title">Payment History</h5>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoice ID</th>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>           
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>Invoice ID</th>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Training</th>
                            <th>Amount</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->updated_at }}</td>
                            <td>{{ $user->invoice_id }}</td>
                            <td>{{ $user->transid }}</td>
                            <td>{{ $user->t_type }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->program->p_name }}</td>
                            <td>₦{{ $user->program->p_amount }}</td>
                            <td>₦{{ $user->t_amount }}</td>
                            @if($user->paymentStatus == 0 )
                                <td><b style="color:red">₦{{ $user->balance }} </b></td> 
                            @else
                                <td><b style="color:green">₦{{ $user->balance }}</b></td> 
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
@endsection