<?php 
    $menus = Auth::user()->menu_permissions ?? [];
    
    if($menus){
        $menus = explode(',',$menus);
    }else{
        $menus = [];
    }
   
?>
@extends('dashboard.admin.index')
@section('title')
    {{ config('app.name') .' Test Management' }}
@endsection
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <h2 style="color:#008000; text-align:center; padding:20px">{{ strtoupper($program_name->p_name) }} <br> MODULE MANAGEMENT</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
        <a href="{{ route('modules.index')}}">
            <div class="card card-hover">
                <div class="box bg-info text-center">
                    <h1 class="font-light text-white"><i class=" fa fa-list-alt"></i></h1>
                    <h6 class="text-white"><b></b> {{$modules->count()}} Module(s)</h6>
                </div>
            </div>
        </a>
        </div>
        <!-- Column -->
        <div class="col-md-4 col-lg-4">
        <a href="{{ route('questions.index')}}">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-check"></i></h1>
                <h6 class="text-white"><b></b> {{ $questions_count }} Questions</h6>
                </div>
            </div>
        </a>
        </div>
        
        <div class="col-md-4 col-lg-4">
        <a href="{{ route('scoreSettings.index')}}">
            <div class="card card-hover">
                <div class="box bg-success text-center">
                    <h1 class="font-light text-white"><i class="fa fa-cog"></i></h1>
                <h6 class="text-white"><b></b> Score Settings </h6>
                </div>
            </div>
        </a>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
             </div>
             
            <div class="card-header">
                <div>
                    <h5 class="card-title"> All Modules @if(auth()->user()->role_id == "Admin" || in_array(21, $menus))<a href="{{route('modules.create', ['p_id' => $program_name->id] )}}"><button type="button" class="btn btn-outline-primary">Add New Module </button></a>@endif </h5> 
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Title</th>                            
                            <th>Associated Training</th>
                            <th>Expected Questions</th>
                            <th>Set Questions</th>
                            <th>Question Time</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                        @foreach($modules as $module)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $module->created_at->format('d/m/Y') }}</td>
                            <td>{{ $module->title }}<br><span style="color: red">{{$module->type }}</span></td>
                            <td>{{$module->program->p_name}}</td>
                            <td>{{ $module->noofquestions }}</td>
                            <td>{{ $module->questions->count() }}</td>
                            <td>{{ $module->time}} minutes</td>
                            <td>{{ $module->type }}</td>
                            <td>{{$module->status == 0 ? 'Disabled' : 'Enabled' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-info btn-sm" href="{{ route('modules.edit', $module->id)}}" onclick="return confirm('Are you really sure?');"><i
                                            class="fa fa-edit"></i> Edit
                                    </a>
                                    @if($module->status == 0)
                                    <a class="btn btn-primary btn-sm" href="{{ route('modules.enable', $module->id)}}" onclick="return confirm('Are you really sure?');"><i
                                            class="fa fa-check"></i> Enable
                                    </a>
                                    @else
                                    <a class="btn btn-info btn-sm" href="{{ route('modules.disable', $module->id)}}" ><i
                                           onclick="return confirm('Are you really sure?');" class="fa fa-ban"></i> Disable
                                    </a>
                                    @endif
                                    <form action="{{ route('modules.destroy', $module->id) }}" method="POST"
                                        onsubmit="return confirm('Do you really want to Delete?');">
                                        
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}
                                    </form>
                                </div>

                            </td>
                        </tr> 
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

</div>

@endsection