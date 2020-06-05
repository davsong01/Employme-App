@extends('dashboard.student.index')
@section('title', 'Download Study materials')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Sttudy Materials</h5>
            <div class="table-responsive">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Action</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $material->title }}</td>
                            <td>{{ $material->created_at->format('d/m/Y') }}</td>
                            <td>{{ $material->program->p_name }}</td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download Material"
                                class="btn btn-info" href="studymaterials/{{ $material->file }}"><i
                                    class="fa fa-download"> Download</i>
                            </a>
            
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    $('#zero_config').DataTable();
</script>
@endsection