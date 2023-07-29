@extends('dashboard.admin.index')

@section('title', 'Add Certificate')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Certificate in {{$p_name}}</h4>
                    </div>
                    <form action="{{ route('certificates.save') }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ csrf_field() }}
                        <!--Gives the first error for input name-->

                        <div><small>{{ $errors->first('title')}}</small></div>
                        <div class="form-group">

                            <label for="class">Select User *</label>

                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value=""></option>
                                @foreach ($users->sortBy('name') as $user)
                                    @if($user->certificates_count <= 0)
                                        <option value="{{ $user->user_id }}">{{$user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div><small style="color:red">{{ $errors->first('user_id')}}</small></div>

                            <div class="form-group">
                                <label>Choose Certificate</label>
                                <input type="file" id="certificate" name="certificate" class="form-control" required>
                            </div>
                            <div><small style="color:red">{{ $errors->first('certificate')}}</small></div>
                        </div>
                        <input type="hidden" value="{{ $p_id }}" name="p_id">
                       
                        <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

      <div class="card">
        <div class="col-md-12">
            
            <div class="card-body">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Access</th>
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
                            <td style="color:{{ $certificate->show_certificate() == 'Disabled' ? 'red' : 'green'}}">{{ $certificate->show_certificate() }}</td>
                            <td>{{ $certificate->created_at->format('d/m/Y') }}</td>
                            <td>{{ isset($certificate->program) ? $certificate->program->p_name: "Program has been trashed" }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($certificate->show_certificate() == 'Disabled')
                                    <a data-toggle="tooltip" data-placement="top" title="Enable certificate"
                                        class="btn btn-light" href="{{route('certificate.status', ['program_id'=>$certificate->program_id, 'user_id'=> $certificate->user_id, 'status'=>1, 'certificate_id' => $certificate->id]) }}"><i
                                            class="fa fa-toggle-on"></i>
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Disable certificate"
                                        class="btn btn-light" href="{{route('certificate.status', ['program_id'=>$certificate->program_id, 'user_id'=> $certificate->user_id, 'status'=>0, 'certificate_id' => $certificate->id ]) }}"><i
                                            class="fa fa-toggle-off"></i>
                                    </a>
                                    @endif
                                    <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                        class="btn btn-info" href="/certificate/{{ $certificate->file }}"><i
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
    <script>
        $(document).ready(function() {
            $('#user_id').select2();
        });
    </script>
    @endsection