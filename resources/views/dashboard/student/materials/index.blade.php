@extends('dashboard.student.trainingsindex')
@section('css')
{{-- <style>
  /* The Modal (background) */
  a.pre-order-btn { 
    color:#000;
    background-color:gold;
    border-radius:1em;
    padding:1em;
    display: block;
    margin: 2em auto;
    width:50%;
    font-size:1.25em;
    font-weight:6600;
    &:hover { 
    background-color:#000;
      text-decoration:none;
      color:gold;
    }
  }
  .Fixdiv{
        position:fixed;
        top:10px;
        left:10px;
    }
    
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black w/ opacity */
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  /* The Close Button */
  .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
</style> --}}
<style>
  /* The Modal (background) */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black w/ opacity */
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
  }

  /* The Close Button */
  .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
</style>
@endsection
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
                        <td>{{  $i++ }}</td>
                        <td>
                            <a data-toggle="tooltip" data-placement="top" title="Download Material"
                            class="btn btn-info" href="{{ route('getmaterial', ['p_id'=>$program->id, 'filename'=> $material->file])}}"><i
                                class="fa fa-download"> {{ $material->title }}</i>
                            </a>
                        </td>
                        <td>{{ $material->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                
            </table>
        </div>
    </div>
  </div>
</div>


@endsection