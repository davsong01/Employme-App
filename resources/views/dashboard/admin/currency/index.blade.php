@extends('dashboard.admin.index')
@section('title', 'Currencies')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Currencies 
                        <a href="{{route('currency.create')}}"><button type="button" class="btn btn-outline-primary">Add Payment currency</button></a>
                    </h5>
                </div>
            </div>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Country Name</th>
                            <th>Conversion Rate</th>
                            <th>Symbol</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $currency)
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $currency->name }}</td>
                            <td>{{ $currency->country_name }}</td>
                            <td>{{ $currency->conversion_rate }}</td>
                            <td>{{ $currency->symbol }}</td>
                            <td><span style="color:{{ $currency->status == 1 ? 'green' : 'red' }}">{{ $currency->status == 1 ? 'Active' : 'Inactive' }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a data-bs-toggle="tooltip" data-placement="top" title="Edit currency"
                                        class="btn btn-info" href="{{ route('currency.edit', $currency->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                    {{-- <form action="{{ route('currency.destroy', $currency->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-sm" data-bs-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                            @endforeach
                    </tbody>
                </table>
                <script type="text/javascript" src="{{ asset('src/jspdf.min.js')}} "></script>
                    
                    <script type="text/javascript" src="{{ asset('src/jspdf.plugin.autotable.min.js'
                    )}}"></script>
                    
                    <script type="text/javascript" src="{{ asset('src/tableHTMLExport.js')}}"></script>
                    
                    <script type="text/javascript">
                                           
                      $("#csv").on("click",function(){
                        $("#zero_config").tableHTMLExport({
                          type:'csv',
                          filename:'Participants.csv'
                        });
                      });
                    
                    </script>
            </div>

        </div>
    </div>
</div>
@endsection
