{{-- <!DOCTYPE html>
<html>
<head>
  <title></title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
</head>
<body> --}}
@extends('dashboard.admin.index')
@section('content')
<table class="table table-bordered" id="example">

<thead>
  <th>#</th>
  <th>Firstname</th>
  <th>Lastname</th>
</thead>

<tbody>
  <tr>
    <td>1</td>
    <td>Saheedb</td>
    <td>Babatunde</td>
  </tr>
  <tr>
    <td>2</td>
    <td>Saheedb</td>
    <td>Babatunde</td>
  </tr>

  <tr>
    <td>3</td>
    <td>Saheedb</td>
    <td>Babatunde</td>
  </tr>
</tbody>

</table>

<center>
<button class="btn btn-success" id="json">JSON</button>

<button class="btn btn-success" id="pdf">PDF</button>

<button class="btn btn-success" id="csv">CSV</button>

</center>

{{-- <script type="text/javascript" src="{{ asset('src/jquery-3.3.1.slim.min.js') }}"></script> --}}

<script type="text/javascript" src="{{ asset('src/jspdf.min.js')}} "></script>

<script type="text/javascript" src="{{ asset('src/jspdf.plugin.autotable.min.js'
)}}"></script>

<script type="text/javascript" src="{{ asset('src/tableHTMLExport.js')}}"></script>

<script type="text/javascript">
  
  $("#json").on("click",function(){
    $("#example").tableHTMLExport({
      type:'json',
      filename:'sample.json'
    });
  });

  $("#pdf").on("click",function(){
    $("#example").tableHTMLExport({
      type:'pdf',
      filename:'sample.pdf'
    });
  });

  $("#csv").on("click",function(){
    $("#example").tableHTMLExport({
      type:'csv',
      filename:'sample.csv'
    });
  });

</script>

@endsection