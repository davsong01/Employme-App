@extends('dashboard.admin.index')
@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<style>
    .select2-container--default .select2-selection--multiple {
        width: 100% !important; /* Force full width */
    }

    .select2-container {
        width: 100% !important; /* Force full width */
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        color: black; /* Text color for selected items */
    }
§
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        color: black; /* Text color for the rendered selections */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: black; /* Text color for the single selected item */
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: black; /* Text color for the placeholder */
    }

    .select2-container--default .select2-results__option {
        color: black; /* Text color for the dropdown options */
    }
    

    .badge {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 45px;
        height: 45px;
        background-color: #4CAF50;
        border-radius: 50%;
        color: white;
        font-size: 10px;
        font-weight: bold;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .transaction-count {
        text-align: center;
    }
    .search-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-control {
        border-radius: 20px;
    }

    .rounded {
        border-radius: 20px !important;
    }
    .btn-search {
        border-radius: 20px;
        transition: background-color 0.3s;
    }
    .btn-search:hover {
        background-color: #0056b3;
    }

    .btn.active {
        background-color: #0056b3;
        color: white;
        border: 4px solid black;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        transform: scale(1.05); 
        transition: all 0.3s;
    }

    .btn:not(.active):hover {
        transform: scale(1.05); 
    }

    .button-container .btn {
        border-radius: 8px;
        font-weight: 500;
        text-align: center;
        transition: all 0.3s ease; 
    }

    .button-container .btn:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .button-container .btn:disabled {
        opacity: 0.6;
    }

    .button-container .fa-unlock {
        margin-right: 0.25rem; 
    }


    

</style>
@endsection
@section('title', 'All Logs')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div lass="card-title">
                @include('layouts.partials.alerts')
                <div class="card-header">
                    <div>
                        <h5 class="card-title">All Logs</h5>
                        </h5>
                        <br>
                        
                        <div class="card-body">
                            <button class="btn btn-danger float-right rounded" id="csv">Truncate Logs</button>
                            
                        </div>
                        
                        <div class="mt-4">
                            <form class="row" method="GET" action="">
                                <div class="col-md-4 mb-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="certificate_number" id="certificate_number" placeholder="EnterCertificate Number" value="{{ request('certificate_number') }}">
                                    </div>
                                </div>

                                <div class="col-md-4 mb-2">
                                    <button type="submit" class="btn btn-primary btn-search w-100">Filter</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Certificate Number</th>
                            <th>Date Searched</th>
                            <th>Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            
                            <td>{{ $i++ }}</td>
                            <td>{{ $log->certificate_number}}</td>
                            <td> {{ $log->created_at->format('d/m/Y') }}</td>
                            <td>
                                @foreach($log->response as $key=>$value)
                                    <strong>{{$key}}:</strong>{{ $value }} <br>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $logs->links() }}
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            allowClear: true
        });
    });
</script>
@endsection