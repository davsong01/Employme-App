@extends('dashboard.admin.index')
@section('title', 'Payment modes')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-title">
                @include('layouts.partials.alerts')
            </div>
            <div class="card-header">
                <div>
                    <h5 class="card-title"> Payment modes 
                        <a href="{{route('payment-modes.create')}}"><button type="button" class="btn btn-outline-primary">Add Payment mode</button></a>
                    </h5>
                </div>
            </div>
            <?php $default_currency = \App\Settings::value('CURR_ABBREVIATION') ?>
            <div class="">
                <table id="zero_config" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Processor</th>
                            <th>Slug</th>
                            <th>Keys</th>
                            <th>Currency</th>
                            <th>Symbol</th>
                            <th>Exchange rate</th>
                            <th>Merchant Email</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modes as $mode)
                        
                        <tr>
                            <td>{{  $i++ }}</td>
                            <td>{{ $mode->processor }}</td>
                            <td>{{ $mode->slug }}</td>
                            <td><strong style="color:green">Public: </strong>{{ $mode->public_key}} <br>
                                <strong style="color:green">Secret: </strong>{{ $mode->secret_key }}
                            </td>
                            <td>{{ $mode->currency }}</td>
                            <td>{{ $mode->currency_symbol }}</td>
                            <td> 1 {{ $default_currency }} = {{ $mode->exchange_rate . ' '. $mode->currency }} </td>
                            <td>{{ $mode->merchant_email}}</td>
                            <td>
                                <div class="btn-group">
                                    <a data-toggle="tooltip" data-placement="top" title="Edit payment mode"
                                        class="btn btn-info" href="{{ route('payment-modes.edit', $mode->id) }}"><i
                                            class="fa fa-edit"></i>
                                    </a>
                                  
                                    <form action="{{ route('payment-modes.destroy', $mode->id) }}" method="POST"
                                        onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete user"> <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
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