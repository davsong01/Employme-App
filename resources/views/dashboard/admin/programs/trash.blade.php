@extends('dashboard.admin.index')
@section('title', 'Trainings')
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

            <h5 class="card-title"> All Trainings</h5>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Program Title</th>
                            <th>Training Fee</th>
                            <th>Early Bird Fee</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Partly Paid</th>
                            <th>Fully Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $program->p_name }}<br><span
                                    style="color:red">https://portal.employme.ng/paystack?id={{ $program->id }}&t=</span>
                            </td>
                            <td>{{\App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY'). $program->p_amount }}</td>
                            <td>{{ \App\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').$program->e_amount }}</td>
                            <td>{{ $program->p_start }}</td>
                            <td>{{ $program->p_end }}</td>
                            <td>{{ $program->part_paid }}</td>
                            <td>{{ $program->fully_paid }}</td>
                            <td>
                                <div class="btn-group">
                                    <form action="{{ route('programs.destroy', $program->id) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to delete forever?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Delete Training"> <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                        <a href="{{ route('programs.restore', $program->id) }}" type="submit" class="btn btn-success btn-sm" data-toggle="tooltip"
                                            data-placement="top" title="Restore Training"> <i class="fas fa-trash-restore"></i>
                                    </a>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>S/N</th>
                            <th>Program Title</th>
                            <th>Training Fee</th>
                            <th>Early Bird Fee</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Partly Paid</th>
                            <th>Fully Paid</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection