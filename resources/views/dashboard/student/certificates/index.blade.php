@extends('dashboard.student.trainingsindex')
@section('title', 'Download Certificate')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="">
                <table id="" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Training</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <div class="text-center">
                            <h5 class="card-title">Please Download your certificate below</h5>
                        </div>
                        <tr>
                            <td>{{ $certificate->user->name }} <br>
                            </td>
                            <td>{{ $certificate->program->p_name }}</td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-info" href="/certificate/{{ $certificate->file }}"><i
                                    class="fa fa-download"> Download Certificate</i></a>
                            </td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    // $('#zero_config').DataTable();
</script>
@endsection