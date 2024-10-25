@extends('dashboard.admin.index')
@section('title', 'All Results')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                @include('layouts.partials.alerts')
                <h5>All Submitted Certification Test</h5>
            </div>
           
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Training</th>
                            <th>Certfication Score</th>
                            <th>Obtainable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @if($user->program->scoresettings)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->program->p_name }}</td>
                            <td>{{ $user->total_cert_score }}% </td>
                            <td>{{ $user->program->scoresettings->certification}}</td>
                        
                            <td>
                                <div class="btn-group">

                                    <a data-toggle="tooltip" data-placement="top" title="View/Mark"
                                        class="btn btn-info" href="{{ route('results.show', $user->id) }}"><i
                                            class="fa fa-eye"></i>
                                    </a>
                                    {{-- <a data-toggle="tooltip" data-placement="top" title="Edit Result"
                                    class="btn btn-warning" href="{{ route('results.edit', $user->id) }}"><i
                                        class="fa fa-edit"></i>
                                </a> --}}
                                    <form action="{{ route('results.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete Record"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                            @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection