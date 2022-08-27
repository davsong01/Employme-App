@extends('dashboard.admin.index')
@section('title', 'Download materials')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-title">
            @include('layouts.partials.alerts')
         </div>
        <div class="card-header">
            <div>
                <h5 class="card-title"> All Materials <a href="{{route('materials.create')}}"><button type="button" class="btn btn-outline-primary">Add New study Material</button></a></h5> 
            </div>
        </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $material->title }}</td>
                            <td>{{ $material->created_at->format('d/m/Y') }}</td>
                            <td>{{ isset($material->program->p_name) ? $material->program->p_name : 'Training has been trashed'}}</td>
                            <td>
                                <div class="btn-group">
                                    @if(isset($material->program->id))
                                    <a data-toggle="tooltip" data-placement="top" title="Download Material"
                                        class="btn btn-info" href="{{ route('getmaterial', ['p_id'=>$material->program->id, 'filename'=> $material->file])}}"><i
                                            class="fa fa-download"></i>
                                    </a>
                                    
                                    <a data-toggle="tooltip" data-placement="top" title="Clone Material"
                                    class="btn btn-primary" onclick="return confirm('Are you really sure?');" href="{{ route('materials.show', $material->id) }}"><i
                                        class="fa fa-clone"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete material"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                            <th>Program/Class</th>
                            <th>Actions</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection