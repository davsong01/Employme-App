@extends('dashboard.student.index')
@section('title', 'Download Certificate')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                        
                            <th>Name</th>
                            <th>Program</th>
                            <th>Action</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates as $certificate)
                        <div class="text-center">
                            <h5 class="card-title">Congratulations!!! You made it at last. Please Download your Certificate below</h5>
                        </div>
                        <tr>
                            <td>{{ $certificate->user->name }}</td>
                            <td>{{ $certificate->user->program->p_name }}</td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                class="btn btn-info" href="certificate/{{ $certificate->file }}"><i
                                    class="fa fa-download"> Download</i></a>
                            </td>
                        </tr>
                        @empty
                        <div class="text-center"> <p>Sorry, your certificate is not available right now, Please check back soon</p></div>
                        @endforelse
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