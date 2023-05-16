@extends('dashboard.admin.index')
@section('title', 'Download certificates')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
         </div>
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Certificates <a href="{{route('certificates.create')}}"><button type="button" class="btn btn-outline-primary">Add New Certificate</button></a></h5> 
            </div>
        </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Program</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ isset($certificate->user->name) ? $certificate->user->name : 'N/A' }}</td>
                            <td>{{ $certificate->created_at->format('d/m/Y') }}</td>
                            <td>{{ isset($certificate->program) ? $certificate->program->p_name: "Program has been trashed" }}</td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                        class="btn btn-info" href="certificate/{{ $certificate->file }}"><i
                                            class="fa fa-download"></i>
                                    </a>
                                        <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                            {{ csrf_field() }}
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                                data-placement="top" title="Delete certificate"> <i
                                                    class="fa fa-trash"></i>
                                            </button>
                                        </form>
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