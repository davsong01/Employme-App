@extends('dashboard.student.trainingsindex')
@section('title', 'Download Study materials')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                <h2 style="color:green; text-align:center; padding:20px">{{ strtoupper($program->p_name) }} STUDY MATERIALS</h2>
                <h5>Study Materials</h5>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Date Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                        <tr>
                            <td>{{  $loop->iteration }}</td>
                            <td>
                                <a data-toggle="tooltip" data-placement="top" title="Download Material"
                                class="btn btn-info" href="{{ route('participants.getmaterial', ['p_id'=>$program->id, 'filename'=> $material->file])}}"><i
                                    class="fa fa-download"> {{ $material->title }}</i>
                                </a>
                            </td>
                            <td>{{ $material->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
               
                @if($useAi && $materials->count() > 0)
                    @php
                        $useCase = 'materials';
                    @endphp
                    @include('layouts.partials.portal-ai-chat')
                @endif
            </div>
        </div>
    </div>
</div>
@endsection